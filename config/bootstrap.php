<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/src/helpers.php';
require_once BASE_PATH . '/src/Auth.php';
require_once BASE_PATH . '/src/FavoriteService.php';
require_once BASE_PATH . '/src/DigimonApi.php';
