<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Exception\MethodNotAllowedException;
use App\Http\Exception\NotFoundException;

final class Router
{
    /**
     * @var string
     */
    private const GET = 'GET';

    /**
     * @var string
     */
    private const POST = 'POST';

    /**
     * @var string
     */
    private const HEAD = 'HEAD';

    /**
     * Index is path.
     *
     * @var array<string, array<string, Route>>
     */
    private array $routes = [];

    /**
     * @param array{class-string, string} $handler
     */
    public function get(string $path, array $handler): Route
    {
        return $this->createRoute(self::GET, $path, $handler);
    }

    /**
     * @param array{class-string, string} $handler
     */
    public function post(string $path, array $handler): Route
    {
        return $this->createRoute(self::POST, $path, $handler);
    }

    /**
     * @param array{class-string, string} $handler
     */
    private function createRoute(string $method, string $path, array $handler): Route
    {
        $route = new Route($method, $path, $handler);
        $this->routes[$path][$method] = $route;

        return $route;
    }

    public function findRoute(string $method, string $path): Route
    {
        if (!isset($this->routes[$path])) {
            throw new NotFoundException(sprintf('No route for path "%s".', $path));
        }
        if (self::HEAD === $method && isset($this->routes[$path][self::GET])) {
            $method = self::GET;
        }
        if (!isset($this->routes[$path][$method])) {
            throw new MethodNotAllowedException(array_keys($this->routes[$path]));
        }

        return $this->routes[$path][$method];
    }
}
