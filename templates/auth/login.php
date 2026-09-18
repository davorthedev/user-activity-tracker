<?php
/** @var string $email */
/** @var list<string> $errors */
/** @var string $next */
/** @var string $csrfToken */
?>
<h1>Log in</h1>
<?php if ([] !== $errors): ?>
    <ul class="errors">
        <?php foreach ($errors as $error): ?>
            <li><?= escape($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<form method="post" action="/login">
    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= escape($email) ?>" required autocomplete="username">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="current-password">
    <button type="submit">Log in</button>
</form>
<p>No account? <a href="/register">Register</a>.</p>
