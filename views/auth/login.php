<div class="auth-card">
    <div class="auth-header">
        <h1><?= __('login_title') ?></h1>
    </div>
    
    <?php if ($msg = getFlash('error')): ?>
    <div class="flash-message flash-error mb-4">
        <i class="ph ph-warning-circle"></i> <?= $msg ?>
    </div>
    <?php endif; ?>
    
    <?php if ($msg = getFlash('success')): ?>
    <div class="flash-message flash-success mb-4">
        <i class="ph ph-check-circle"></i> <?= $msg ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="<?= BASE_URL ?>/login">
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_email') ?></label>
            <input type="email" name="email" class="form-input" value="<?= e(old('email')) ?>" 
                   placeholder="name@example.com" required autofocus>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_password') ?></label>
            <input type="password" name="password" class="form-input" 
                   placeholder="••••••••" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-full btn-lg mt-4">
            <i class="ph ph-sign-in"></i> <?= __('btn_login') ?>
        </button>
    </form>
    
    <div class="auth-footer">
        <?= __('no_account') ?> 
        <a href="<?= BASE_URL ?>/register"><?= __('nav_register') ?></a>
    </div>
</div>
