<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secureCookie = envValue('SESSION_SECURE_COOKIE', '0') === '1';
    $sameSite = envValue('SESSION_SAMESITE', 'Lax') ?? 'Lax';

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');

    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => (int) $cookieParams['lifetime'],
        'path' => (string) $cookieParams['path'],
        'domain' => (string) $cookieParams['domain'],
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => $sameSite,
    ]);

    session_start();
}

require_once BASE_PATH . '/src/helpers.php';
require_once BASE_PATH . '/src/Logger.php';
require_once BASE_PATH . '/src/Auth.php';
require_once BASE_PATH . '/src/FavoriteService.php';
require_once BASE_PATH . '/src/DigimonApi.php';
require_once BASE_PATH . '/src/DigimonMapper.php';
