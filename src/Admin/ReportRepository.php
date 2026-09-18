<?php

declare(strict_types=1);

namespace App\Admin;

interface ReportRepository
{
    /**
     * Days with no events ignored.
     * 
     * @return array<string, DailyActivity> key Y-m-d
     */
    public function dailyReport(ReportRange $range): array;
}
