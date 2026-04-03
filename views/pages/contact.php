<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
$phone = Setting::get('contact_phone');
$emailSetting = Setting::get('contact_email');
?>
<div class="container contact-page">
    <div class="contact-grid">
        <div>
            <h1 class="page-title"><?= Helpers::e(Helpers::__('contact_h1')) ?></h1>
            <p class="page-lead"><?= Helpers::e(Helpers::__('contact_lead')) ?></p>
            <?php if ($phone !== ''): ?>
                <p class="contact-line"><i class="ph ph-phone"></i> <a href="tel:<?= Helpers::e(preg_replace('/\s+/', '', $phone)) ?>"><?= Helpers::e($phone) ?></a></p>
            <?php endif; ?>
            <?php if ($emailSetting !== ''): ?>
                <p class="contact-line"><i class="ph ph-envelope"></i> <a href="mailto:<?= Helpers::e($emailSetting) ?>"><?= Helpers::e($emailSetting) ?></a></p>
            <?php endif; ?>
        </div>
        <div class="contact-form-card">
            <form method="post" action="<?= Helpers::e(BASE_URL) ?>/contact" class="form-stack">
                <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                <label class="form-label"><?= Helpers::e(Helpers::__('contact_form_name')) ?></label>
                <input class="input" type="text" name="name" required maxlength="255" autocomplete="name">

                <label class="form-label"><?= Helpers::e(Helpers::__('contact_form_email')) ?></label>
                <input class="input" type="email" name="email" required maxlength="255" autocomplete="email">

                <label class="form-label"><?= Helpers::e(Helpers::__('contact_form_message')) ?></label>
                <textarea class="input input--area" name="message" required minlength="10" rows="5"></textarea>

                <button type="submit" class="btn btn--primary btn--pill"><?= Helpers::e(Helpers::__('btn_submit')) ?></button>
            </form>
        </div>
    </div>
</div>
