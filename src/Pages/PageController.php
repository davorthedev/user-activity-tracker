<?php

declare(strict_types=1);

namespace App\Pages;

use App\Http\DownloadResponse;
use App\Http\RedirectResponse;
use App\Http\Request;
use App\Http\Response;
use App\Tracking\UserActivityTracker;
use App\Tracking\ActionMan;
use App\Tracking\PerformedOn;
use App\View\PageRenderer;

final class PageController
{
    private const DOWNLOAD_INTERNAL_PATH = '/_protected/random-installer.exe';

    private const DOWNLOAD_FILENAME = 'random-installer.exe';

    private const BOUGHT_COW_KEY = 'bought_cow';

    public function __construct(
        private readonly PageRenderer $pageRenderer,
        private readonly UserActivityTracker $tracker
    ) {
    }

    public function pageA(Request $request): Response
    {
        // "thankYou" just once
        $boughtCow = true === $request->getSession()->get(self::BOUGHT_COW_KEY);
        $request->getSession()->set(self::BOUGHT_COW_KEY, null);

        return $this->pageRenderer->page($request, 'pages/page-a', ['boughtCow' => $boughtCow], 'Page A');
    }

    public function buyCow(Request $request): Response
    {
        $this->tracker->buttonClick(PerformedOn::BuyCow, ActionMan::fromRequest($request));
        if ($this->isJson($request)) {
            return new Response('', 204);
        }
        $request->getSession()->set(self::BOUGHT_COW_KEY, true);

        return new RedirectResponse('/page-a');
    }

    public function pageB(Request $request): Response
    {
        return $this->pageRenderer->page($request, 'pages/page-b', [], 'Page B');
    }

    public function download(Request $request): Response
    {
        $this->tracker->buttonClick(PerformedOn::Download, ActionMan::fromRequest($request));

        return new DownloadResponse(self::DOWNLOAD_INTERNAL_PATH, self::DOWNLOAD_FILENAME);
    }

    private function isJson(Request $request): bool
    {
        return str_contains($request->getHeader('Accept') ?? '', 'application/json');
    }
}
