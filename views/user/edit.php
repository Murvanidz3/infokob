<?php
/**
 * Edit Listing Form
 * Pre-filled with existing property data
 */
$trans = $property['translations']['ka'] ?? ['title' => '', 'description' => ''];
?>

<h2 style="margin-bottom: var(--space-6);"><?= __('edit_title') ?></h2>

<form method="POST" action="<?= BASE_URL ?>/my/listings/<?= $property['id'] ?>/edit" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-info"></i> <?= __('step_basic') ?></h3>
        
        <!-- Type -->
        <div class="form-group">
            <label class="form-label"><?= __('field_type') ?></label>
            <select name="type" class="form-select">
                <?php foreach (PROPERTY_TYPES as $type): ?>
                <option value="<?= $type ?>" <?= $property['type'] === $type ? 'selected' : '' ?>><?= getTypeLabel($type) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_deal_type') ?></label>
            <select name="deal_type" class="form-select">
                <?php foreach (DEAL_TYPES as $dt): ?>
                <option value="<?= $dt ?>" <?= $property['deal_type'] === $dt ? 'selected' : '' ?>><?= getDealLabel($dt) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_price') ?></label>
                <input type="number" name="price" class="form-input" value="<?= e($property['price']) ?>" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_currency') ?></label>
                <select name="currency" class="form-select">
                    <?php foreach (CURRENCIES as $c): ?>
                    <option value="<?= $c ?>" <?= $property['currency'] === $c ? 'selected' : '' ?>><?= getCurrencySymbol($c) ?> <?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <label class="form-check mb-4">
            <input type="checkbox" name="price_negotiable" value="1" <?= $property['price_negotiable'] ? 'checked' : '' ?>>
            <?= __('field_negotiable') ?>
        </label>
        
        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label"><?= __('field_area') ?></label>
                <input type="number" name="area_m2" class="form-input" value="<?= e($property['area_m2']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_rooms') ?></label>
                <input type="number" name="rooms" class="form-input" value="<?= e($property['rooms']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_bedrooms') ?></label>
                <input type="number" name="bedrooms" class="form-input" value="<?= e($property['bedrooms']) ?>">
            </div>
        </div>
        
        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label"><?= __('field_bathrooms') ?></label>
                <input type="number" name="bathrooms" class="form-input" value="<?= e($property['bathrooms']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_floor') ?></label>
                <input type="number" name="floor_number" class="form-input" value="<?= e($property['floor_number']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_floors_total') ?></label>
                <input type="number" name="floors_total" class="form-input" value="<?= e($property['floors_total']) ?>">
            </div>
        </div>
    </div>
    
    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-map-pin"></i> <?= __('step_details') ?></h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_address') ?></label>
                <input type="text" name="address" class="form-input" value="<?= e($property['address']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_district') ?></label>
                <select name="district" class="form-select">
                    <option value=""><?= __('all_districts') ?></option>
                    <?php foreach (DISTRICTS as $key => $name): ?>
                    <option value="<?= $key ?>" <?= $property['district'] === $key ? 'selected' : '' ?>><?= __('district_' . $key) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_sea_distance') ?></label>
            <select name="sea_distance_m" class="form-select">
                <option value=""><?= __('filter_any') ?></option>
                <?php foreach (SEA_DISTANCES as $dist => $label): ?>
                <option value="<?= $dist ?>" <?= $property['sea_distance_m'] == $dist ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_features') ?></label>
            <div class="filter-checks">
                <label class="filter-check"><input type="checkbox" name="has_pool" value="1" <?= $property['has_pool'] ? 'checked' : '' ?>> <?= __('feature_pool') ?></label>
                <label class="filter-check"><input type="checkbox" name="has_garden" value="1" <?= $property['has_garden'] ? 'checked' : '' ?>> <?= __('feature_garden') ?></label>
                <label class="filter-check"><input type="checkbox" name="has_balcony" value="1" <?= $property['has_balcony'] ? 'checked' : '' ?>> <?= __('feature_balcony') ?></label>
                <label class="filter-check"><input type="checkbox" name="has_garage" value="1" <?= $property['has_garage'] ? 'checked' : '' ?>> <?= __('feature_garage') ?></label>
                <label class="filter-check"><input type="checkbox" name="has_furniture" value="1" <?= $property['has_furniture'] ? 'checked' : '' ?>> <?= __('feature_furniture') ?></label>
            </div>
        </div>
        
        <input type="hidden" name="lat" value="<?= e($property['lat']) ?>">
        <input type="hidden" name="lng" value="<?= e($property['lng']) ?>">
        
        <div class="form-group">
            <label class="form-label"><?= __('field_title') ?></label>
            <input type="text" name="title" class="form-input" value="<?= e($trans['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_description') ?></label>
            <textarea name="description" class="form-textarea" rows="5"><?= e($trans['description']) ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_contact_name') ?></label>
                <input type="text" name="contact_name" class="form-input" value="<?= e($property['contact_name']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_contact_phone') ?></label>
                <input type="tel" name="contact_phone" class="form-input" value="<?= e($property['contact_phone']) ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">WhatsApp</label>
                <input type="tel" name="contact_whatsapp" class="form-input" value="<?= e($property['contact_whatsapp']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Telegram</label>
                <input type="text" name="contact_telegram" class="form-input" value="<?= e($property['contact_telegram']) ?>">
            </div>
        </div>
    </div>
    
    <!-- Existing Images -->
    <?php if (!empty($property['images'])): ?>
    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4">არსებული ფოტოები</h3>
        <div class="upload-preview-grid">
            <?php foreach ($property['images'] as $img): ?>
            <div class="upload-preview-item">
                <img src="<?= getImageUrl($img['filename'], 'thumb') ?>" alt="">
                <?php if ($img['is_main']): ?>
                <span class="main-badge">⭐</span>
                <?php endif; ?>
                <label class="remove-btn existing-remove-btn" title="წაშლა">
                    <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" style="display:none">
                    <i class="ph ph-x"></i>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- New Images -->
    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-camera"></i> ახალი ფოტოების დამატება</h3>
        <div class="upload-zone" id="upload-zone">
            <div class="upload-zone-icon"><i class="ph ph-cloud-arrow-up"></i></div>
            <div class="upload-zone-text"><?= __('upload_title') ?></div>
            <div class="upload-zone-hint"><?= __('upload_hint') ?></div>
            <input type="file" name="images[]" id="file-input" multiple accept="image/*" style="display: none;">
        </div>
        <div class="upload-preview-grid" id="preview-grid"></div>
    </div>
    
    <div class="flex justify-between items-center mb-8">
        <a href="<?= BASE_URL ?>/my/dashboard" class="btn btn-ghost">
            <i class="ph ph-arrow-left"></i> <?= __('btn_back') ?>
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="ph ph-floppy-disk"></i> <?= __('btn_save') ?>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.existing-remove-btn').forEach(function (btn) {
        const checkbox = btn.querySelector('input[type="checkbox"]');
        const card = btn.closest('.upload-preview-item');
        if (!checkbox || !card) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            checkbox.checked = !checkbox.checked;
            card.classList.toggle('marked-delete', checkbox.checked);
            btn.classList.toggle('active', checkbox.checked);
        });
    });
});
</script>
