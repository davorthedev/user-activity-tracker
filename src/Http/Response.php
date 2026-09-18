<?php

declare(strict_types=1);

namespace App\Http;

class Response
{
    private array $headers = [];

    public function __construct(
        private readonly string $body = '',
        private readonly int $status = 200,
        array $headers = []
    ) {
        if ($status < 100 || $status > 599) {
            throw new \InvalidArgumentException(sprintf('Invalid HTTP status "%d".', $status));
        }
        foreach ($headers as $key => $value) {
            $this->headers[strtolower((string) $key)] = (string) $value;
        }
    }

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status, ['content-type' => 'text/html; charset=utf-8']);
    }

    public function withHeader(string $name, string $value): static
    {
        $cloned = clone $this;
        $cloned->headers[strtolower($name)] = $value;

        return $cloned;
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value), true);
        }
        echo $this->body;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }
}
