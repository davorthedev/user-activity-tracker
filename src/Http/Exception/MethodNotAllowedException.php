<?php

declare(strict_types=1);

namespace App\Http\Exception;

final class MethodNotAllowedException extends HttpException
{
    /**
     * @var int
     */
    private const STATUS_CODE = 405;

    /**
     * @param list<string> $allowed
     */
    public function __construct(private readonly array $allowed)
    {
        parent::__construct('Method not allowed!');
    }

    public function getStatusCode(): int
    {
        return self::STATUS_CODE;
    }

    public function getHeaders(): array
    {
        return ['Allow' => implode(', ', $this->allowed)];
    }
}
