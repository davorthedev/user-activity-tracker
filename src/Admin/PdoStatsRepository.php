<?php

declare(strict_types=1);

namespace App\Admin;

use App\Tracking\Action;
use App\Tracking\PerformedOn;

final class PdoStatsRepository implements StatsRepository
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function page(EventFilter $filter, ?EventCursor $cursor, int $perPage): EventPage
    {
        $conditions = [];
        $params = [];
        if (null !== $filter->from) {
            $conditions[] = 'e.created_at >= :from';
            $params['from'] = $filter->from->format('Y-m-d H:i:s.v');
        }
        if (null !== $filter->to) {
            $conditions[] = 'e.created_at < :to';
            $params['to'] = $filter->to->format('Y-m-d H:i:s.v');
        }
        if (null !== $filter->userId) {
            $conditions[] = 'e.user_id = :user_id';
            $params['user_id'] = $filter->userId;
        }
        if (null !== $filter->action) {
            $conditions[] = 'e.action = :action';
            $params['action'] = $filter->action->value;
        }
        if (null !== $cursor) {
            $conditions[] = '(e.created_at, e.id) < (:cursor_at, :cursor_id)';
            $params['cursor_at'] = $cursor->createdAt->format('Y-m-d H:i:s.v');
            $params['cursor_id'] = $cursor->id;
        }
        $sql = 'SELECT e.id, e.created_at, e.action, e.performed_on, INET6_NTOA(e.ip_address) AS ip_address, u.email'
            .' FROM events e LEFT JOIN users u ON u.id = e.user_id';
        if ([] !== $conditions) {
            $sql .= sprintf(' WHERE %s', implode(' AND ', $conditions));
        }
        $sql .= sprintf(' ORDER BY e.created_at DESC, e.id DESC LIMIT %d', ((int) $perPage + 1));
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $rows = [];

        foreach ($statement->fetchAll() as $row) {
            $rows[] = new EventRow(
                (int) $row['id'],
                new \DateTimeImmutable((string) $row['created_at'], new \DateTimeZone('UTC')),
                Action::from((string) $row['action']),
                null === $row['performed_on'] ? null : PerformedOn::from((string) $row['performed_on']),
                null === $row['ip_address'] ? null : (string) $row['ip_address'],
                null === $row['email'] ? null : (string) $row['email']
            );
        }
        $next = null;
        if (count($rows) > $perPage) {
            $rows = array_slice($rows, 0, $perPage);
            $next = $rows[$perPage - 1]->cursor();
        }

        return new EventPage($rows, $next);
    }
}
