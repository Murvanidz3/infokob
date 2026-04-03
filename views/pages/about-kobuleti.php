<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var list<array{section:string,title:?string,content:?string}> $sections */
?>
<section class="kob-hero">
    <div class="kob-hero__overlay"></div>
    <div class="container kob-hero__inner">
        <h1 class="kob-hero__title"><?= Helpers::e(Helpers::__('kob_page_h1')) ?></h1>
        <p class="kob-hero__lead"><?= Helpers::e(Helpers::__('kob_page_lead')) ?></p>
    </div>
</section>

<div class="container editorial">
    <?php foreach ($sections as $block): ?>
        <?php
        $t = (string) ($block['title'] ?? '');
        $c = (string) ($block['content'] ?? '');
        if ($t === '' && trim(strip_tags($c)) === '') {
            continue;
        }
        ?>
        <article class="editorial__block">
            <?php if ($t !== ''): ?>
                <h2 class="editorial__h"><?= Helpers::e($t) ?></h2>
            <?php endif; ?>
            <div class="editorial__body rte"><?= nl2br(Helpers::e($c)) ?></div>
        </article>
    <?php endforeach; ?>

    <div class="editorial__cta">
        <a class="btn btn--primary btn--pill btn--lg" href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('cta_listings_title')) ?></a>
    </div>
</div>
