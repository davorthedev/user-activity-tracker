<?php

declare(strict_types=1);

use App\Admin\PdoReportRepository;
use App\Admin\PdoStatsRepository;
use App\Admin\ReportController;
use App\Admin\ReportRepository;
use App\Admin\StatsController;
use App\Admin\StatsRepository;
use App\Auth\AuthController;
use App\Auth\AuthService;
use App\Auth\PdoUserRepository;
use App\Auth\RegistrationValidator;
use App\Auth\RequireRole;
use App\Auth\ResolveUser;
use App\Auth\UserRepository;
use App\Http\Csrf;
use App\Http\ControllerDispatcher;
use App\Http\HttpApplication;
use App\Http\Middleware\CatchErrors;
use App\Http\Middleware\ResolveRoute;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\StartSession;
use App\Http\Middleware\VerifyCsrf;
use App\Http\PhpSession;
use App\Http\Router;
use App\Http\Session;
use App\Pages\HomeController;
use App\Pages\PageController;
use App\Shared\Clock;
use App\Shared\ConnectionFactory;
use App\Shared\ErrorLogLogger;
use App\Shared\Logger;
use App\Shared\SystemClock;
use App\Tracking\EventRepository;
use App\Tracking\PdoEventRepository;
use App\Tracking\TrackPageView;
use App\Tracking\UserActivityTracker;
use App\View\PageRenderer;
use App\View\Renderer;

/**
 * @param array{
 *     pdo?: PDO,
 *     session?: Session,
 *     clock?: Clock,
 *     logger?: Logger,
 *     userRepository?: UserRepository,
 *     eventRepository?: EventRepository,
 *     statsRepository?: StatsRepository,
 *     reportRepository?: ReportRepository
 * } $overrides
 */
return static function (array $overrides = []): HttpApplication {
    $root = dirname(__DIR__);

    $renderer = new Renderer(sprintf('%s/templates', $root));
    $logger = $overrides['logger'] ?? new ErrorLogLogger();
    $clock = $overrides['clock'] ?? new SystemClock();
    $session = $overrides['session'] ?? new PhpSession();
    $csrf = new Csrf($session);
    $pdo = $overrides['pdo'] ?? null;
    $userRepository = $overrides['userRepository'] ?? new PdoUserRepository($pdo ??= ConnectionFactory::create());
    $eventRepository = $overrides['eventRepository'] ?? new PdoEventRepository($pdo ??= ConnectionFactory::create());
    $statsRepository = $overrides['statsRepository'] ?? new PdoStatsRepository($pdo ??= ConnectionFactory::create());
    $reportRepository = $overrides['reportRepository'] ?? new PdoReportRepository($pdo ??= ConnectionFactory::create());

    $auth = new AuthService($userRepository, $clock);
    $validator = new RegistrationValidator();
    $pageRenderer = new PageRenderer($renderer, $csrf);
    $tracker = new UserActivityTracker($eventRepository, $clock, $logger);

    $router = new Router();
    (require sprintf('%s/config/routes.php', $root))($router);

    $controllers = [
        HomeController::class => static fn (): HomeController => new HomeController($pageRenderer, $clock),
        AuthController::class => static fn (): AuthController => new AuthController($auth, $validator, $pageRenderer, $tracker),
        PageController::class => static fn (): PageController => new PageController($pageRenderer, $tracker),
        StatsController::class => static fn (): StatsController => new StatsController($statsRepository, $userRepository, $pageRenderer),
        ReportController::class => static fn (): ReportController => new ReportController($reportRepository, $pageRenderer),
    ];

    return new HttpApplication(
        [
            new SecurityHeaders(),
            new CatchErrors($renderer, $logger),
            new StartSession($session),
            new ResolveRoute($router),
            new VerifyCsrf($csrf),
            new ResolveUser($userRepository),
            new RequireRole(),
            new TrackPageView($tracker)
        ],
        new ControllerDispatcher($controllers),
    );
};
