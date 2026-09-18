<?php

declare(strict_types=1);

namespace App\Shared;

final class ErrorLogLogger implements Logger
{
    public function error(string $message, array $context = []): void
    {
        $exception = $context['exception'] ?? null;
        unset($context['exception']);
        $line = $message;
        if ([] !== $context) {
            $line .= sprintf(' %s', json_encode($context, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));
        }

        if ($exception instanceof \Throwable) {
            $line .= sprintf(
                " | %s: %s at %s:%d\n%s",
                $exception::class,
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );
        }

        error_log($line);
    }
}
