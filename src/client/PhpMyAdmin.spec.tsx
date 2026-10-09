import { afterEach, expect, test, vi } from 'vitest';
import { cleanup, render, screen } from '@testing-library/react';
import { createExtensionTestHost, createTestServer } from '@pterodactyl/sdk/testing';
import { setConfig } from './config';
import PhpMyAdmin from './PhpMyAdmin';

vi.mock('./api', async (importOriginal) => ({
    ...(await importOriginal<typeof import('./api')>()),
    getDatabases: vi.fn().mockResolvedValue([{ id: 'abc123', name: 's1_game', username: 'u1_xyz', host: { address: 'db.example.com', port: 3306 } }]),
}));

let host: ReturnType<typeof createExtensionTestHost> | undefined;

afterEach(() => {
    cleanup();
    host?.dispose();
    host = undefined;
});

function renderBox(permissions: string[], url: string) {
    setConfig({ url });
    host = createExtensionTestHost({ extensionId: 'phpmyadmin', prefix: 'pma', server: createTestServer({ owner: false, permissions }) });
    render(<PhpMyAdmin />, { wrapper: host.Wrapper });
}

test('lists databases for users who may see their passwords', async () => {
    renderBox(['database.read', 'database.view_password'], 'https://pma.example.com/signon.php');

    expect(await screen.findByText('s1_game')).toBeTruthy();
    expect(screen.getByRole('button', { name: 'Open in phpMyAdmin' })).toBeTruthy();
});

test('stays hidden without the password permission or a configured URL', async () => {
    renderBox(['database.read'], 'https://pma.example.com/signon.php');
    await new Promise((resolve) => setTimeout(resolve, 50));
    expect(screen.queryByText('s1_game')).toBeNull();

    cleanup();
    host?.dispose();
    renderBox(['database.read', 'database.view_password'], '');
    await new Promise((resolve) => setTimeout(resolve, 50));
    expect(screen.queryByText('s1_game')).toBeNull();
});
