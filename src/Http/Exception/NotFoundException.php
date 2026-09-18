<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class NotFoundException extends HttpException
{
    /**
     * @var int
     */
    private const STATUS_CODE = 404;

    public function getStatusCode(): int
    {
        return self::STATUS_CODE;
    }
}
