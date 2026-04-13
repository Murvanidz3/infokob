<?php
/**
 * Single Property Detail Page
 * Gallery, specs, description, map, contact box, similar listings
 */

$images = $property['images'] ?? [];
$mainImageUrl = !empty($property['main_image']) ? getImageUrl($property['main_image'], 'original') : '';
?>

<div class="single-property">
    <div class="container">
        
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>"><?= __('breadcrumb_home') ?></a>
            <span>/</span>
            <a href="<?= BASE_URL ?>/listings"><?= __('nav_listings') ?></a>
            <span>/</span>
            <span><?= e(truncate($property['title'] ?? '', 50)) ?></span>
        </nav>
        
        <div class="single-layout">
            <!-- ═══ LEFT COLUMN ═══ -->
            <div class="single-main">
                <!-- Title & Price (mobile) -->
                <div class="hide-desktop mb-6">
                    <h1 style="font-size: var(--font-size-2xl); margin-bottom: var(--space-2);">
                        <?= e($property['title'] ?? '') ?>
                    </h1>
                    <div class="contact-price"><?= formatPriceWithDeal($property['price'], $property['currency'], $property['deal_type']) ?></div>
                    <?php if ($property['price_negotiable']): ?>
                    <span class="contact-negotiable"><?= __('property_price_negotiable') ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Gallery -->
                <div class="gallery" x-data="gallery()" x-init="init(<?= htmlspecialchars(json_encode(array_map(function($img) { return getImageUrl($img['filename'], 'original'); }, $images)), ENT_QUOTES) ?>)">
                    <div class="gallery-main" @click="openLightbox()">
                        <?php if ($mainImageUrl): ?>
                        <img :src="images[activeIndex] || '<?= $mainImageUrl ?>'" alt="<?= e($property['title'] ?? '') ?>" id="gallery-main-img">
                        <?php else: ?>
                        <div class="no-image"><i class="ph ph-image"></i></div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (count($images) > 1): ?>
                    <div class="gallery-thumbs">
                        <?php foreach ($images as $i => $img): ?>
                        <div class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>" 
                             @click="setActive(<?= $i ?>)"
                             :class="{ active: activeIndex === <?= $i ?> }">
                            <img src="<?= getImageUrl($img['filename'], 'thumb') ?>" alt="" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Title (desktop) -->
                <h1 class="hide-mobile hide-tablet" style="font-size: var(--font-size-2xl); margin: var(--space-6) 0 var(--space-2);">
                    <?= e($property['title'] ?? '') ?>
                </h1>
                
                <!-- Specs Grid -->
                <div class="specs-grid">
                    <?php if (!empty($property['rooms'])): ?>
                    <div class="spec-item">
                        <div class="spec-icon"><i class="ph ph-bed"></i></div>
                        <div class="spec-value"><?= (int)$property['rooms'] ?></div>
                        <div class="spec-label"><?= __('property_rooms') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($property['area_m2'])): ?>
                    <div class="spec-item">
                        <div class="spec-icon"><i class="ph ph-ruler"></i></div>
                        <div class="spec-value"><?= (int)$property['area_m2'] ?> <?= __('sqm') ?></div>
                        <div class="spec-label"><?= __('property_area') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($property['floor_number'])): ?>
                    <div class="spec-item">
                        <div class="spec-icon"><i class="ph ph-buildings"></i></div>
                        <div class="spec-value"><?= (int)$property['floor_number'] ?>/<?= (int)$property['floors_total'] ?></div>
                        <div class="spec-label"><?= __('property_floor') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($property['sea_distance_m'])): ?>
                    <div class="spec-item">
                        <div class="spec-icon"><i class="ph ph-waves"></i></div>
                        <div class="spec-value"><?= (int)$property['sea_distance_m'] ?> <?= __('m') ?></div>
                        <div class="spec-label"><?= __('property_sea') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($property['bedrooms'])): ?>
                    <div class="spec-item">
                        <div class="spec-icon"><i class="ph ph-bed"></i></div>
                        <div class="spec-value"><?= (int)$property['bedrooms'] ?></div>
                        <div class="spec-label"><?= __('property_bedrooms') ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($property['bathrooms'])): ?>
                    <div class="spec-item">
                        <div class="spec-icon"><i class="ph ph-shower"></i></div>
                        <div class="spec-value"><?= (int)$property['bathrooms'] ?></div>
                        <div class="spec-label"><?= __('property_bathrooms') ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Features -->
                <?php 
                $features = [];
                if ($property['has_pool']) $features[] = ['icon' => 'ph-swimming-pool', 'label' => __('feature_pool')];
                if ($property['has_garage']) $features[] = ['icon' => 'ph-garage', 'label' => __('feature_garage')];
                if ($property['has_balcony']) $features[] = ['icon' => 'ph-door', 'label' => __('feature_balcony')];
                if ($property['has_garden']) $features[] = ['icon' => 'ph-tree', 'label' => __('feature_garden')];
                if ($property['has_furniture']) $features[] = ['icon' => 'ph-armchair', 'label' => __('feature_furniture')];
                ?>
                
                <?php if (!empty($features)): ?>
                <h3 style="margin-bottom: var(--space-4);"><?= __('property_features') ?></h3>
                <div class="feature-tags mb-6">
                    <?php foreach ($features as $f): ?>
                    <span class="feature-tag">
                        <i class="ph <?= $f['icon'] ?>"></i> <?= $f['label'] ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Description -->
                <?php if (!empty($property['description'])): ?>
                <h3 style="margin-bottom: var(--space-4);"><?= __('property_description') ?></h3>
                <div class="info-content mb-8" style="max-width: 100%;">
                    <p><?= nl2br(e($property['description'])) ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Map -->
                <?php if (!empty($property['lat']) && !empty($property['lng'])): ?>
                <h3 style="margin-bottom: var(--space-4);"><?= __('property_location') ?></h3>
                <div class="map-container" id="property-map"></div>
                
                <script>
                function initMap() {
                    const position = { lat: <?= (float)$property['lat'] ?>, lng: <?= (float)$property['lng'] ?> };
                    const map = new google.maps.Map(document.getElementById('property-map'), {
                        center: position,
                        zoom: 15,
                        styles: [{ featureType: "poi", stylers: [{ visibility: "off" }] }]
                    });
                    new google.maps.Marker({ position, map, title: '<?= e($property['title'] ?? '') ?>' });
                }
                </script>
                <?php endif; ?>
            </div>
            
            <!-- ═══ RIGHT COLUMN — CONTACT BOX ═══ -->
            <div class="single-sidebar">
                <div class="contact-box">
                    <div class="contact-price">
                        <?= formatPriceWithDeal($property['price'], $property['currency'], $property['deal_type']) ?>
                    </div>
                    
                    <?php if ($property['price_negotiable']): ?>
                    <span class="contact-negotiable"><?= __('property_price_negotiable') ?></span>
                    <?php endif; ?>
                    
                    <div class="contact-summary">
                        <?= getTypeLabel($property['type']) ?> 
                        <?php if (!empty($property['rooms'])): ?>| <?= (int)$property['rooms'] ?> <?= __('property_rooms') ?><?php endif; ?>
                        <?php if (!empty($property['area_m2'])): ?>| <?= (int)$property['area_m2'] ?> <?= __('sqm') ?><?php endif; ?>
                        <?php if (!empty($property['district'])): ?>| <?= getDistrictLabel($property['district']) ?><?php endif; ?>
                    </div>
                    
                    <div class="contact-person">
                        <div class="contact-person-avatar">
                            <?= mb_substr($contactName, 0, 1) ?>
                        </div>
                        <div>
                            <div class="contact-person-name"><?= e($contactName) ?></div>
                        </div>
                    </div>
                    
                    <div class="contact-buttons">
                        <?php if (!empty($contactWhatsapp)): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $contactWhatsapp) ?>?text=<?= urlencode('გამარჯობა, მაინტერესებს თქვენი განცხადება infokobuleti.com-ზე: ' . ($property['title'] ?? '')) ?>" 
                           target="_blank" class="btn btn-whatsapp btn-full">
                            <i class="ph ph-whatsapp-logo"></i> <?= __('btn_whatsapp') ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($contactTelegram)): ?>
                        <a href="https://t.me/<?= e(ltrim($contactTelegram, '@')) ?>" 
                           target="_blank" class="btn btn-telegram btn-full">
                            <i class="ph ph-telegram-logo"></i> <?= __('btn_telegram') ?>
                        </a>
                        <?php endif; ?>
                        
                        <a href="tel:<?= e($contactPhone) ?>" class="btn btn-primary btn-full">
                            <i class="ph ph-phone"></i> <?= e($contactPhone) ?>
                        </a>
                    </div>
                    
                    <div class="contact-meta">
                        <span><i class="ph ph-eye"></i> <?= number_format($property['views']) ?> <?= __('property_views') ?></span>
                        <span><i class="ph ph-calendar"></i> <?= timeAgo($property['created_at']) ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ═══ SIMILAR LISTINGS ═══ -->
        <?php if (!empty($similar)): ?>
        <section class="section" style="padding-top: var(--space-12);">
            <div class="section-header">
                <h2 class="section-title"><?= __('property_similar') ?></h2>
            </div>
            <div class="property-grid">
                <?php foreach ($similar as $property): ?>
                    <?php require VIEW_PATH . '/partials/property-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" x-data="lightbox()">
    <button class="lightbox-close" @click="close()"><i class="ph ph-x"></i></button>
    <button class="lightbox-nav lightbox-prev" @click="prev()"><i class="ph ph-caret-left"></i></button>
    <img class="lightbox-img" :src="currentImage" alt="">
    <button class="lightbox-nav lightbox-next" @click="next()"><i class="ph ph-caret-right"></i></button>
</div>

<script>
function gallery() {
    return {
        images: [],
        activeIndex: 0,
        init(imgs) { this.images = imgs || []; },
        setActive(i) { this.activeIndex = i; },
        openLightbox() {
            window.dispatchEvent(new CustomEvent('open-lightbox', { detail: { images: this.images, index: this.activeIndex } }));
        }
    };
}

function lightbox() {
    return {
        images: [],
        index: 0,
        currentImage: '',
        show: false,
        init() {
            window.addEventListener('open-lightbox', (e) => {
                this.images = e.detail.images;
                this.index = e.detail.index;
                this.currentImage = this.images[this.index];
                this.show = true;
                this.$el.classList.add('active');
            });
        },
        close() { this.show = false; this.$el.classList.remove('active'); },
        prev() { this.index = (this.index - 1 + this.images.length) % this.images.length; this.currentImage = this.images[this.index]; },
        next() { this.index = (this.index + 1) % this.images.length; this.currentImage = this.images[this.index]; },
    };
}
</script>
