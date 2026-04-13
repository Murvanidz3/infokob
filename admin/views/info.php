<div class="admin-header">
    <h1>Kobuleti Info — CMS</h1>
</div>

<!-- Existing Sections -->
<?php foreach ($sections as $section): ?>
<div class="table-wrap p-6 mb-4">
    <form method="POST" action="<?= ADMIN_URL ?>/info">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" value="<?= $section['id'] ?>">
        
        <div class="flex justify-between items-center mb-4">
            <h3>
                <span class="badge badge-active"><?= strtoupper($section['lang']) ?></span>
                <?= e($section['title']) ?>
            </h3>
        </div>
        
        <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-input" value="<?= e($section['title']) ?>">
        </div>
        
        <div class="form-group">
            <label class="form-label">Content (HTML allowed)</label>
            <textarea name="content" class="form-textarea" rows="8"><?= e($section['content']) ?></textarea>
        </div>
        
        <div class="flex justify-between">
            <button type="submit" class="btn btn-primary btn-sm"><i class="ph ph-floppy-disk"></i> Save</button>
        </div>
    </form>
    
    <form method="POST" action="<?= ADMIN_URL ?>/info" style="margin-top: var(--space-4);"
          onsubmit="return confirm('Delete this section?')">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $section['id'] ?>">
        <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--danger);">
            <i class="ph ph-trash"></i> Delete Section
        </button>
    </form>
</div>
<?php endforeach; ?>

<!-- Add New Section -->
<div class="table-wrap p-6">
    <h3 class="mb-4">➕ Add New Section</h3>
    
    <form method="POST" action="<?= ADMIN_URL ?>/info">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create">
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Language</label>
                <select name="lang" class="form-select">
                    <option value="ka">Georgian</option>
                    <option value="ru">Russian</option>
                    <option value="en">English</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-input" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Content (HTML allowed)</label>
            <textarea name="content" class="form-textarea" rows="8" placeholder="<p>Write about Kobuleti...</p>"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="ph ph-plus"></i> Add Section
        </button>
    </form>
</div>
