<?php

declare(strict_types=1);

use App\Http\HttpApplication;
use App\Http\Request;

$root = dirname(__DIR__);
require sprintf('%s/vendor/autoload.php', $root);

try {
    /** @var HttpApplication $httpApp */
    $httpApp = require sprintf('%s/config/container.php', $root);
    $response = $httpApp()->handle(Request::createFromGlobals());
} catch (Throwable $e) {
    // For uncaught failures
    error_log((string) $e);
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit("Server error\n");
}

$response->send();
