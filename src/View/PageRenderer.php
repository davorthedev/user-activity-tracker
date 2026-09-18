<?php

declare(strict_types=1);

namespace App\View;

use App\Http\Csrf;
use App\Http\Request;
use App\Http\Response;

final class PageRenderer
{
    public function __construct(private readonly Renderer $renderer, private readonly Csrf $csrf)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function page(
        Request $request,
        string $template,
        array $data = [],
        string $title = '',
        int $status = 200,
    ): Response {
        $shared = [
            'user' => $request->getUser(),
            'flashMessages' => $request->getSession()->consumeFlashMessages(),
            'csrfToken' => $this->csrf->token(),
        ];

        return Response::html($this->renderer->page($template, $data + $shared, $title), $status);
    }
}
