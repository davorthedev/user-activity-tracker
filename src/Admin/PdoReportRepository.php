<?php

declare(strict_types=1);

namespace App\Admin;

final class PdoReportRepository implements ReportRepository
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function dailyReport(ReportRange $range): array
    {
        $statement = $this->pdo->prepare(
            "SELECT DATE(created_at) AS day,
                SUM(performed_on = 'page_a') AS views_a,
                SUM(performed_on = 'page_b') AS views_b,
                SUM(performed_on = 'btn_buy_cow') AS clicks_buy_cow,
                SUM(performed_on = 'btn_download') AS clicks_download
            FROM events
            WHERE performed_on IN ('page_a', 'page_b', 'btn_buy_cow', 'btn_download')
                AND created_at >= :from
                AND created_at < :to
            GROUP BY day
            ORDER BY day"
        );
        $statement->execute([
            'from' => $range->from->format('Y-m-d H:i:s.v'),
            'to' => $range->toExclusive->format('Y-m-d H:i:s.v'),
        ]);
        $byDay = [];
        foreach ($statement->fetchAll() as $row) {
            $day = (string) $row['day'];
            $byDay[$day] = new DailyActivity(
                new \DateTimeImmutable($day, new \DateTimeZone('UTC')),
                (int) $row['views_a'],
                (int) $row['views_b'],
                (int) $row['clicks_buy_cow'],
                (int) $row['clicks_download'],
            );
        }

        return $byDay;
    }
}
