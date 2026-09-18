<?php

declare(strict_types=1);

namespace App\Http;

final class DownloadResponse extends Response
{
    public function __construct(string $internalPath, string $filename)
    {
        if ('' === $internalPath) {
            throw new \InvalidArgumentException('internalPath is empty');
        }
        if ('' === $internalPath) {
            throw new \InvalidArgumentException('filename is empty');
        }
        parent::__construct(
            '', 
            200,
            [
                'x-accel-redirect' => $internalPath,
                'content-type' => 'application/octet-stream',
                'content-disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }
}
