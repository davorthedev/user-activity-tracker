<?php

declare(strict_types=1);

namespace App\Http;

final class RedirectResponse extends Response
{
    private const VALID_STATUSES = [
        301,
        302,
        303, // default "see other"
        307,
        308,
    ];

    public function __construct(string $location, int $status = 303)
    {
        if ('' === $location) {
            throw new \InvalidArgumentException('Redirect location is empty!');
        }
        if (!in_array($status, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException(sprintf('Invalid redirect HTTP status "%d"!', $status));
        }
        parent::__construct('', $status, ['location' => $location]);
    }
}
