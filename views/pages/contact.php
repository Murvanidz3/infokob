<?php
/**
 * Contact Page
 */
$settings = new Setting();
?>

<div class="container">
    <div class="contact-layout">
        <!-- Contact Info Card -->
        <div class="contact-info-card">
            <h2><?= __('contact_info_title') ?></h2>
            
            <div class="contact-info-item">
                <div class="contact-info-icon"><i class="ph ph-phone"></i></div>
                <div>
                    <div class="font-semibold">ტელეფონი</div>
                    <div style="opacity: 0.8;"><?= e($settings->get('contact_phone')) ?></div>
                </div>
            </div>
            
            <div class="contact-info-item">
                <div class="contact-info-icon"><i class="ph ph-envelope"></i></div>
                <div>
                    <div class="font-semibold">ელ.ფოსტა</div>
                    <div style="opacity: 0.8;"><?= e($settings->get('contact_email')) ?></div>
                </div>
            </div>
            
            <div class="contact-info-item">
                <div class="contact-info-icon"><i class="ph ph-map-pin"></i></div>
                <div>
                    <div class="font-semibold">მისამართი</div>
                    <div style="opacity: 0.8;">Kobuleti, Adjara, Georgia</div>
                </div>
            </div>
            
            <div class="footer-social" style="margin-top: var(--space-8);">
                <?php if ($fb = $settings->get('facebook_url')): ?>
                <a href="<?= e($fb) ?>" target="_blank"><i class="ph ph-facebook-logo"></i></a>
                <?php endif; ?>
                <?php if ($ig = $settings->get('instagram_url')): ?>
                <a href="<?= e($ig) ?>" target="_blank"><i class="ph ph-instagram-logo"></i></a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Contact Form -->
        <div class="contact-form-card">
            <h2 style="margin-bottom: var(--space-6);"><?= __('contact_subtitle') ?></h2>
            
            <form method="POST" action="<?= BASE_URL ?>/contact">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label class="form-label"><?= __('contact_name') ?></label>
                    <input type="text" name="name" class="form-input" required value="<?= e(old('name')) ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label"><?= __('contact_email') ?></label>
                    <input type="email" name="email" class="form-input" required value="<?= e(old('email')) ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label"><?= __('contact_message') ?></label>
                    <textarea name="message" class="form-textarea" rows="5" required><?= e(old('message')) ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg btn-full">
                    <i class="ph ph-paper-plane-tilt"></i> <?= __('contact_send') ?>
                </button>
            </form>
        </div>
    </div>
</div>
