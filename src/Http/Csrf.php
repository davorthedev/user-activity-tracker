<?php

declare(strict_types=1);

namespace App\Http;

final class Csrf
{
    private const SESSION_KEY = '_csrf';

    public function __construct(private readonly Session $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get(self::SESSION_KEY);
        if (!is_string($token) || '' === $token) {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public function isValid(?string $receivedToken): bool
    {
        $token = $this->session->get(self::SESSION_KEY);
        if (!is_string($token) || '' === $token || null === $receivedToken) {
            return false;
        }

        return hash_equals($token, $receivedToken);
    }
}
