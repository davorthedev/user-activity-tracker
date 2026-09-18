<?php

declare(strict_types=1);

namespace App\Auth;

use App\Shared\Clock;

final class AuthService
{
    /**
     * Random generated.
     * 
     * @var string
     */
    private const DUMMY_HASH = '$2y$10$h92Kmqp67x1pedRGTVX9n.fJcld7yXlg/Dc92ot12jZRTHUD6jez2';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Clock $clock,
    ) {
    }

    public function attemptLogin(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user instanceof User) {
            password_verify($password, self::DUMMY_HASH);

            return null;
        }
        if (!password_verify($password, $user->passwordHash)) {
            return null;
        }
        if (password_needs_rehash($user->passwordHash, PASSWORD_DEFAULT)) {
            $this->userRepository->updatePasswordHash($user->id, password_hash($password, PASSWORD_DEFAULT));
        }

        return $user;
    }

    /**
     * @throws EmailAlreadyTakenException
     */
    public function register(string $email, string $password, Role $role = Role::User): int
    {
        return $this->userRepository->create(
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $role,
            $this->clock->now(),
        );
    }
}
