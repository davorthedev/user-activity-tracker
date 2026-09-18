<?php

declare(strict_types=1);

/**
 * Escape value for use in HTML.
 */
function escape(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
