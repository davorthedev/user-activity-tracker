<?php

declare(strict_types=1);

namespace App\Shared;

final class ConnectionFactory
{
    /**
     * @param string|null $database - for testing
     */
    public static function create(?string $database = null): \PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            Env::getByName('DB_HOST'),
            Env::getByName('DB_PORT', '3306'),
            $database ?? Env::getByName('DB_NAME'),
        );  

        return new \PDO(
            $dsn,
            Env::getByName('DB_USER'),
            Env::getByName('DB_PASSWORD'),
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'",
            ]
        );
    }
}
