<?php

declare(strict_types=1);

namespace App\Admin;

final class EventCursor
{
    /**
     * @var string
     */
    private const FORMAT = 'Y-m-d H:i:s.v';

    public function __construct(public readonly \DateTimeImmutable $createdAt, public readonly int $id) {
    }

    public function toString(): string
    {
        $raw = sprintf('%s|%s', $this->createdAt->format(self::FORMAT), $this->id);

        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    public static function fromString(?string $value): ?self
    {
        if (null === $value || '' === $value) {
            return null;
        }
        $padded = strtr($value, '-_', '+/');
        $decoded = base64_decode($padded, true);
        if (false === $decoded || 1 !== substr_count($decoded, '|')) {
            return null;
        }
        [$at, $id] = explode('|', $decoded);
        if (!ctype_digit($id)) {
            return null;
        }
        $createdAt = \DateTimeImmutable::createFromFormat(
            '!' . self::FORMAT,
            $at,
            new \DateTimeZone('UTC'),
        );
        if (false === $createdAt) {
            return null;
        }

        return new self($createdAt, (int) $id);
    }
}
