<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Http\Session;

final class StartSession implements Middleware
{
    public function __construct(private readonly Session $session)
    {
    }

    public function process(Request $request, RequestHandler $next): Response
    {
        $this->session->start();

        return $next->handle($request->withSession($this->session));
    }
}
