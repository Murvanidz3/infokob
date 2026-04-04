<?php

declare(strict_types=1);

/**
 * Two rows × five category cards (homepage hub).
 *
 * @var list<array{key:string,href:string,icon:string,tone:string}> $categoryCards
 */
?>
<section class="category-hub section" aria-labelledby="category-hub-title">
    <div class="container">
        <h2 id="category-hub-title" class="category-hub__title"><?= Helpers::e(Helpers::__('home_category_hub_title')) ?></h2>
        <div class="category-hub__grid" role="list">
            <?php foreach ($categoryCards as $card): ?>
                <a class="category-card category-card--<?= Helpers::e($card['tone']) ?>"
                   href="<?= Helpers::e(BASE_URL . $card['href']) ?>"
                   role="listitem">
                    <span class="category-card__icon" aria-hidden="true"><?= $card['icon'] ?></span>
                    <span class="category-card__label"><?= Helpers::e(Helpers::__($card['key'])) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
