<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;

final class SecurityHeaders implements Middleware
{
    public function process(Request $request, RequestHandler $next): Response
    {
        return $next->handle($request)
            ->withHeader('Content-Security-Policy', "default-src 'self'")
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Referrer-Policy', 'same-origin');
    }
}
