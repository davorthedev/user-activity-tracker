<?php
/** @var string $csrfToken */
/** @var bool $boughtCow */
?>
<h1>Page A</h1>

<div id="buy-cow-div">
    <?php if ($boughtCow): ?>
        <p class="thank-you">thankYou</p>
    <?php else: ?>
        <form method="post" action="/page-a/buy-cow" id="buy-cow-form">
            <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
            <button type="submit">Buy a cow</button>
        </form>
    <?php endif; ?>
</div>
<script src="/assets/buy-cow.js"></script>
