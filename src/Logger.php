<?php

declare(strict_types=1);

final class Logger
{
    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void
    {
        $enabled = defined('APP_LOG_ENABLED') ? APP_LOG_ENABLED : true;
        if ($enabled !== true) {
            return;
        }

        $logFile = defined('APP_LOG_FILE') ? APP_LOG_FILE : (BASE_PATH . '/storage/logs/app.log');
        $directory = dirname($logFile);

        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $context !== [] ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ''
        );

        @file_put_contents($logFile, $line, FILE_APPEND);
    }
}
