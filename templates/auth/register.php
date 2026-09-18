<?php
/** @var string $email */
/** @var list<string> $errors */
/** @var string $csrfToken */

use App\Auth\RegistrationValidator;
?>
<h1>Register</h1>
<?php if ([] !== $errors): ?>
    <ul class="errors">
        <?php foreach ($errors as $error): ?>
            <li><?= escape($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<form method="post" action="/register">
    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= escape($email) ?>" required autocomplete="username"
        maxlength="<?= RegistrationValidator::MAX_EMAIL_LENGTH ?>">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="new-password"
        minlength="<?= RegistrationValidator::MIN_PASSWORD_LENGTH ?>"
        maxlength="<?= RegistrationValidator::MAX_PASSWORD_LENGTH ?>">
    <label for="password_confirm">Repeat password</label>
    <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password"
        minlength="<?= RegistrationValidator::MIN_PASSWORD_LENGTH ?>"
        maxlength="<?= RegistrationValidator::MAX_PASSWORD_LENGTH ?>">
    <button type="submit">Create account</button>
</form>
<p>Already registered? <a href="/login">Log in</a>.</p>
