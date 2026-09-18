<?php

declare(strict_types=1);

namespace App\Tracking;

interface EventRepository
{
    public function add(Event $event): void;
}
