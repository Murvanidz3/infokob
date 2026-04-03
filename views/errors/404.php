<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
?>
<div class="container page-error">
    <h1 class="page-error__title">404</h1>
    <p class="page-error__text"><?= Helpers::e(Helpers::__('error_404')) ?></p>
    <a class="btn btn--primary btn--pill" href="<?= Helpers::e(PUBLIC_BASE_URL) ?>/"><?= Helpers::e(Helpers::__('nav_home')) ?></a>
</div>
