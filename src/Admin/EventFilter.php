<?php

declare(strict_types=1);

namespace App\Admin;

use App\Http\Request;
use App\Tracking\Action;

final class EventFilter
{
    /**
     * @param list<string> $errors
     */
    private function __construct(
        public readonly ?\DateTimeImmutable $from,
        public readonly ?\DateTimeImmutable $to,
        public readonly ?int $userId,
        public readonly ?Action $action,
        public readonly string $rawFrom,
        public readonly string $rawTo,
        public readonly array $errors
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $errors = [];
        $rawFrom = trim($request->getQueryParam('from', '') ?? '');
        $rawTo = trim($request->getQueryParam('to', '') ?? '');
        $from = self::parseDay($rawFrom, $errors, 'from');
        $to = self::parseDay($rawTo, $errors, 'to');
        // Adding one day for MySql
        $to = $to?->modify('+1 day');
        if (null !== $from && null !== $to && $from >= $to) {
            $errors[] = 'Start date must be same or before the end date.';
            $from = null;
            $to = null;
        }
        $rawUser = $request->getQueryParam('user', '') ?? '';
        $userId = ctype_digit($rawUser) && (int) $rawUser > 0 ? (int) $rawUser : null;
        $action = Action::tryFrom($request->getQueryParam('action', '') ?? '');

        return new self($from, $to, $userId, $action, $rawFrom, $rawTo, $errors);
    }

    /**
     * @param list<string> $errors
     */
    private static function parseDay(string $value, array &$errors, string $label): ?\DateTimeImmutable
    {
        if ('' === $value) {
            return null;
        }
        $day = \DateTimeImmutable::createFromFormat('!Y-m-d', $value, new \DateTimeZone('UTC'));
        if (false === $day || $value !== $day->format('Y-m-d')) {
            $errors[] = sprintf('Ignored "%s" date: expected YYYY-MM-DD.', $label);

            return null;
        }

        return $day;
    }

    /**
     * @return array<string, string>
     */
    public function toQuery(): array
    {
        $query = [];
        if ('' !== $this->rawFrom) {
            $query['from'] = $this->rawFrom;
        }
        if ('' !== $this->rawTo) {
            $query['to'] = $this->rawTo;
        }
        if (null !== $this->userId) {
            $query['user'] = (string) $this->userId;
        }
        if (null !== $this->action) {
            $query['action'] = $this->action->value;
        }

        return $query;
    }

    public function isActive(): bool
    {
        return [] !== $this->toQuery();
    }
}
