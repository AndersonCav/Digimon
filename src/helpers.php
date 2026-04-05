<?php

declare(strict_types=1);

if (!function_exists('h')) {
    function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit();
    }
}

if (!function_exists('isPostRequest')) {
    function isPostRequest(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}

if (!function_exists('inputString')) {
    function inputString(array $source, string $key, string $default = ''): string
    {
        $value = $source[$key] ?? $default;
        if (!is_string($value)) {
            return $default;
        }

        return trim($value);
    }
}

if (!function_exists('flashSet')) {
    function flashSet(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }
}

if (!function_exists('flashGet')) {
    function flashGet(string $key): ?string
    {
        if (!isset($_SESSION['_flash'][$key])) {
            return null;
        }

        $message = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);

        return is_string($message) ? $message : null;
    }
}

if (!function_exists('csrfToken')) {
    function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrfField')) {
    function csrfField(): string
    {
        return '<input type="hidden" name="_csrf" value="' . h(csrfToken()) . '">';
    }
}

if (!function_exists('csrfIsValid')) {
    function csrfIsValid(?string $token): bool
    {
        $sessionToken = $_SESSION['_csrf_token'] ?? '';

        if (!is_string($sessionToken) || $sessionToken === '' || !is_string($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}
