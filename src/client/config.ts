import type { ExtensionConfig } from '@pterodactyl/sdk';

let configured = false;

export function setConfig(config: ExtensionConfig): void {
    configured = typeof config.url === 'string' && config.url !== '';
}

export const isConfigured = () => configured;
