<?php
/**
 * Property Card Partial
 * Reusable card component used in grids
 * 
 * Expects: $property (array with title, price, main_image, etc.)
 */

$mainImage = !empty($property['main_image']) 
    ? getImageUrl($property['main_image'], 'thumb') 
    : '';
$title = e($property['title'] ?? 'Property #' . $property['id']);
$price = formatPriceWithDeal($property['price'], $property['currency'], $property['deal_type']);
$link = BASE_URL . '/listings/' . e($property['slug']);

// Badge class
$dealBadgeClass = match($property['deal_type']) {
    'sale' => 'badge-sale',
    'rent' => 'badge-rent',
    'daily_rent' => 'badge-daily',
    default => 'badge-sale',
};
$dealLabel = getDealLabel($property['deal_type']);
?>

<article class="property-card">
    <a href="<?= $link ?>" class="property-card-image">
        <?php if ($mainImage): ?>
            <img src="<?= $mainImage ?>" alt="<?= $title ?>" loading="lazy" width="400" height="300">
        <?php else: ?>
            <div class="no-image"><i class="ph ph-image"></i></div>
        <?php endif; ?>
        
        <div class="property-card-badges">
            <span class="badge <?= $dealBadgeClass ?>"><?= $dealLabel ?></span>
            <span class="badge badge-sale"><?= getTypeLabel($property['type']) ?></span>
        </div>
        
        <?php if (!empty($property['is_featured'])): ?>
        <div class="property-card-featured">
            <span class="badge badge-featured">⭐ Featured</span>
        </div>
        <?php endif; ?>
    </a>
    
    <div class="property-card-body">
        <div class="property-card-price"><?= $price ?></div>
        
        <h3 class="property-card-title">
            <a href="<?= $link ?>"><?= $title ?></a>
        </h3>
        
        <div class="property-card-divider"></div>
        
        <div class="property-card-specs">
            <?php if (!empty($property['rooms'])): ?>
            <span class="property-card-spec">
                <i class="ph ph-bed"></i> <?= (int)$property['rooms'] ?>
            </span>
            <?php endif; ?>
            
            <?php if (!empty($property['area_m2'])): ?>
            <span class="property-card-spec">
                <i class="ph ph-ruler"></i> <?= (int)$property['area_m2'] ?> <?= __('sqm') ?>
            </span>
            <?php endif; ?>
            
            <?php if (!empty($property['sea_distance_m'])): ?>
            <span class="property-card-spec">
                <i class="ph ph-waves"></i> <?= (int)$property['sea_distance_m'] ?> <?= __('m') ?>
            </span>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($property['address'])): ?>
        <div class="property-card-address">
            <i class="ph ph-map-pin"></i>
            <?= e(truncate($property['address'], 40)) ?>
        </div>
        <?php endif; ?>
        
        <div class="property-card-time">
            <i class="ph ph-clock"></i>
            <?= timeAgo($property['created_at']) ?>
        </div>
    </div>
</article>
