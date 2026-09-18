<?php

declare(strict_types=1);

namespace App\Http;

interface Session
{
    public function start(): void;

    public function get(string $key): mixed;

    public function set(string $key, mixed $value): void;

    public function userId(): ?int;

    public function logIn(int $userId): void;

    public function destroy(): void;

    public function addFlashMessage(string $message): void;

    /**
     * @return list<string>
     */
    public function consumeFlashMessages(): array;
}
