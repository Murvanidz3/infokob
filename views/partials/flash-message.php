<?php
/**
 * Flash Message Partial
 * Displays success/error/warning flash messages with auto-dismiss
 */
?>

<?php if ($msg = getFlash('success')): ?>
<div class="container" style="padding-top: var(--space-4);">
    <div class="flash-message flash-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <i class="ph ph-check-circle"></i>
        <span><?= e($msg) ?></span>
        <button class="flash-close" @click="show = false"><i class="ph ph-x"></i></button>
    </div>
</div>
<?php endif; ?>

<?php if ($msg = getFlash('error')): ?>
<div class="container" style="padding-top: var(--space-4);">
    <div class="flash-message flash-error" x-data="{ show: true }" x-show="show">
        <i class="ph ph-warning-circle"></i>
        <span><?= e($msg) ?></span>
        <button class="flash-close" @click="show = false"><i class="ph ph-x"></i></button>
    </div>
</div>
<?php endif; ?>

<?php if ($msg = getFlash('warning')): ?>
<div class="container" style="padding-top: var(--space-4);">
    <div class="flash-message flash-warning" x-data="{ show: true }" x-show="show">
        <i class="ph ph-info"></i>
        <span><?= e($msg) ?></span>
        <button class="flash-close" @click="show = false"><i class="ph ph-x"></i></button>
    </div>
</div>
<?php endif; ?>

<?php if ($msg = getFlash('info')): ?>
<div class="container" style="padding-top: var(--space-4);">
    <div class="flash-message flash-info" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <i class="ph ph-info"></i>
        <span><?= e($msg) ?></span>
        <button class="flash-close" @click="show = false"><i class="ph ph-x"></i></button>
    </div>
</div>
<?php endif; ?>
