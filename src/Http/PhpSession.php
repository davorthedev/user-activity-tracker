<?php

declare(strict_types=1);

namespace App\Http;

final class PhpSession implements Session
{
    private bool $started = false;

    public function start(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;

            return;
        }
        session_start();
        $this->started = true;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function userId(): ?int
    {
        $id = $_SESSION['user_id'] ?? null;

        return is_int($id) ? $id : null;
    }

    public function logIn(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'],
                ]
            );
        }
        session_destroy();
        $this->started = false;
    }

    public function addFlashMessage(string $message): void
    {
        $flashes = $_SESSION['_flashes'] ?? [];
        $flashes[] = $message;

        $_SESSION['_flashes'] = $flashes;
    }

    public function consumeFlashMessages(): array
    {
        $flashes = $_SESSION['_flashes'] ?? [];
        unset($_SESSION['_flashes']);

        return array_values(array_filter($flashes, 'is_string'));
    }
}
