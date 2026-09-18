<?php

declare(strict_types=1);

namespace App\Pages;

use App\Http\Request;
use App\Http\Response;
use App\Shared\Clock;
use App\View\PageRenderer;

final class HomeController
{
    public function __construct(
        private readonly PageRenderer $pageRenderer,
        private readonly Clock $clock,
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->pageRenderer->page(
            $request,
            'home',
            ['now' => $this->clock->now()->format('Y-m-d H:i:s.v')],
            'User activity tracker'
        );
    }

    public function test(Request $request): Response
    {
        throw new \RuntimeException('Jocko Willink');
    }
}
