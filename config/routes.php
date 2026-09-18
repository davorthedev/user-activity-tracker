<?php

declare(strict_types=1);

use App\Admin\ReportController;
use App\Admin\StatsController;
use App\Auth\AuthController;
use App\Auth\Role;
use App\Http\Router;
use App\Pages\HomeController;
use App\Pages\PageController;
use App\Tracking\PerformedOn;

return static function (Router $routes): void {
    $routes->get('/', [HomeController::class, 'index']);
    $routes->get('/test', [HomeController::class, 'test']);
    $routes->get('/register', [AuthController::class, 'showRegister']);
    $routes->post('/register', [AuthController::class, 'register']);
    $routes->get('/login', [AuthController::class, 'showLogin']);
    $routes->post('/login', [AuthController::class, 'login']);
    $routes->post('/logout', [AuthController::class, 'logout'])->withAdditionalAttributes(['role' => Role::User]);
    $routes->get('/page-a', [PageController::class, 'pageA'])
        ->withAdditionalAttributes(['role' => Role::User, 'track' => PerformedOn::PageA]);
    $routes->post('/page-a/buy-cow', [PageController::class, 'buyCow'])
        ->withAdditionalAttributes(['role' => Role::User]);
    $routes->get('/page-b', [PageController::class, 'pageB'])
        ->withAdditionalAttributes(['role' => Role::User, 'track' => PerformedOn::PageB]);
    $routes->post('/page-b/download', [PageController::class, 'download'])
        ->withAdditionalAttributes(['role' => Role::User]);
    $routes->get('/admin/stats', [StatsController::class, 'index'])->withAdditionalAttributes(['role' => Role::Admin]);
    $routes->get('/admin/reports', [ReportController::class, 'index'])->withAdditionalAttributes(['role' => Role::Admin]);
};
