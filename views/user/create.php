<?php
/**
 * Create Listing Form (Simplified single-page form)
 */
$user = Auth::user();
?>

<h2 style="margin-bottom: var(--space-6);"><?= __('create_title') ?></h2>

<form method="POST" action="<?= BASE_URL ?>/my/listings/create" enctype="multipart/form-data" id="create-form">
    <?= csrf_field() ?>
    
    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-info"></i> <?= __('step_basic') ?></h3>
        
        <!-- Type Selection -->
        <div class="form-group">
            <label class="form-label"><?= __('field_type') ?> *</label>
            <div class="type-cards" x-data="{ selected: '<?= e(old('type', 'apartment')) ?>' }">
                <?php 
                $typeIcons = [
                    'apartment' => '🏢', 'house' => '🏠', 'cottage' => '🌿', 
                    'land' => '🌱', 'commercial' => '🏪', 'hotel_room' => '🏨'
                ];
                foreach (PROPERTY_TYPES as $type): ?>
                <label class="type-card" :class="{ active: selected === '<?= $type ?>' }" @click="selected = '<?= $type ?>'">
                    <input type="radio" name="type" value="<?= $type ?>" :checked="selected === '<?= $type ?>'">
                    <div class="type-card-icon"><?= $typeIcons[$type] ?? '🏠' ?></div>
                    <div class="type-card-label"><?= getTypeLabel($type) ?></div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Deal Type -->
        <div class="form-group">
            <label class="form-label"><?= __('field_deal_type') ?> *</label>
            <div class="deal-toggles" x-data="{ deal: '<?= e(old('deal_type', 'sale')) ?>' }">
                <label class="deal-toggle" :class="{ active: deal === 'sale' }" @click="deal = 'sale'">
                    <input type="radio" name="deal_type" value="sale" :checked="deal === 'sale'">
                    💰 <?= __('deal_sale') ?>
                </label>
                <label class="deal-toggle" :class="{ active: deal === 'rent' }" @click="deal = 'rent'">
                    <input type="radio" name="deal_type" value="rent" :checked="deal === 'rent'">
                    🔑 <?= __('deal_rent') ?>
                </label>
                <label class="deal-toggle" :class="{ active: deal === 'daily_rent' }" @click="deal = 'daily_rent'">
                    <input type="radio" name="deal_type" value="daily_rent" :checked="deal === 'daily_rent'">
                    📅 <?= __('deal_daily_rent') ?>
                </label>
            </div>
        </div>
        
        <!-- Price -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_price') ?> *</label>
                <input type="number" name="price" class="form-input" value="<?= e(old('price')) ?>" min="0" step="0.01" required>
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_currency') ?></label>
                <select name="currency" class="form-select">
                    <option value="USD">$ USD</option>
                    <option value="GEL">₾ GEL</option>
                    <option value="EUR">€ EUR</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-check">
                <input type="checkbox" name="price_negotiable" value="1">
                <?= __('field_negotiable') ?>
            </label>
        </div>
        
        <!-- Dimensions -->
        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label"><?= __('field_area') ?></label>
                <input type="number" name="area_m2" class="form-input" value="<?= e(old('area_m2')) ?>" min="0">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_rooms') ?></label>
                <input type="number" name="rooms" class="form-input" value="<?= e(old('rooms')) ?>" min="0" max="50">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_bedrooms') ?></label>
                <input type="number" name="bedrooms" class="form-input" value="<?= e(old('bedrooms')) ?>" min="0" max="50">
            </div>
        </div>
        
        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label"><?= __('field_bathrooms') ?></label>
                <input type="number" name="bathrooms" class="form-input" value="<?= e(old('bathrooms')) ?>" min="0" max="20">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_floor') ?></label>
                <input type="number" name="floor_number" class="form-input" value="<?= e(old('floor_number')) ?>" min="0">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_floors_total') ?></label>
                <input type="number" name="floors_total" class="form-input" value="<?= e(old('floors_total')) ?>" min="0">
            </div>
        </div>
    </div>
    
    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-map-pin"></i> <?= __('step_details') ?></h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_address') ?></label>
                <input type="text" name="address" class="form-input" value="<?= e(old('address')) ?>" placeholder="ქობულეთი, ...">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_district') ?></label>
                <select name="district" class="form-select">
                    <option value=""><?= __('all_districts') ?></option>
                    <?php foreach (DISTRICTS as $key => $name): ?>
                    <option value="<?= $key ?>" <?= old('district') === $key ? 'selected' : '' ?>><?= __('district_' . $key) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_sea_distance') ?></label>
            <select name="sea_distance_m" class="form-select">
                <option value=""><?= __('filter_any') ?></option>
                <?php foreach (SEA_DISTANCES as $dist => $label): ?>
                <option value="<?= $dist ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Features -->
        <div class="form-group">
            <label class="form-label"><?= __('field_features') ?></label>
            <div class="filter-checks">
                <label class="filter-check"><input type="checkbox" name="has_pool" value="1"> 🏊 <?= __('feature_pool') ?></label>
                <label class="filter-check"><input type="checkbox" name="has_garden" value="1"> 🌳 <?= __('feature_garden') ?></label>
                <label class="filter-check"><input type="checkbox" name="has_balcony" value="1"> 🏗 <?= __('feature_balcony') ?></label>
                <label class="filter-check"><input type="checkbox" name="has_garage" value="1"> 🚗 <?= __('feature_garage') ?></label>
                <label class="filter-check"><input type="checkbox" name="has_furniture" value="1"> 🛋 <?= __('feature_furniture') ?></label>
            </div>
        </div>
        
        <!-- Map coordinates (hidden inputs) -->
        <input type="hidden" name="lat" id="lat" value="<?= e(old('lat', '41.8114')) ?>">
        <input type="hidden" name="lng" id="lng" value="<?= e(old('lng', '41.7700')) ?>">
        
        <!-- Title & Description -->
        <div class="form-group">
            <label class="form-label"><?= __('field_title') ?> * (<?= Language::get() === 'ka' ? 'ქართულად' : Language::get() ?>)</label>
            <input type="text" name="title" class="form-input" value="<?= e(old('title')) ?>" required 
                   placeholder="მაგ: 2-ოთახიანი ბინა ჩაქვში, ზღვის ხედით">
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_description') ?></label>
            <textarea name="description" class="form-textarea" rows="5" 
                      placeholder="აღწერეთ ქონება დეტალურად..."><?= e(old('description')) ?></textarea>
        </div>
        
        <!-- Contact -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_contact_name') ?></label>
                <input type="text" name="contact_name" class="form-input" value="<?= e(old('contact_name', $user['name'])) ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_contact_phone') ?> *</label>
                <input type="tel" name="contact_phone" class="form-input" value="<?= e(old('contact_phone', $user['phone'])) ?>" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">WhatsApp</label>
                <input type="tel" name="contact_whatsapp" class="form-input" value="<?= e(old('contact_whatsapp', $user['whatsapp_number'] ?? '')) ?>" placeholder="+995XXXXXXXXX">
            </div>
            <div class="form-group">
                <label class="form-label">Telegram</label>
                <input type="text" name="contact_telegram" class="form-input" value="<?= e(old('contact_telegram', $user['telegram_username'] ?? '')) ?>" placeholder="@username">
            </div>
        </div>
    </div>
    
    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-camera"></i> <?= __('step_photos') ?></h3>
        
        <div class="upload-zone" id="upload-zone">
            <div class="upload-zone-icon"><i class="ph ph-cloud-arrow-up"></i></div>
            <div class="upload-zone-text"><?= __('upload_title') ?></div>
            <div class="upload-zone-hint"><?= __('upload_hint') ?></div>
            <input type="file" name="images[]" id="file-input" multiple accept="image/*" style="display: none;">
        </div>
        
        <div class="upload-preview-grid" id="preview-grid"></div>
    </div>
    
    <!-- Submit -->
    <div class="flex justify-between items-center mb-8">
        <a href="<?= BASE_URL ?>/my/dashboard" class="btn btn-ghost">
            <i class="ph ph-arrow-left"></i> <?= __('btn_back') ?>
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="ph ph-paper-plane-tilt"></i> <?= __('btn_publish') ?>
        </button>
    </div>
</form>
