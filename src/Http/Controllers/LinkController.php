<?php

declare(strict_types=1);

namespace Phpmyadmin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Pterodactyl\Models\Database;
use Pterodactyl\Models\Server;
use Pterodactyl\Services\Extensions\ExtensionSettingsRegistry;

final class LinkController
{
    public const int TTL_SECONDS = 60;

    public function __construct(private readonly ExtensionSettingsRegistry $settings) {}

    public static function key(string $token): string
    {
        return 'ext:phpmyadmin:token:'.hash('sha256', $token);
    }

    /** Creates a single-use phpMyAdmin login link for one of the server's databases. */
    public function store(Request $request, Server $server, string $id): JsonResponse
    {
        abort_unless((bool) $request->user()?->can('database.view_password', $server), 403, 'You do not have permission to see this database\'s password.');

        $settings = $this->settings->get('phpmyadmin');
        $url = (string) ($settings?->get('url') ?? '');
        abort_if($url === '' || (string) ($settings?->get('secret') ?? '') === '', 422, 'phpMyAdmin is not set up yet.');

        $model = (new Database())->resolveRouteBinding($id);
        abort_unless($model instanceof Database && $model->server_id === $server->id, 404);

        $host = $model->host;
        $token = Str::random(48);

        Cache::put(self::key($token), Crypt::encrypt([
            'host' => (string) (($settings->get('host') ?: null) ?? $host->host),
            'port' => (int) $host->port,
            'username' => $model->username,
            'password' => Crypt::decrypt($model->password),
            'database' => $model->database,
        ]), self::TTL_SECONDS);

        return new JsonResponse(['url' => $url.(str_contains($url, '?') ? '&' : '?').'token='.$token]);
    }
}
