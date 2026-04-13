<?php
/**
 * Pagination Partial
 * Expects: $pagination array from paginate() helper
 * Expects: $baseUrl string for page links
 */

if (!isset($pagination) || $pagination['total_pages'] <= 1) return;

$current = $pagination['current_page'];
$total = $pagination['total_pages'];
$baseUrl = $baseUrl ?? '?';
$separator = strpos($baseUrl, '?') !== false ? '&' : '?';
?>

<nav class="pagination" aria-label="Pagination">
    <!-- Previous -->
    <?php if ($pagination['has_prev']): ?>
    <a href="<?= $baseUrl . $separator ?>page=<?= $current - 1 ?>" aria-label="Previous">
        <i class="ph ph-caret-left"></i>
    </a>
    <?php else: ?>
    <span class="disabled"><i class="ph ph-caret-left"></i></span>
    <?php endif; ?>
    
    <?php
    // Calculate page range to show
    $start = max(1, $current - 2);
    $end = min($total, $current + 2);
    
    // Always show first page
    if ($start > 1): ?>
    <a href="<?= $baseUrl . $separator ?>page=1">1</a>
    <?php if ($start > 2): ?>
    <span>...</span>
    <?php endif; ?>
    <?php endif; ?>
    
    <?php for ($i = $start; $i <= $end; $i++): ?>
    <?php if ($i == $current): ?>
    <span class="active"><?= $i ?></span>
    <?php else: ?>
    <a href="<?= $baseUrl . $separator ?>page=<?= $i ?>"><?= $i ?></a>
    <?php endif; ?>
    <?php endfor; ?>
    
    <?php if ($end < $total): ?>
    <?php if ($end < $total - 1): ?>
    <span>...</span>
    <?php endif; ?>
    <a href="<?= $baseUrl . $separator ?>page=<?= $total ?>"><?= $total ?></a>
    <?php endif; ?>
    
    <!-- Next -->
    <?php if ($pagination['has_next']): ?>
    <a href="<?= $baseUrl . $separator ?>page=<?= $current + 1 ?>" aria-label="Next">
        <i class="ph ph-caret-right"></i>
    </a>
    <?php else: ?>
    <span class="disabled"><i class="ph ph-caret-right"></i></span>
    <?php endif; ?>
</nav>
