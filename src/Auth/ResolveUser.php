<?php

declare(strict_types=1);

namespace App\Auth;

use App\Http\Middleware\Middleware;
use App\Http\Middleware\RequestHandler;
use App\Http\Request;
use App\Http\Response;

final class ResolveUser implements Middleware
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function process(Request $request, RequestHandler $next): Response
    {
        $userId = $request->getSession()->userId();
        if (null === $userId) {
            return $next->handle($request);
        }
        $user = $this->userRepository->findById($userId);
        if (null === $user) {
            $request->getSession()->destroy();

            return $next->handle($request);
        }

        return $next->handle($request->withUser($user));
    }
}
