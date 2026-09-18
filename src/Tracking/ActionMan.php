<?php

declare(strict_types=1);

namespace App\Tracking;

use App\Http\Request;

final class ActionMan
{
    public function __construct(public readonly ?int $userId, public readonly ?string $ipAddress)
    {
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->getSession()?->userId(), $request->getClientIp());
    }
}
