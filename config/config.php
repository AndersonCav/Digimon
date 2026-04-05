<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/src/Logger.php';

$loadedEnv = loadEnvFile(BASE_PATH . '/.env');

if (!function_exists('envValue')) {
    function envValue(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;

        if (!is_string($value) || $value === '') {
            return $default;
        }

        return $value;
    }
}

$dbConfig = [
    'host' => envValue('DB_HOST', '127.0.0.1'),
    'port' => (int) envValue('DB_PORT', '3306'),
    'name' => envValue('DB_NAME', 'digimon'),
    'user' => envValue('DB_USER', 'root'),
    'password' => envValue('DB_PASSWORD', ''),
    'charset' => envValue('DB_CHARSET', 'utf8mb4'),
];

$apiConfig = [
    'base_url' => envValue('DIGIMON_API_URL', 'https://digi-api.com/api/v1/digimon'),
    'cache_enabled' => envValue('CACHE_ENABLED', '1') === '1',
    'cache_ttl_seconds' => (int) envValue('CACHE_TTL_SECONDS', '300'),
    'cache_dir' => BASE_PATH . '/storage/cache',
];

if (!defined('APP_LOG_ENABLED')) {
    define('APP_LOG_ENABLED', envValue('LOG_ENABLED', '1') === '1');
}

if (!defined('APP_LOG_FILE')) {
    define('APP_LOG_FILE', BASE_PATH . '/storage/logs/app.log');
}

$conn = null;
$dbError = null;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(
        $dbConfig['host'],
        $dbConfig['user'],
        $dbConfig['password'],
        $dbConfig['name'],
        $dbConfig['port']
    );
    $conn->set_charset($dbConfig['charset']);
} catch (mysqli_sql_exception $exception) {
    $dbError = 'Não foi possível conectar ao banco de dados no momento.';
    \Logger::error('Falha ao conectar no banco de dados.', [
        'exception' => $exception->getMessage(),
        'host' => $dbConfig['host'],
        'database' => $dbConfig['name'],
        'port' => $dbConfig['port'],
    ]);
}