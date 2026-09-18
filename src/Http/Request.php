<?php

declare(strict_types=1);

namespace App\Http;

use App\Auth\User;

final class Request
{
    /**
     * @var string
     */
    private const CT = 'CONTENT_TYPE';

    /**
     * @var string
     */
    private const CL = 'CONTENT_LENGTH';
    
    private readonly string $path;

    private ?Route $route = null;

    private ?Session $session = null;

    private ?User $user = null;

    private function __construct(
        private readonly string $method,
        string $path,
        private readonly array $queryParams,
        private readonly array $post,
        private readonly array $headers,
        private readonly array $server
    ) {
        $this->path = self::handlePath($path);
    }

    public static function createFromGlobals(): self
    {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);

        return new self(
            strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')),
            is_string($path) ? rawurldecode($path) : '/',
            $_GET,
            $_POST,
            self::extractHeadersFromServer($_SERVER),
            $_SERVER
        );
    }

    public static function create(
        string $method,
        string $path,
        array $queryParams = [],
        array $post = [],
        array $headers = [],
        array $server = []
    ): self {
        $lowHeaders = [];
        foreach ($headers as $key => $value) {
            $lowHeaders[strtolower((string) $key)] = (string) $value;
        }

        return new self(
            strtoupper($method),
            $path,
            $queryParams,
            $post,
            $lowHeaders,
            $server
        );
    }

    public function getSession(): Session
    {
        if (null === $this->session) {
            throw new \LogicException('No session.');
        }

        return $this->session;
    }

    public function withSession(Session $session): self
    {
        $cloned = clone $this;
        $cloned->session = $session;

        return $cloned;
    }

    /**
     * @return User|null Null until ResolveUser run and on anonymous requests.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function withUser(User $user): self
    {
        $cloned = clone $this;
        $cloned->user = $user;

        return $cloned;
    }

    public function withRoute(Route $route): self
    {
        $cloned = clone $this;
        $cloned->route = $route;

        return $cloned;
    }

    public function getRoute(): Route
    {
        if (!$this->route instanceof Route) {
            throw new \LogicException('There is no "route"!');
        }

        return $this->route;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getQueryParam(string $key, ?string $default = null): ?string
    {
        $value = $this->queryParams[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    public function getPost(): array
    {
        return $this->post;
    }

    public function getPostValue(string $key, ?string $default = null): ?string
    {
        $value = $this->post[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function getServer(): array
    {
        return $this->server;
    }

    public function getClientIp(): ?string
    {
        $ip = $this->server['REMOTE_ADDR'] ?? null;

        return is_string($ip) && '' !== $ip ? $ip : null;
    }

    private static function handlePath(string $path): string
    {
        $path = rtrim(sprintf('/%s', ltrim($path, '/')), '/');

        return '' === $path ? '/' : $path;
    }
    
    private static function extractHeadersFromServer(array $server): array
    {
        $headers = [];
        if (isset($server[self::CT])) {
            $headers['content-type'] = (string) $server[self::CT];
        }
        if (isset($server[self::CL])) {
            $headers['content-length'] = (string) $server[self::CL];
        }
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = (string) $value;
            }
        }

        return $headers;
    }
}
