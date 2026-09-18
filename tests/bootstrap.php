<?php

declare(strict_types=1);

use App\Shared\ConnectionFactory;

$root = dirname(__DIR__);

require sprintf('%s/vendor/autoload.php', $root);

$pdo = ConnectionFactory::create(getenv('DB_NAME_TEST') ?: 'db_user_activity_tracker_test');

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('DROP TABLE IF EXISTS events, users');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
$pdo->exec(file_get_contents(sprintf('%s/migrations/schema.sql', $root)));

