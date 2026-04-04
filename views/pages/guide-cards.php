<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var string $pageH1 */
/** @var string $leadKey */
/** @var list<array{image:string,title:string,lines:list<string>}> $items */
?>
<div class="container guide-page">
    <p class="guide-demo-banner"><?= Helpers::e(Helpers::__('guide_demo_notice')) ?></p>
    <h1 class="page-title"><?= Helpers::e($pageH1) ?></h1>
    <p class="page-lead"><?= Helpers::e(Helpers::__($leadKey)) ?></p>
    <div class="guide-card-grid">
        <?php foreach ($items as $card): ?>
            <article class="guide-card">
                <?php if ($card['image'] !== ''): ?>
                    <div class="guide-card__media">
                        <img src="<?= Helpers::e($card['image']) ?>" alt="" loading="lazy" width="800" height="520" decoding="async">
                    </div>
                <?php endif; ?>
                <div class="guide-card__body">
                    <h2 class="guide-card__title"><?= Helpers::e($card['title']) ?></h2>
                    <?php foreach ($card['lines'] as $line): ?>
                        <p class="guide-card__text"><?= Helpers::e($line) ?></p>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
