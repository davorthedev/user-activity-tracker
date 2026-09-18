<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class AccessDeniedException extends HttpException
{
    /**
     * @var int
     */
    private const STATUS_CODE = 403;

    public function getStatusCode(): int
    {
        return self::STATUS_CODE;
    }
}
