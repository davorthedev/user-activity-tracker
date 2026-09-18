<?php

declare(strict_types=1);

namespace App\Admin;

interface StatsRepository
{
    public function page(EventFilter $filter, ?EventCursor $cursor, int $perPage): EventPage;
}
