<?php

declare(strict_types=1);

namespace App\Shared;

final class FrozenClock implements Clock
{
    public function __construct(private readonly \DateTimeImmutable $now)
    {
    }

    public static function at(string $time): self
    {
        return new self(new \DateTimeImmutable($time, new \DateTimeZone('UTC')));
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }
}
