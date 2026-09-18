<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Http\Router;

final class ResolveRoute implements Middleware
{
    public function __construct(private readonly Router $router)
    {
    }

    public function process(Request $request, RequestHandler $next): Response
    {
        $route = $this->router->findRoute($request->getMethod(), $request->getPath());

        return $next->handle($request->withRoute($route));
    }
}
