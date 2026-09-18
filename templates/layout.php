<?php
/** @var string $title */
/** @var string $content */
$user = $user ?? null;
$flashMessages = $flashMessages ?? [];
$csrfToken = $csrfToken ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape($title) ?></title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
<header class="topbar">
    <a href="/">User activity tracker</a>
    <nav>
        <?php if (null !== $user): ?>
            <a href="/page-a">Page A</a>
            <a href="/page-b">Page B</a>
            <?php if ($user->role->allows(\App\Auth\Role::Admin)): ?>
                <a href="/admin/stats">Statistics</a>
                <a href="/admin/reports">Reports</a>
            <?php endif; ?>
            <form method="post" action="/logout" class="inline">
                <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
                <button type="submit">Log out (<?= escape($user->email) ?>)</button>
            </form>
        <?php else: ?>
            <a href="/login">Log in</a>
            <a href="/register">Register</a>
        <?php endif; ?>
    </nav>
</header>
<?php foreach ($flashMessages as $msg): ?>
    <p class="flash"><?= escape($msg) ?></p>
<?php endforeach; ?>
<main>
    <?= $content ?>
</main>
</body>
</html>
