<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var string $pageH1 */
/** @var string $leadKey */
/** @var list<array{title:string,lines:list<string>}> $blocks */
?>
<div class="container guide-page">
    <p class="guide-demo-banner"><?= Helpers::e(Helpers::__('guide_demo_notice')) ?></p>
    <h1 class="page-title"><?= Helpers::e($pageH1) ?></h1>
    <p class="page-lead"><?= Helpers::e(Helpers::__($leadKey)) ?></p>
    <div class="guide-bank-list">
        <?php foreach ($blocks as $block): ?>
            <section class="guide-bank-block">
                <h2 class="guide-bank-block__title"><?= Helpers::e($block['title']) ?></h2>
                <?php foreach ($block['lines'] as $line): ?>
                    <p class="guide-bank-block__text"><?= Helpers::e($line) ?></p>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
    </div>
</div>
