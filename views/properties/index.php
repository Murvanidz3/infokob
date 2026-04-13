<?php
/**
 * Listings Page View 
 * Filter sidebar + property grid with AJAX support
 */
?>

<div class="container">
    <div class="listings-layout">
        
        <!-- ═══ FILTERS SIDEBAR ═══ -->
        <aside class="filters-sidebar" id="filters-sidebar" x-data="{ open: false }">
            <div class="filter-toggle-bar" @click="open = !open"></div>
            
            <div class="filter-header">
                <h3><i class="ph ph-funnel"></i> <?= __('filter_title') ?></h3>
                <button type="button" class="btn btn-ghost btn-sm" id="clear-filters">
                    <?= __('btn_clear_filters') ?>
                </button>
            </div>
            
            <form id="filter-form">
                <!-- Deal Type -->
                <div class="filter-group">
                    <div class="filter-group-label"><?= __('filter_deal') ?></div>
                    <div class="filter-pills" id="deal-type-pills">
                        <button type="button" class="filter-pill <?= empty($filters['deal_type']) ? 'active' : '' ?>" data-value="">
                            <?= __('filter_all') ?>
                        </button>
                        <button type="button" class="filter-pill <?= ($filters['deal_type'] ?? '') === 'sale' ? 'active' : '' ?>" data-value="sale">
                            <?= __('deal_sale') ?>
                        </button>
                        <button type="button" class="filter-pill <?= ($filters['deal_type'] ?? '') === 'rent' ? 'active' : '' ?>" data-value="rent">
                            <?= __('deal_rent') ?>
                        </button>
                        <button type="button" class="filter-pill <?= ($filters['deal_type'] ?? '') === 'daily_rent' ? 'active' : '' ?>" data-value="daily_rent">
                            <?= __('deal_daily_rent') ?>
                        </button>
                    </div>
                    <input type="hidden" name="deal_type" id="deal_type" value="<?= e($filters['deal_type'] ?? '') ?>">
                </div>
                
                <!-- Property Type -->
                <div class="filter-group">
                    <div class="filter-group-label"><?= __('filter_type') ?></div>
                    <div class="filter-checks">
                        <?php foreach (PROPERTY_TYPES as $type): ?>
                        <label class="filter-check <?= ($filters['type'] ?? '') === $type ? 'active' : '' ?>">
                            <input type="checkbox" name="type[]" value="<?= $type ?>" 
                                   <?= ($filters['type'] ?? '') === $type ? 'checked' : '' ?>>
                            <?= getTypeLabel($type) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Price Range -->
                <div class="filter-group">
                    <div class="filter-group-label"><?= __('filter_price') ?> (USD)</div>
                    <div class="filter-price-row">
                        <input type="number" name="price_min" placeholder="<?= __('from') ?>" min="0" 
                               value="<?= e($filters['price_min'] ?? '') ?>">
                        <span>—</span>
                        <input type="number" name="price_max" placeholder="<?= __('to') ?>" min="0"
                               value="<?= e($filters['price_max'] ?? '') ?>">
                    </div>
                </div>
                
                <!-- Rooms -->
                <div class="filter-group">
                    <div class="filter-group-label"><?= __('filter_rooms') ?></div>
                    <div class="filter-pills" id="rooms-pills">
                        <button type="button" class="filter-pill <?= empty($filters['rooms']) ? 'active' : '' ?>" data-value="">
                            <?= __('filter_any') ?>
                        </button>
                        <?php for ($r = 1; $r <= 4; $r++): ?>
                        <button type="button" class="filter-pill <?= ($filters['rooms'] ?? '') == $r ? 'active' : '' ?>" data-value="<?= $r ?>">
                            <?= $r ?>
                        </button>
                        <?php endfor; ?>
                        <button type="button" class="filter-pill <?= ($filters['rooms'] ?? '') == '5' ? 'active' : '' ?>" data-value="5">
                            5+
                        </button>
                    </div>
                    <input type="hidden" name="rooms" id="rooms" value="<?= e($filters['rooms'] ?? '') ?>">
                </div>
                
                <!-- Sea Distance -->
                <div class="filter-group">
                    <div class="filter-group-label"><?= __('filter_sea') ?></div>
                    <select name="sea_distance" class="form-select">
                        <option value=""><?= __('filter_any') ?></option>
                        <?php foreach (SEA_DISTANCES as $dist => $label): ?>
                        <option value="<?= $dist ?>" <?= ($filters['sea_distance'] ?? '') == $dist ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- District -->
                <div class="filter-group">
                    <div class="filter-group-label"><?= __('filter_district') ?></div>
                    <select name="district" class="form-select">
                        <option value=""><?= __('all_districts') ?></option>
                        <?php foreach (DISTRICTS as $key => $name): ?>
                        <option value="<?= $key ?>" <?= ($filters['district'] ?? '') === $key ? 'selected' : '' ?>>
                            <?= __('district_' . $key) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Filter Actions -->
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="ph ph-magnifying-glass"></i> <?= __('btn_search') ?>
                    </button>
                </div>
            </form>
        </aside>
        
        <!-- ═══ RESULTS ═══ -->
        <div class="listings-results">
            <!-- Results Bar -->
            <div class="results-bar">
                <div class="results-count">
                    <?= __('listings_found') ?> <span id="results-total"><?= $pagination['total_items'] ?></span> <?= __('listings_found_suffix') ?>
                </div>
                <div class="results-sort">
                    <select id="sort-select" name="sort">
                        <option value="newest" <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>><?= __('sort_newest') ?></option>
                        <option value="cheapest" <?= ($filters['sort'] ?? '') === 'cheapest' ? 'selected' : '' ?>><?= __('sort_cheapest') ?></option>
                        <option value="expensive" <?= ($filters['sort'] ?? '') === 'expensive' ? 'selected' : '' ?>><?= __('sort_expensive') ?></option>
                        <option value="popular" <?= ($filters['sort'] ?? '') === 'popular' ? 'selected' : '' ?>><?= __('sort_popular') ?></option>
                    </select>
                </div>
            </div>
            
            <!-- Property Grid -->
            <div class="property-grid" id="property-grid">
                <?php if (!empty($properties)): ?>
                    <?php foreach ($properties as $property): ?>
                        <?php require VIEW_PATH . '/partials/property-card.php'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted" style="grid-column: 1/-1; padding: var(--space-12) 0;">
                        <i class="ph ph-magnifying-glass" style="font-size: 3rem; display: block; margin-bottom: var(--space-4);"></i>
                        <h3><?= __('no_results') ?></h3>
                        <p><?= __('no_results_desc') ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <div id="pagination-wrap">
                <?php $baseUrl = BASE_URL . '/listings'; ?>
                <?php require VIEW_PATH . '/partials/pagination.php'; ?>
            </div>
        </div>
    </div>
</div>
