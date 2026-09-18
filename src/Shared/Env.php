<?php

declare(strict_types=1);

namespace App\Shared;

final class Env
{
    public static function getByName(string $name, ?string $default = null): string
    {
        $value = $_ENV[$name] ?? getenv($name);
        if (false === $value || null === $value || '' === $value) {
            if (null === $default) {
                throw new \RuntimeException(sprintf('Missing env variable: %s', $name));
            }

            return $default;
        }

        return (string) $value;
    }
}
