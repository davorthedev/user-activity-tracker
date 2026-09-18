<?php

use App\Admin\EventFilter;
use App\Admin\EventRow;
use App\Tracking\Action;

/** @var EventFilter $filter */
/** @var list<EventRow> $rows */
/** @var ?string $olderQuery */
/** @var bool $onFirstPage */
/** @var array<int, string> $users */
/** @var list<Action> $actions */
?>
<h1>Statistics</h1>
<p class="muted">All times UTC.</p>

<?php if ([] !== $filter->errors): ?>
    <ul class="errors">
        <?php foreach ($filter->errors as $error): ?>
            <li><?= escape($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="get" action="/admin/stats" class="filters">
    <label for="from">From</label>
    <input type="date" id="from" name="from" value="<?= escape($filter->rawFrom) ?>">
    <label for="to">To</label>
    <input type="date" id="to" name="to" value="<?= escape($filter->rawTo) ?>">
    <label for="user">User</label>
    <select id="user" name="user">
        <option value="">Everyone</option>
        <?php foreach ($users as $id => $email): ?>
            <option value="<?= escape((string) $id) ?>"<?= $filter->userId === $id ? ' selected' : '' ?>>
                <?= escape($email) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="action">Action</label>
    <select id="action" name="action">
        <option value="">Any action</option>
        <?php foreach ($actions as $action): ?>
            <option value="<?= escape($action->value) ?>"<?= $filter->action === $action ? ' selected' : '' ?>>
                <?= escape($action->value) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Filter</button>
    <?php if ($filter->isActive()): ?>
        <a href="/admin/stats">Clear</a>
    <?php endif; ?>
</form>

<?php if ($rows === []): ?>
    <p>No events match these filters.</p>
<?php else: ?>
    <table class="events">
        <thead>
        <tr>
            <th>Created (UTC)</th>
            <th>User</th>
            <th>Action</th>
            <th>Performed on</th>
            <th>IP address</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= escape($row->createdAt->format('Y-m-d H:i:s.v')) ?></td>
                <td><?= escape($row->email ?? '(anonymous or deleted user)') ?></td>
                <td><?= escape($row->action->value) ?></td>
                <td><?= escape($row->performedOn?->value ?? '-') ?></td>
                <td><?= escape($row->ipAddress ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<nav class="pager">
    <?php if (!$onFirstPage): ?>
        <a href="/admin/stats<?= $filter->toQuery() === [] ? '' : '?' . escape(http_build_query($filter->toQuery())) ?>">Newest</a>
    <?php endif; ?>
    <?php if ($olderQuery !== null): ?>
        <a href="/admin/stats?<?= escape($olderQuery) ?>">Older &rarr;</a>
    <?php endif; ?>
</nav>
