<?php

declare(strict_types=1);

namespace App\Tracking;

final class PdoEventRepository implements EventRepository
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function add(Event $event): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO events (user_id, action, performed_on, created_at, ip_address)'
            .' VALUES (:user_id, :action, :performed_on, :created_at, :ip_address)'
        );

        $statement->execute([
            'user_id' => $event->userId,
            'action' => $event->action->value,
            'performed_on' => $event->performedOn?->value,
            'created_at' => $event->created->format('Y-m-d H:i:s.v'),
            'ip_address' => self::prepareIpAddress($event->ipAddress),
        ]);
    }

    private static function prepareIpAddress(?string $ipAddress): ?string
    {
        if (null === $ipAddress) {
            return null;
        }
        $prepared = @inet_pton($ipAddress);

        return false === $prepared ? null : $prepared;
    }
}
