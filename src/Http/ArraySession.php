<?php

declare(strict_types=1);

namespace App\Http;

final class ArraySession implements Session
{
    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    public function start(): void
    {
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function userId(): ?int
    {
        $id = $this->data['user_id'] ?? null;

        return is_int($id) ? $id : null;
    }

    public function logIn(int $userId): void
    {
        $this->data['user_id'] = $userId;
    }

    public function destroy(): void
    {
        $this->data = [];
    }

    public function addFlashMessage(string $message): void
    {
        $this->data['_flashes'][] = $message;
    }

    public function consumeFlashMessages(): array
    {
        $flashes = $this->data['_flashes'] ?? [];
        unset($this->data['_flashes']);

        return array_values(array_filter($flashes, 'is_string'));
    }
}
