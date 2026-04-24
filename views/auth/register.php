<div class="auth-card">
    <div class="auth-header">
        <h1><?= __('register_title') ?></h1>
    </div>
    
    <?php if ($msg = getFlash('error')): ?>
    <div class="flash-message flash-error mb-4">
        <i class="ph ph-warning-circle"></i> <?= $msg ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="<?= BASE_URL ?>/register">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_name') ?> *</label>
            <input type="text" name="name" class="form-input" value="<?= e(old('name')) ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_email') ?> *</label>
            <input type="email" name="email" class="form-input" value="<?= e(old('email')) ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_phone') ?> *</label>
            <input type="tel" name="phone" class="form-input" value="<?= e(old('phone')) ?>" 
                   placeholder="+995 5XX XXX XXX" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_whatsapp') ?></label>
                <input type="tel" name="whatsapp_number" class="form-input" value="<?= e(old('whatsapp_number')) ?>"
                       placeholder="+995XXXXXXXXX">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_telegram') ?></label>
                <input type="text" name="telegram_username" class="form-input" value="<?= e(old('telegram_username')) ?>"
                       placeholder="@username">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_password') ?> *</label>
            <input type="password" name="password" class="form-input" placeholder="მინ. 6 სიმბოლო" required>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_password_confirm') ?> *</label>
            <input type="password" name="password_confirm" class="form-input" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-full btn-lg mt-4">
            <i class="ph ph-user-plus"></i> <?= __('btn_register') ?>
        </button>
    </form>
    
    <div class="auth-footer">
        <?= __('have_account') ?> 
        <a href="<?= BASE_URL ?>/login"><?= __('nav_login') ?></a>
    </div>
</div>
