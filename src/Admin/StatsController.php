<?php

declare(strict_types=1);

namespace App\Admin;

use App\Auth\UserRepository;
use App\Http\Request;
use App\Http\Response;
use App\Tracking\Action;
use App\View\PageRenderer;

final class StatsController
{
    /**
     * @var int
     */
    public const PER_PAGE = 50;

    public function __construct(
        private readonly StatsRepository $statsRepository,
        private readonly UserRepository $userRepository,
        private readonly PageRenderer $pageRenderer
    ) {
    }

    public function index(Request $request): Response
    {
        $filter = EventFilter::fromRequest($request);
        $cursor = EventCursor::fromString($request->getQueryParam('cursor'));
        $page = $this->statsRepository->page($filter, $cursor, self::PER_PAGE);
        $olderQuery = null;
        if (null !== $page->next) {
            $olderQuery = http_build_query($filter->toQuery() + ['cursor' => $page->next->toString()]);
        }

        return $this->pageRenderer->page(
            $request,
            'admin/stats',
            [
                'filter' => $filter,
                'rows' => $page->rows,
                'olderQuery' => $olderQuery,
                'onFirstPage' => null === $cursor,
                'users' => $this->userRepository->listForFilter(),
                'actions' => Action::cases(),
            ],
            'Statistics'
        );
    }
}
