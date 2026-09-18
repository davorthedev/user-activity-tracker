<?php

declare(strict_types=1);

namespace App\Tracking;

use App\Shared\Clock;
use App\Shared\Logger;

final class UserActivityTracker
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly Clock $clock,
        private readonly Logger $logger
    ) {
    }

    public function login(ActionMan $actionMan): void
    {
        $this->write(Event::login($actionMan, $this->clock->now()));
    }

    public function logout(ActionMan $actionMan): void
    {
        $this->write(Event::logout($actionMan, $this->clock->now()));
    }

    public function registration(ActionMan $actionMan): void
    {
        $this->write(Event::registration($actionMan, $this->clock->now()));
    }

    public function pageView(PerformedOn $page, ActionMan $actionMan): void
    {
        $this->write(Event::pageView($page, $actionMan, $this->clock->now()));
    }

    public function buttonClick(PerformedOn $button, ActionMan $actionMan): void
    {
        $this->write(Event::buttonClick($button, $actionMan, $this->clock->now()));
    }

    private function write(Event $event): void
    {
        try {
            $this->eventRepository->add($event);
        } catch (\Throwable $e) {
            $this->logger->error(
                'User Activity Tracker Error',
                [
                    'action' => $event->action->value,
                    'performed_on' => $event->performedOn?->value,
                    'user_id' => $event->userId,
                    'exception' => $e,
                ]
            );
        }
    }
}
