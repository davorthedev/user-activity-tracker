<?php

declare(strict_types=1);

namespace App\Auth;

final class RegistrationValidator
{
    public const MIN_PASSWORD_LENGTH = 8;

    public const MAX_PASSWORD_LENGTH = 200;

    public const MAX_EMAIL_LENGTH = 254;

    /**
     * @return list<string>
     */
    public function validate(string $email, string $password, string $passwordConfirm): array
    {
        $errors = [];
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Invalid email address.';
        } elseif (strlen($email) > self::MAX_EMAIL_LENGTH) {
            $errors[] = 'Email address is too long.';
        }
        $length = strlen($password);
        if ($length < self::MIN_PASSWORD_LENGTH) {
            $errors[] = sprintf('Password must be min %d characters.', self::MIN_PASSWORD_LENGTH);
        } elseif ($length > self::MAX_PASSWORD_LENGTH) {
            $errors[] = sprintf('Password must be max %d characters.', self::MAX_PASSWORD_LENGTH);
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }

        return $errors;
    }
}
