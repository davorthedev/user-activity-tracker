<?php

declare(strict_types=1);

namespace App\Admin;

use App\Tracking\Action;
use App\Tracking\PerformedOn;

final class EventRow
{
    public function __construct(
        public readonly int $id,
        public readonly \DateTimeImmutable $createdAt,
        public readonly Action $action,
        public readonly ?PerformedOn $performedOn,
        public readonly ?string $ipAddress,
        public readonly ?string $email
    ) {
    }

    public function cursor(): EventCursor
    {
        return new EventCursor($this->createdAt, $this->id);
    }
}
