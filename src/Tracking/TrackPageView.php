<?php

declare(strict_types=1);

namespace App\Tracking;

use App\Http\Middleware\RequestHandler;
use App\Http\Middleware\Middleware;
use App\Http\Request;
use App\Http\Response;

final class TrackPageView implements Middleware
{
    /**
     * @var string
     */
    private const PREFETCH = 'prefetch';

    public function __construct(private readonly UserActivityTracker $tracker)
    {
    }

    public function process(Request $request, RequestHandler $next): Response
    {
        $response = $next->handle($request);
        $performedOn = $request->getRoute()->getAttribute('track');
        if (!$performedOn instanceof PerformedOn) {
            return $response;
        }
        if ('GET' !== $request->getMethod()) {
            return $response;
        }
        if (200 !== $response->getStatus()) {
            return $response;
        }
        if ($this->isPrefetch($request)) {
            return $response;
        }
        $this->tracker->pageView($performedOn, ActionMan::fromRequest($request));

        return $response;
    }

    private function isPrefetch(Request $request): bool
    {
        $secPurpose = strtolower($request->getHeader('Sec-Purpose') ?? '');
        if (str_contains($secPurpose, self::PREFETCH) || str_contains($secPurpose, 'prerender')) {
            return true;
        }
        if (self::PREFETCH === strtolower($request->getHeader('Purpose') ?? '')) {
            return true;
        }

        return self::PREFETCH === strtolower($request->getHeader('X-Moz') ?? '');
    }
}
