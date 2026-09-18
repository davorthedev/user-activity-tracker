<?php

declare(strict_types=1);

namespace App\Auth;

interface UserRepository
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    /**
     * @throws EmailAlreadyTakenException
     * 
     * @return int userId
     */
    public function create(string $email, string $passwordHash, Role $role, \DateTimeImmutable $createdAt): int;

    public function updatePasswordHash(int $id, string $passwordHash): void;

    /**
     * Id => email, for dropdown list on statistic page
     *
     * @return array<int, string>
     */
    public function listForFilter(int $limit = 500): array;
}
