<?php

declare(strict_types=1);

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user_id'] : null;
    }

    public static function username(): ?string
    {
        $username = $_SESSION['username'] ?? null;
        return is_string($username) ? $username : null;
    }

    public static function login(array $user): void
    {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['username'] = (string) $user['username'];
        session_regenerate_id(true);
    }

    public static function requireLogin(string $redirectPath = 'login.php'): void
    {
        if (!self::check()) {
            flashSet('error', 'Você precisa fazer login para acessar esta página.');
            redirect($redirectPath);
        }
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
