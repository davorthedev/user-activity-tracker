<?php

declare(strict_types=1);

namespace App\Admin;

final class Day
{
    /**
     * Parse a YYYY-MM-DD string to UTC time at midnight and guards against nonsensical dates.
     */
    public static function parse(string $value): ?\DateTimeImmutable
    {
        if ('' === $value) {
            return null;
        }
        $day = \DateTimeImmutable::createFromFormat('!Y-m-d', $value, new \DateTimeZone('UTC'));
        if (false === $day || $value !== $day->format('Y-m-d')) {
            return null;
        }

        return $day;
    }

    public static function today(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('today', new \DateTimeZone('UTC'));
    }
}
