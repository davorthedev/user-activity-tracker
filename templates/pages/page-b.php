<?php
/** @var string $csrfToken */
?>
<h1>Page B</h1>

<form method="post" action="/page-b/download">
    <input type="hidden" name="_csrf" value="<?= escape($csrfToken) ?>">
    <button type="submit">Download</button>
</form>
