<?php

declare(strict_types=1);

namespace App\Admin;

final class EventPage
{
    /**
     * @param list<EventRow> $rows
     */
    public function __construct(public readonly array $rows, public readonly ?EventCursor $next)
    {
    }
}
