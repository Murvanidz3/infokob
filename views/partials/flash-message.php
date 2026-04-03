<?php

declare(strict_types=1);

$flash = Helpers::consumeFlash();
if ($flash === null) {
    return;
}
$cls = $flash['type'] === 'success' ? 'flash flash--success' : ($flash['type'] === 'error' ? 'flash flash--danger' : 'flash flash--info');
?>
<div class="<?= Helpers::e($cls) ?>" role="status"><?= Helpers::e($flash['message']) ?></div>
