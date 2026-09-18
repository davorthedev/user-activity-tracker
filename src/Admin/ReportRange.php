<?php

declare(strict_types=1);

namespace App\Admin;

use App\Http\Request;

final class ReportRange
{
    /**
     * @var int
     */
    public const DEFAULT_DAYS = 30;

    /**
     * @var int
     */
    public const MAX_DAYS = 366;

    /**
     * @param list<string> $notices
     */
    private function __construct(
        public readonly \DateTimeImmutable $from,
        public readonly \DateTimeImmutable $toExclusive,
        public readonly string $rawFrom,
        public readonly string $rawTo,
        public readonly array $notices
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $notices = [];
        $rawFrom = trim($request->getQueryParam('from', '') ?? '');
        $rawTo = trim($request->getQueryParam('to', '') ?? '');
        $from = Day::parse($rawFrom);
        $to = Day::parse($rawTo);
        if ('' !== $rawFrom && null === $from) {
            $notices[] = 'Ignoring start date: expected YYYY-MM-DD.';
        }
        if ('' !== $rawTo && null === $to) {
            $notices[] = 'Ignoring end date: expected YYYY-MM-DD.';
        }
        $today = Day::today(); 
        $to ??= $today;
        $defaultDaysMinusOne = sprintf('-%d days', self::DEFAULT_DAYS - 1);
        $from ??= $to->modify($defaultDaysMinusOne);
        if ($from > $to) {
            $notices[] = 'Start date greater than end date, using default range.';
            $to = $today;
            $from = $to->modify($defaultDaysMinusOne);
        }
        $span = (int) $from->diff($to)->days + 1;
        if ($span > self::MAX_DAYS) {
            $from = $to->modify(sprintf('-%d days', self::MAX_DAYS - 1));
        }

        // End day is inclusive APP user, exclusive in DB.
        return new self($from, $to->modify('+1 day'), $from->format('Y-m-d'), $to->format('Y-m-d'), $notices);
    }

    public function days(): int
    {
        return (int) $this->from->diff($this->toExclusive)->days;
    }
}
