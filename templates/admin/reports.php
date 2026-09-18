<?php

use App\Admin\DailyActivity;
use App\Admin\ReportRange;

/** @var ReportRange $range */
/** @var list<DailyActivity> $series */
/** @var DailyActivity $totals */
/** @var string $chart */
?>
<h1>Reports</h1>
<p class="muted">All days are UTC. Showing <?= escape((string) $range->days()) ?> days.</p>

<?php if ([] !== $range->notices): ?>
    <ul class="errors">
        <?php foreach ($range->notices as $notice): ?>
            <li><?= escape($notice) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="get" action="/admin/reports" class="filters">
    <label for="from">From</label>
    <input type="date" id="from" name="from" value="<?= escape($range->rawFrom) ?>">
    <label for="to">To</label>
    <input type="date" id="to" name="to" value="<?= escape($range->rawTo) ?>">
    <button type="submit">Show</button>
    <a href="/admin/reports">Last <?= escape((string) ReportRange::DEFAULT_DAYS) ?> days</a>
</form>

<div class="chart-wrap">
    <canvas id="report-chart" height="120"></canvas>
</div>

<script type="application/json" id="report-data"><?= $chart ?></script>
<script src="/assets/chart.umd.min.js"></script>
<script src="/assets/reports.js" defer></script>

<table class="report">
    <thead>
    <tr>
        <th>Date (UTC)</th>
        <th>Page A views</th>
        <th>Page B views</th>
        <th>Buy a cow</th>
        <th>Download</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($series as $row): ?>
        <tr>
            <td><?= escape($row->day->format('Y-m-d')) ?></td>
            <td><?= escape((string) $row->viewsA) ?></td>
            <td><?= escape((string) $row->viewsB) ?></td>
            <td><?= escape((string) $row->clicksBuyCow) ?></td>
            <td><?= escape((string) $row->clicksDownload) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <th>Total</th>
        <td><?= escape((string) $totals->viewsA) ?></td>
        <td><?= escape((string) $totals->viewsB) ?></td>
        <td><?= escape((string) $totals->clicksBuyCow) ?></td>
        <td><?= escape((string) $totals->clicksDownload) ?></td>
    </tr>
    </tfoot>
</table>
