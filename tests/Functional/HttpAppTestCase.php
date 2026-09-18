<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Http\ArraySession;
use App\Http\Csrf;
use App\Http\HttpApplication;
use App\Http\Request;
use App\Http\Response;
use App\Shared\ConnectionFactory;
use App\Shared\FrozenClock;
use PHPUnit\Framework\TestCase;

abstract class HttpAppTestCase extends TestCase
{
    protected \PDO $pdo;

    protected ArraySession $session;

    protected HttpApplication $httpApp;

    protected Csrf $csrf;

    protected function setUp(): void
    {
        $this->pdo = ConnectionFactory::create(getenv('DB_NAME_TEST') ?: 'db_user_activity_tracker_test');
        $this->pdo->exec('DELETE FROM events');
        $this->pdo->exec('DELETE FROM users');
        $this->session = new ArraySession();
        $this->csrf = new Csrf($this->session);
        // require on purpose
        $build = require dirname(__DIR__, 2) . '/config/container.php';
        $this->httpApp = $build([
            'pdo' => $this->pdo,
            'session' => $this->session,
            'clock' => FrozenClock::at('2026-09-17 12:00:00'),
        ]);
    }

    protected function get(string $path, array $query = []): Response
    {
        return $this->httpApp
            ->handle(Request::create('GET', $path, queryParams: $query, server: ['REMOTE_ADDR' => '127.0.0.1']));
    }

    protected function post(string $path, array $body = []): Response
    {
        $body['_csrf'] ??= $this->csrf->token();

        return $this->httpApp
            ->handle(Request::create('POST', $path, post: $body, server: ['REMOTE_ADDR' => '127.0.0.1']));
    }
}
