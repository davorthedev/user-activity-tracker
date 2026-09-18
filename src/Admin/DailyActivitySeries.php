<?php

declare(strict_types=1);

namespace App\Admin;

final class DailyActivitySeries
{
    /**
     * Days with no events skipped.
     *
     * @param  array<string, DailyActivity> $byDay key Y-m-d
     * 
     * @return list<DailyActivity>
     */
    public static function fill(array $byDay, \DateTimeImmutable $from, \DateTimeImmutable $toExclusive): array
    {
        $dActivities = [];
        foreach (new \DatePeriod($from, new \DateInterval('P1D'), $toExclusive) as $day) {
            $dActivities[] = $byDay[$day->format('Y-m-d')] ?? DailyActivity::empty(
                \DateTimeImmutable::createFromInterface($day)
            );
        }

        return $dActivities;
    }

    /**
     * @param list<DailyActivity> $bulk 
     */
    public static function totals(array $bulk): DailyActivity
    {
        $a = 0;
        $b = 0;
        $cow = 0;
        $down = 0;
        foreach ($bulk as $row) {
            $a += $row->viewsA;
            $b += $row->viewsB;
            $cow += $row->clicksBuyCow;
            $down += $row->clicksDownload;
        }

        return new DailyActivity(Day::today(), $a, $b, $cow, $down);
    }
}
