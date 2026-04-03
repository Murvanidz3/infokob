<?php

declare(strict_types=1);

$phone = Setting::get('contact_phone');
$email = Setting::get('contact_email');
$fb = Setting::get('facebook_url');
$ig = Setting::get('instagram_url');
?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div>
            <div class="site-logo site-logo--footer">
                <span class="site-logo__mark" aria-hidden="true">🌊</span>
                <span class="site-logo__text">InfoKobuleti</span>
            </div>
            <p class="site-footer__tagline"><?= Helpers::e(Helpers::__('footer_tagline')) ?></p>
        </div>
        <div>
            <h3 class="site-footer__h"><?= Helpers::e(Helpers::__('footer_links')) ?></h3>
            <ul class="site-footer__links">
                <li><a href="<?= Helpers::e(BASE_URL) ?>/listings"><?= Helpers::e(Helpers::__('nav_listings')) ?></a></li>
                <li><a href="<?= Helpers::e(BASE_URL) ?>/kobuleti"><?= Helpers::e(Helpers::__('nav_kobuleti')) ?></a></li>
                <li><a href="<?= Helpers::e(BASE_URL) ?>/contact"><?= Helpers::e(Helpers::__('nav_contact')) ?></a></li>
            </ul>
        </div>
        <div>
            <h3 class="site-footer__h"><?= Helpers::e(Helpers::__('footer_contact')) ?></h3>
            <?php if ($phone !== ''): ?>
                <p><a href="tel:<?= Helpers::e(preg_replace('/\s+/', '', $phone)) ?>"><?= Helpers::e($phone) ?></a></p>
            <?php endif; ?>
            <?php if ($email !== ''): ?>
                <p><a href="mailto:<?= Helpers::e($email) ?>"><?= Helpers::e($email) ?></a></p>
            <?php endif; ?>
            <div class="site-footer__social">
                <?php if ($fb !== ''): ?>
                    <a href="<?= Helpers::e($fb) ?>" rel="noopener noreferrer" target="_blank" aria-label="Facebook">FB</a>
                <?php endif; ?>
                <?php if ($ig !== ''): ?>
                    <a href="<?= Helpers::e($ig) ?>" rel="noopener noreferrer" target="_blank" aria-label="Instagram">IG</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            <span>© <?= date('Y') ?> InfoKobuleti · infokobuleti.com</span>
        </div>
    </div>
</footer>
