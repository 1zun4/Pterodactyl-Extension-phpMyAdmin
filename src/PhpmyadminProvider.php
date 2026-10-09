<?php

declare(strict_types=1);

namespace Phpmyadmin;

use Pterodactyl\Extensions\ExtensionProvider;
use Pterodactyl\Services\Extensions\ExtensionSettingDefinition;
use Pterodactyl\Services\Extensions\ExtensionSettingsDefinition;

class PhpmyadminProvider extends ExtensionProvider
{
    public function boot(): void
    {
        $this->registerApiRoutes();
        $this->registerWebRoutes($this->extensionPath('routes', 'web.php'));

        $this->registerSettings(new ExtensionSettingsDefinition($this->settings(), [
            ExtensionSettingDefinition::make('url', 'url', '', ['nullable', 'url', 'max:255'])
                ->label('signon.php URL')
                ->help('Full address of the signon.php you placed in phpMyAdmin, such as https://pma.example.com/signon.php.')
                ->frontend()
                ->frontendType('string'),
            ExtensionSettingDefinition::make('secret', 'secret', '', ['nullable', 'string', 'min:24', 'max:255'])
                ->label('Shared secret')
                ->help('The same value as $secret in signon.php. At least 24 characters.')
                ->secret(),
            ExtensionSettingDefinition::make('host', 'host', '', ['nullable', 'string', 'max:255'])
                ->label('Database host for phpMyAdmin')
                ->help('Address phpMyAdmin uses to reach the database host. Leave empty to use the address from the database host settings.'),
            ExtensionSettingDefinition::make('allowed_ips', 'allowed_ips', [], [])
                ->label('phpMyAdmin IP addresses')
                ->help('Only these addresses may redeem login links. Leave empty to allow any address that knows the secret.')
                ->list(['ip'], 10),
        ]));
    }
}
