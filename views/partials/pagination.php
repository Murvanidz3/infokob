<?php

declare(strict_types=1);

/** @var int $page */
/** @var int $totalPages */
/** @var int $total */
/** @var string $paginationBase full URL to /listings including query (no page param) */

if ($totalPages <= 1) {
    return;
}
$sep = str_contains($paginationBase, '?') ? '&' : '?';
?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($page > 1): ?>
        <a class="pagination__link" href="<?= Helpers::e($paginationBase . $sep . 'page=' . ($page - 1)) ?>"><?= Helpers::e(Helpers::__('pagination_prev')) ?></a>
    <?php endif; ?>
    <span class="pagination__info"><?= Helpers::e((string) $page) ?> / <?= Helpers::e((string) $totalPages) ?></span>
    <?php if ($page < $totalPages): ?>
        <a class="pagination__link" href="<?= Helpers::e($paginationBase . $sep . 'page=' . ($page + 1)) ?>"><?= Helpers::e(Helpers::__('pagination_next')) ?></a>
    <?php endif; ?>
</nav>
