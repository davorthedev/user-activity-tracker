<?php

declare(strict_types=1);

namespace App\Http;

final class Route
{
    /**
     * @var array<string, mixed>
     */
    private array $attributes = [];

    /**
     * @param array{class-string, string} $handler
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $handler
    ){
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function withAdditionalAttributes(array $attributes): self
    {
        $this->attributes = $attributes + $this->attributes;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array{class-string, string}
     */
    public function getHandler(): array
    {
        return $this->handler;
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }
}
