<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Csrf;
use App\Http\Exception\AccessDeniedException;
use App\Http\Request;
use App\Http\Response;

final class VerifyCsrf implements Middleware
{
    /**
     * @var list<string>
     */
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function __construct(private readonly Csrf $csrf)
    {
    }

    public function process(Request $request, RequestHandler $next): Response
    {
        if (in_array($request->getMethod(), self::SAFE_METHODS, true)) {
            return $next->handle($request);
        }
        $token = $request->getPostValue('_csrf') ?? $request->getHeader('X-CSRF-Token');
        if (!$this->csrf->isValid($token)) {
            throw new AccessDeniedException('Invalid or missing CSRF token.');
        }

        return $next->handle($request);
    }
}
