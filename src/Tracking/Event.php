<?php

declare(strict_types=1);

namespace App\Tracking;

final class Event
{
    private function __construct(
        public readonly Action $action,
        public readonly ?PerformedOn $performedOn,
        public readonly ?int $userId,
        public readonly ?string $ipAddress,
        public readonly \DateTimeImmutable $created
    ) {
    }

    public static function login(ActionMan $actionMan, \DateTimeImmutable $created): self
    {
        return new self(Action::Login, null, $actionMan->userId, $actionMan->ipAddress, $created);
    }

    public static function logout(ActionMan $actionMan, \DateTimeImmutable $created): self
    {
        return new self(Action::Logout, null, $actionMan->userId, $actionMan->ipAddress, $created);
    }

    public static function registration(ActionMan $actionMan, \DateTimeImmutable $created): self
    {
        return new self(Action::Registration, null, $actionMan->userId, $actionMan->ipAddress, $created);
    }

    public static function pageView(PerformedOn $page, ActionMan $actionMan, \DateTimeImmutable $created): self
    {
        if (PerformedOn::PageA !== $page && PerformedOn::PageB !== $page) {
            throw new \InvalidArgumentException(sprintf('Invalid page "%s"', $page->value));
        }

        return new self(Action::ViewPage, $page, $actionMan->userId, $actionMan->ipAddress, $created);
    }

    public static function buttonClick(PerformedOn $button, ActionMan $actionMan, \DateTimeImmutable $created): self
    {
        if (PerformedOn::BuyCow !== $button && PerformedOn::Download !== $button) {
            throw new \InvalidArgumentException(sprintf('Invalid button "%s"', $button->value));
        }

        return new self(Action::ButtonClick, $button, $actionMan->userId, $actionMan->ipAddress, $created);
    }
}
