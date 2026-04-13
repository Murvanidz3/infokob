<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- About -->
            <div class="footer-col">
                <div class="footer-about">
                    <a href="<?= BASE_URL ?>" class="header-logo">
                        <span>🌊</span> InfoKobuleti
                    </a>
                    <p><?= __('footer_about') ?></p>
                </div>
            </div>
            
            <!-- Links -->
            <div class="footer-col">
                <h4><?= __('footer_links') ?></h4>
                <div class="footer-links">
                    <a href="<?= BASE_URL ?>/listings"><?= __('nav_listings') ?></a>
                    <a href="<?= BASE_URL ?>/kobuleti"><?= __('nav_kobuleti') ?></a>
                    <a href="<?= BASE_URL ?>/contact"><?= __('nav_contact') ?></a>
                    <a href="<?= BASE_URL ?>/register"><?= __('nav_register') ?></a>
                </div>
            </div>
            
            <!-- Contact -->
            <div class="footer-col">
                <h4><?= __('footer_contact') ?></h4>
                <div class="footer-contact-item">
                    <i class="ph ph-phone"></i>
                    <span><?= e((new Setting)->get('contact_phone', '+995 555 000 001')) ?></span>
                </div>
                <div class="footer-contact-item">
                    <i class="ph ph-envelope"></i>
                    <span><?= e((new Setting)->get('contact_email', 'info@infokobuleti.com')) ?></span>
                </div>
                <div class="footer-contact-item">
                    <i class="ph ph-map-pin"></i>
                    <span>Kobuleti, Georgia</span>
                </div>
            </div>
            
            <!-- Social -->
            <div class="footer-col">
                <h4><?= __('footer_social') ?></h4>
                <div class="footer-social">
                    <?php $fbUrl = (new Setting)->get('facebook_url'); ?>
                    <?php if ($fbUrl): ?>
                    <a href="<?= e($fbUrl) ?>" target="_blank" rel="noopener" aria-label="Facebook">
                        <i class="ph ph-facebook-logo"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php $igUrl = (new Setting)->get('instagram_url'); ?>
                    <?php if ($igUrl): ?>
                    <a href="<?= e($igUrl) ?>" target="_blank" rel="noopener" aria-label="Instagram">
                        <i class="ph ph-instagram-logo"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p><?= sprintf(__('footer_copyright'), date('Y')) ?></p>
        </div>
    </div>
</footer>
