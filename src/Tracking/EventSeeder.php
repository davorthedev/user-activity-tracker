<?php

declare(strict_types=1);

namespace App\Tracking;

final class EventSeeder
{
    /**
     * @var int
     */
    private const BATCH = 1000;

    /**
     * Example frequency.
     */
    private const MIX = [
        [Action::ViewPage, PerformedOn::PageA, 40],
        [Action::ViewPage, PerformedOn::PageB, 25],
        [Action::ButtonClick, PerformedOn::BuyCow, 6],
        [Action::ButtonClick, PerformedOn::Download, 9],
        [Action::Login, null, 10],
        [Action::Logout, null, 8],
        [Action::Registration, null, 2],
    ];

    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function seed(int $events, int $days, int $users, ?callable $progress = null): void
    {
        mt_srand(20260914);
        $userIds = $this->seedUsers($users);
        $weights = $this->getWeights();
        $total = (int) end($weights)[2];
        $end = new \DateTimeImmutable('today', new \DateTimeZone('UTC'));
        $start = $end->modify(sprintf('-%d days', $days - 1));
        $dayWeights = $this->dayWeights($start, $days);
        $dayTotal = array_sum($dayWeights);
        $this->pdo->exec('SET unique_checks = 0');
        $this->pdo->exec('SET foreign_key_checks = 0');
        $written = 0;
        while ($written < $events) {
            $size = min(self::BATCH, $events - $written);
            $rows = [];
            $args = [];
            for ($i = 0; $i < $size; $i++) {
                [$action, $performedOn] = $this->chooseActionAndPerformedOn($weights, $total);
                $createdAt = $this->chooseTime($start, $dayWeights, $dayTotal);
                $rows[] = '(?, ?, ?, ?, ?)';
                array_push(
                    $args,
                    $userIds[array_rand($userIds)],
                    $action,
                    $performedOn,
                    $createdAt->format('Y-m-d H:i:s.v'),
                    inet_pton(sprintf('172.18.2.%d', mt_rand(1, 254)))
                );
            }
            $this->pdo->beginTransaction();
            $this->pdo
                ->prepare('INSERT INTO events (user_id, action, performed_on, created_at, ip_address) VALUES ' . implode(', ', $rows))
                ->execute($args);
            $this->pdo->commit();
            $written += $size;
            if (null !== $progress) {
                $progress($written, $events);
            }
        }
        $this->pdo->exec('SET foreign_key_checks = 1');
        $this->pdo->exec('SET unique_checks = 1');
    }

    /**
     * @return list<int>
     */
    private function seedUsers(int $count): array
    {
        $existing = $this->pdo->query('SELECT id FROM users')->fetchAll(\PDO::FETCH_COLUMN);
        if (count($existing) >= $count) {
            return array_map('intval', $existing);
        }
        // Hash is reused for performance
        $hash = password_hash('ponchek2024', PASSWORD_DEFAULT);
        $statement = $this->pdo->prepare(
            'INSERT INTO users (email, password_hash, role, created_at) VALUES (?, ?, ?, UTC_TIMESTAMP())'
        );
        for ($i = count($existing); $i < $count; $i++) {
            $statement->execute([sprintf('seed%03d@mejl.com', $i), $hash, 'user']);
            $existing[] = $this->pdo->lastInsertId();
        }

        return array_map('intval', $existing);
    }

    /**
     * @return list<array{0: string, 1: ?string, 2: int}>
     */
    private function getWeights(): array
    {
        $weights = [];
        $r = 0;
        foreach (self::MIX as [$action, $performedOn, $weight]) {
            $r += $weight;
            $weights[] = [$action->value, $performedOn?->value, $r];
        }

        return $weights;
    }

    /**
     * @param  list<array{0: string, 1: ?string, 2: int}> $weights
     * @return array{0: string, 1: ?string}
     */
    private function chooseActionAndPerformedOn(array $weights, int $total): array
    {
        $temp = mt_rand(1, $total);
        foreach ($weights as [$action, $performedOn, $weight]) {
            if ($temp <= $weight) {
                return [$action, $performedOn];
            }
        }

        return ['view_page', 'page_a'];
    }

    /** @return list<int> */
    private function dayWeights(\DateTimeImmutable $start, int $days): array
    {
        $weights = [];
        for ($d = 0; $d < $days; $d++) {
            $dayOfWeek = (int) $start->modify("+{$d} days")->format('N');
            $weights[] = $dayOfWeek >= 6 ? 4 : 10;
        }

        return $weights;
    }

    /** @param list<int> $dayWeights */
    private function chooseTime(\DateTimeImmutable $start, array $dayWeights, int $dayTotal): \DateTimeImmutable
    {
        $rand = mt_rand(1, $dayTotal);
        $r = 0;
        $day = 0;
        foreach ($dayWeights as $index => $weight) {
            $r += $weight;
            if ($rand <= $r) {
                $day = $index;
                break;
            }
        }

        return $start
            ->modify("+{$day} days")
            ->setTime(mt_rand(0, 23), mt_rand(0, 59), mt_rand(0, 59), mt_rand(0, 999) * 1000);
    }
}
