<?php

declare (strict_types = 1);

if (! function_exists('loadEnvFile')) {
    function loadEnvFile(string $envFilePath): array
    {
        $values = [];

        if (! is_file($envFilePath)) {
            return $values;
        }

        $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return $values;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || strpos($trimmed, '#') === 0) {
                continue;
            }

            $separatorPos = strpos($trimmed, '=');

            if ($separatorPos === false) {
                continue;
            }

            $key   = trim(substr($trimmed, 0, $separatorPos));
            $value = trim(substr($trimmed, $separatorPos + 1));

            if ($key == '') {
                continue;
            }

            $value         = trim($value, "\"'");
            $values[$key]  = $value;
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }

        return $values;
    }
}
