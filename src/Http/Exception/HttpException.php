<?php

declare(strict_types=1);

namespace App\Http\Exception;

abstract class HttpException extends \RuntimeException
{
    abstract public function getStatusCode(): int;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return [];
    }
}
