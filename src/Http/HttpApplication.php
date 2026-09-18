<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Middleware\RequestHandler;
use App\Http\Middleware\Middleware;
use App\Http\Middleware\Pipeline;

final class HttpApplication
{
    private readonly Pipeline $pipeline;

    /**
     * @param list<Middleware> $middleware
     */
    public function __construct(array $middleware, RequestHandler $handler)
    {
        $this->pipeline = new Pipeline($middleware, $handler);
    }

    public function handle(Request $request): Response
    {
        return $this->pipeline->handle($request);
    }
}
