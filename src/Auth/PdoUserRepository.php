<?php

declare(strict_types=1);

namespace App\Auth;

final class PdoUserRepository implements UserRepository
{
    private const MYSQL_DUPLICATE_ENTRY = 1062;

    public function __construct(private readonly \PDO $pdo)
    {
    }

    public function findById(int $id): ?User
    {
        $statement = $this->pdo->prepare('SELECT id, email, password_hash, role, created_at FROM users WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $this->hydrate($statement->fetch());
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->pdo->prepare(
            'SELECT id, email, password_hash, role, created_at FROM users WHERE email = :email'
        );
        $statement->execute(['email' => $email]);

        return $this->hydrate($statement->fetch());
    }

    public function create(string $email, string $passwordHash, Role $role, \DateTimeImmutable $createdAt): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (email, password_hash, role, created_at) VALUES (:email, :password_hash, :role, :created_at)'
        );

        try {
            $statement->execute([
                'email' => $email,
                'password_hash' => $passwordHash,
                'role' => $role->value,
                'created_at' => $createdAt->format('Y-m-d H:i:s'),
            ]);
        } catch (\PDOException $e) {
            if (($e->errorInfo[1] ?? null) === self::MYSQL_DUPLICATE_ENTRY) {
                throw new EmailAlreadyTakenException('Email duplicate.', 0, $e);
            }

            throw $e;
        }

        return (int) $this->pdo->lastInsertId();
    }

    public function updatePasswordHash(int $id, string $passwordHash): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $statement->execute(['hash' => $passwordHash, 'id' => $id]);
    }

    public function listForFilter(int $limit = 500): array
    {
        $statement = $this->pdo->query(sprintf('SELECT id, email FROM users ORDER BY email LIMIT %d', $limit));
        $users = [];
        foreach ($statement->fetchAll() as $row) {
            $users[(int) $row['id']] = (string) $row['email'];
        }

        return $users;
    }

    private function hydrate(mixed $row): ?User
    {
        if (!is_array($row)) {
            return null;
        }

        return new User(
            (int) $row['id'],
            (string) $row['email'],
            (string) $row['password_hash'],
            Role::from((string) $row['role']),
            new \DateTimeImmutable((string) $row['created_at'], new \DateTimeZone('UTC')),
        );
    }
}
