<?php

declare(strict_types=1);

namespace Phpmyadmin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Pterodactyl\Services\Extensions\ExtensionSettingsRegistry;

/**
 * Called by signon.php on the phpMyAdmin server to swap a login link for the database credentials, once.
 */
final class VerifyController
{
    public function __construct(private readonly ExtensionSettingsRegistry $settings) {}

    public function __invoke(Request $request, string $token): JsonResponse
    {
        $settings = $this->settings->get('phpmyadmin');
        $secret = (string) ($settings?->get('secret') ?? '');
        $allowed = (array) ($settings?->get('allowed_ips') ?? []);

        abort_if($secret === '' || ! hash_equals($secret, (string) $request->header('X-Pma-Secret')), 403);
        abort_if($allowed !== [] && ! in_array($request->ip(), $allowed, true), 403);

        $payload = preg_match('/^[A-Za-z0-9]{48}$/', $token) === 1 ? Cache::pull(LinkController::key($token)) : null;
        abort_if(! is_string($payload), 404);

        return new JsonResponse(Crypt::decrypt($payload));
    }
}
