<?php

declare(strict_types=1);

namespace App\Auth;

use App\Http\Exception\AccessDeniedException;
use App\Http\Middleware\Middleware;
use App\Http\Middleware\RequestHandler;
use App\Http\RedirectResponse;
use App\Http\Request;
use App\Http\Response;

final class RequireRole implements Middleware
{
    public function process(Request $request, RequestHandler $next): Response
    {
        $requiredRole = $request->getRoute()->getAttribute('role');
        if (!$requiredRole instanceof Role) {
            return $next->handle($request);
        }
        $user = $request->getUser();
        if (null === $user) {
            return new RedirectResponse(sprintf('/login?next=%s', rawurlencode($request->getPath())), 302);
        }
        if (!$user->role->allows($requiredRole)) {
            throw new AccessDeniedException('Insufficient permissions.');
        }

        return $next->handle($request);
    }
}
