<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;

final class Pipeline implements RequestHandler
{
    /**
     * @param list<Middleware> $middleware
     */
    public function __construct(
        private readonly array $middleware,
        private readonly RequestHandler $requestHandler,
    ) {
    }

    public function handle(Request $request): Response
    {
        // One instance to serve many requests, one ring to rule them all, one ring to find them
        $mid = $this->middleware;
        $current = array_shift($mid);
        if (null === $current) {
            return $this->requestHandler->handle($request);
        }

        return $current->process($request, new self($mid, $this->requestHandler));
    }
}
