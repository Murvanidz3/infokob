<?php
/**
 * Admin listing edit form
 * Allows admins to edit any listing.
 */
$trans = $property['translations']['ka'] ?? ['title' => '', 'description' => ''];
?>

<div class="admin-header">
    <h1>Edit Listing #<?= (int)$property['id'] ?></h1>
    <a href="<?= ADMIN_URL ?>/listings" class="btn btn-ghost btn-sm">
        <i class="ph ph-arrow-left"></i> Back to Listings
    </a>
</div>

<form method="POST" action="<?= ADMIN_URL ?>/listings/<?= (int)$property['id'] ?>/edit" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-info"></i> Basic Information</h3>

        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['pending', 'active', 'rejected', 'sold'] as $status): ?>
                    <option value="<?= $status ?>" <?= $property['status'] === $status ? 'selected' : '' ?>>
                        <?= ucfirst($status) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Property Type</label>
                <select name="type" class="form-select">
                    <?php foreach (PROPERTY_TYPES as $type): ?>
                    <option value="<?= $type ?>" <?= $property['type'] === $type ? 'selected' : '' ?>><?= getTypeLabel($type) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deal Type</label>
                <select name="deal_type" class="form-select">
                    <?php foreach (DEAL_TYPES as $dt): ?>
                    <option value="<?= $dt ?>" <?= $property['deal_type'] === $dt ? 'selected' : '' ?>><?= getDealLabel($dt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Price</label>
                <input type="number" name="price" class="form-input" value="<?= e($property['price']) ?>" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label">Currency</label>
                <select name="currency" class="form-select">
                    <?php foreach (CURRENCIES as $c): ?>
                    <option value="<?= $c ?>" <?= $property['currency'] === $c ? 'selected' : '' ?>><?= getCurrencySymbol($c) ?> <?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <label class="form-check">
                <input type="checkbox" name="price_negotiable" value="1" <?= !empty($property['price_negotiable']) ? 'checked' : '' ?>>
                Negotiable price
            </label>
            <label class="form-check">
                <input type="checkbox" name="is_featured" value="1" <?= !empty($property['is_featured']) ? 'checked' : '' ?>>
                Featured listing (30 days from now)
            </label>
        </div>

        <div class="form-group mt-4">
            <label class="form-label">Admin Note</label>
            <textarea name="admin_note" class="form-textarea" rows="3"><?= e($property['admin_note'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-ruler"></i> Specs</h3>

        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label">Area (m2)</label>
                <input type="number" name="area_m2" class="form-input" value="<?= e($property['area_m2']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Rooms</label>
                <input type="number" name="rooms" class="form-input" value="<?= e($property['rooms']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Bedrooms</label>
                <input type="number" name="bedrooms" class="form-input" value="<?= e($property['bedrooms']) ?>">
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label">Bathrooms</label>
                <input type="number" name="bathrooms" class="form-input" value="<?= e($property['bathrooms']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Floor Number</label>
                <input type="number" name="floor_number" class="form-input" value="<?= e($property['floor_number']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Total Floors</label>
                <input type="number" name="floors_total" class="form-input" value="<?= e($property['floors_total']) ?>">
            </div>
        </div>
    </div>

    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-map-pin"></i> Location & Content</h3>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-input" value="<?= e($property['address']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">District</label>
                <select name="district" class="form-select">
                    <option value="">All Districts</option>
                    <?php foreach (DISTRICTS as $key => $name): ?>
                    <option value="<?= $key ?>" <?= $property['district'] === $key ? 'selected' : '' ?>><?= __('district_' . $key) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Sea Distance</label>
            <select name="sea_distance_m" class="form-select">
                <option value="">Any</option>
                <?php foreach (SEA_DISTANCES as $dist => $label): ?>
                <option value="<?= $dist ?>" <?= (string)$property['sea_distance_m'] === (string)$dist ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Features</label>
            <div class="filter-checks">
                <label class="filter-check"><input type="checkbox" name="has_pool" value="1" <?= !empty($property['has_pool']) ? 'checked' : '' ?>> Pool</label>
                <label class="filter-check"><input type="checkbox" name="has_garden" value="1" <?= !empty($property['has_garden']) ? 'checked' : '' ?>> Garden</label>
                <label class="filter-check"><input type="checkbox" name="has_balcony" value="1" <?= !empty($property['has_balcony']) ? 'checked' : '' ?>> Balcony</label>
                <label class="filter-check"><input type="checkbox" name="has_garage" value="1" <?= !empty($property['has_garage']) ? 'checked' : '' ?>> Garage</label>
                <label class="filter-check"><input type="checkbox" name="has_furniture" value="1" <?= !empty($property['has_furniture']) ? 'checked' : '' ?>> Furniture</label>
            </div>
        </div>

        <input type="hidden" name="lat" value="<?= e($property['lat']) ?>">
        <input type="hidden" name="lng" value="<?= e($property['lng']) ?>">

        <div class="form-group">
            <label class="form-label">Title (KA)</label>
            <input type="text" name="title" class="form-input" value="<?= e($trans['title']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Description (KA)</label>
            <textarea name="description" class="form-textarea" rows="5"><?= e($trans['description']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Contact Name</label>
                <input type="text" name="contact_name" class="form-input" value="<?= e($property['contact_name']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Contact Phone</label>
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

    <?php if (!empty($property['images'])): ?>
    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4">Current Images</h3>
        <div class="upload-preview-grid">
            <?php foreach ($property['images'] as $img): ?>
            <div class="upload-preview-item">
                <img src="<?= getImageUrl($img['filename'], 'thumb') ?>" alt="">
                <?php if ($img['is_main']): ?>
                <span class="main-badge">MAIN</span>
                <?php endif; ?>
                <label class="form-check" style="position:absolute;bottom:8px;left:8px;background:rgba(0,0,0,.65);padding:4px 8px;border-radius:8px;">
                    <input type="checkbox" name="delete_images[]" value="<?= (int)$img['id'] ?>">
                    Delete
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="table-wrap p-6 mb-6">
        <h3 class="mb-4"><i class="ph ph-camera"></i> Add New Images</h3>
        <div class="upload-zone" id="upload-zone">
            <div class="upload-zone-icon"><i class="ph ph-cloud-arrow-up"></i></div>
            <div class="upload-zone-text">Upload images</div>
            <div class="upload-zone-hint">JPG / PNG up to 5MB each</div>
            <input type="file" name="images[]" id="file-input" multiple accept="image/*" style="display: none;">
        </div>
        <div class="upload-preview-grid" id="preview-grid"></div>
    </div>

    <div class="flex justify-between items-center mb-8">
        <a href="<?= ADMIN_URL ?>/listings" class="btn btn-ghost">
            <i class="ph ph-arrow-left"></i> Back
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="ph ph-floppy-disk"></i> Save Listing
        </button>
    </div>
</form>

<script src="<?= asset('js/upload-preview.js') ?>"></script>
