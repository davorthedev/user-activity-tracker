<?php

declare(strict_types=1);

namespace App\Admin;

final class DailyActivity
{
    public function __construct(
        public readonly \DateTimeImmutable $day,
        public readonly int $viewsA,
        public readonly int $viewsB,
        public readonly int $clicksBuyCow,
        public readonly int $clicksDownload
    ) {
    }

    public static function empty(\DateTimeImmutable $day): self
    {
        return new self($day, 0, 0, 0, 0);
    }
}
