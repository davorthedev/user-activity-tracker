<?php

declare(strict_types=1);

namespace App\Admin;

use App\Http\Request;
use App\Http\Response;
use App\View\PageRenderer;

final class ReportController
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
        private readonly PageRenderer $pageRenderer,
    ) {
    }

    public function index(Request $request): Response
    {
        $range  = ReportRange::fromRequest($request);
        $series = DailyActivitySeries::fill(
            $this->reportRepository->dailyReport($range), $range->from, $range->toExclusive
        );

        return $this->pageRenderer->page(
            $request,
            'admin/reports',
            [
                'range' => $range,
                'series' => $series,
                'totals' => DailyActivitySeries::totals($series),
                'chart' => self::jsonDataForChart($series),
            ],
            'Reports'
        );
    }

    /**
     * @param list<DailyActivity> $series
     */
    private static function jsonDataForChart(array $series): string
    {
        $payload = [
            'labels' => array_map(static fn (DailyActivity $dA): string => $dA->day->format('Y-m-d'), $series),
            'datasets' => [
                ['label' => 'Page A views', 'data' => array_map(static fn ($dA): int => $dA->viewsA, $series)],
                ['label' => 'Page B views', 'data' => array_map(static fn ($dA): int => $dA->viewsB, $series)],
                ['label' => 'Buy a cow', 'data' => array_map(static fn ($dA): int => $dA->clicksBuyCow, $series)],
                ['label' => 'Download', 'data' => array_map(static fn ($dA): int => $dA->clicksDownload, $series)],
            ],
        ];

        return json_encode($payload);
    }
}
