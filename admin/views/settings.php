<div class="admin-header">
    <h1>Settings</h1>
</div>

<div class="table-wrap p-6">
    <form method="POST" action="<?= ADMIN_URL ?>/settings">
        <?= csrf_field() ?>
        
        <h3 class="mb-4">Contact Information</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-input" value="<?= e($settings['contact_phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-input" value="<?= e($settings['contact_email'] ?? '') ?>">
            </div>
        </div>
        
        <hr style="margin: var(--space-6) 0; border: none; border-top: 1px solid var(--border);">
        
        <h3 class="mb-4">Social Media</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Facebook URL</label>
                <input type="url" name="facebook_url" class="form-input" value="<?= e($settings['facebook_url'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Instagram URL</label>
                <input type="url" name="instagram_url" class="form-input" value="<?= e($settings['instagram_url'] ?? '') ?>">
            </div>
        </div>
        
        <hr style="margin: var(--space-6) 0; border: none; border-top: 1px solid var(--border);">
        
        <h3 class="mb-4">Site Settings</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Site Name (Georgian)</label>
                <input type="text" name="site_name_ka" class="form-input" value="<?= e($settings['site_name_ka'] ?? 'InfoKobuleti') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Site Name (English)</label>
                <input type="text" name="site_name_en" class="form-input" value="<?= e($settings['site_name_en'] ?? 'InfoKobuleti') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Google Analytics ID</label>
            <input type="text" name="ga_id" class="form-input" value="<?= e($settings['ga_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg mt-4">
            <i class="ph ph-floppy-disk"></i> Save Settings
        </button>
    </form>
</div>
