<?php
/**
 * User Profile Page
 */
?>

<h2 style="margin-bottom: var(--space-6);"><?= __('nav_profile') ?></h2>

<div class="table-wrap p-6">
    <form method="POST" action="<?= BASE_URL ?>/my/profile">
        <?= csrf_field() ?>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_name') ?></label>
                <input type="text" name="name" class="form-input" value="<?= e($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_email') ?></label>
                <input type="email" class="form-input" value="<?= e($user['email']) ?>" disabled>
                <span class="form-hint">ელ.ფოსტის შეცვლა შეუძლებელია</span>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label"><?= __('field_phone') ?></label>
            <input type="tel" name="phone" class="form-input" value="<?= e($user['phone']) ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label"><?= __('field_whatsapp') ?></label>
                <input type="tel" name="whatsapp_number" class="form-input" value="<?= e($user['whatsapp_number']) ?>" placeholder="+995XXXXXXXXX">
            </div>
            <div class="form-group">
                <label class="form-label"><?= __('field_telegram') ?></label>
                <input type="text" name="telegram_username" class="form-input" value="<?= e($user['telegram_username']) ?>" placeholder="@username">
            </div>
        </div>
        
        <hr style="margin: var(--space-6) 0; border: none; border-top: 1px solid var(--border);">
        
        <h4 style="margin-bottom: var(--space-4);">პაროლის შეცვლა</h4>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">ახალი პაროლი</label>
                <input type="password" name="new_password" class="form-input" placeholder="დატოვეთ ცარიელი თუ არ გსურთ ცვლილება">
            </div>
            <div class="form-group">
                <label class="form-label">გაიმეორეთ პაროლი</label>
                <input type="password" name="new_password_confirm" class="form-input">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg mt-4">
            <i class="ph ph-floppy-disk"></i> <?= __('btn_save') ?>
        </button>
    </form>
</div>
