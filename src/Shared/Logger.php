<?php

declare(strict_types=1);

namespace App\Shared;

interface Logger
{
    /**
     * @param array<string, mixed> $context
     */
    public function error(string $message, array $context = []): void;
}
