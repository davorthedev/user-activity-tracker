<?php

declare(strict_types=1);

namespace App\Shared;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
