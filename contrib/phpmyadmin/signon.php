<?php

/*
 * Single sign-on for the Pterodactyl phpMyAdmin extension.
 * Put this file next to phpMyAdmin's index.php and fill in the three values below.
 */

declare(strict_types=1);

$panel = 'https://panel.example.com';   // Your Pterodactyl panel, without a trailing slash.
$secret = 'paste-the-shared-secret';     // The same value as "Shared secret" in the extension settings.
$server = 1;                              // The phpMyAdmin server number that uses auth_type "signon".

$token = $_GET['token'] ?? '';

if (! is_string($token) || preg_match('/^[A-Za-z0-9]{48}$/', $token) !== 1) {
    http_response_code(400);
    exit('This login link is not valid.');
}

$login = json_decode(fetch("{$panel}/extensions/phpmyadmin/verify/{$token}", $secret), true);

if (! is_array($login) || ! isset($login['username'], $login['password'], $login['host'], $login['database'])) {
    http_response_code(403);
    exit('This login link has expired. Open phpMyAdmin from the panel again.');
}

session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => ! empty($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax']);
session_name('SignonSession');
session_start();
$_SESSION['PMA_single_signon_user'] = $login['username'];
$_SESSION['PMA_single_signon_password'] = $login['password'];
$_SESSION['PMA_single_signon_host'] = $login['host'];
$_SESSION['PMA_single_signon_port'] = (string) $login['port'];
$_SESSION['PMA_single_signon_cfgupdate'] = ['only_db' => $login['database']];
session_write_close();

header('Location: index.php?server='.$server.'&db='.rawurlencode($login['database']));

function fetch(string $url, string $secret): string
{
    $headers = ["X-Pma-Secret: {$secret}", 'Accept: application/json', 'User-Agent: phpMyAdmin signon'];

    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_HTTPHEADER => $headers, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
        $body = curl_exec($curl);

        return is_string($body) ? $body : '';
    }

    $context = stream_context_create(['http' => ['header' => implode("\r\n", $headers)."\r\n", 'timeout' => 5, 'ignore_errors' => true]]);

    return (string) @file_get_contents($url, false, $context);
}
