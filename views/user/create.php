<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var array<string, mixed>|null $property */
/** @var array<string, array{title:string,description:string}>|null $translations */
/** @var list<array<string, mixed>> $images */
/** @var bool $edit */
/** @var array<string, mixed>|null $user */

$p = $property ?? [];
$tr = $translations ?? [];
$ka = $tr['ka'] ?? ['title' => '', 'description' => ''];
$edit = !empty($edit);
$action = ($edit && !empty($p['id'])) ? (BASE_URL . '/my/listings/' . (int) $p['id'] . '/edit') : (BASE_URL . '/my/listings/create');
$csrf = Helpers::csrfToken();

$typeVal = (string) ($p['type'] ?? 'apartment');
$dealVal = (string) ($p['deal_type'] ?? 'sale');
$currencyVal = (string) ($p['currency'] ?? 'USD');
?>
<div class="user-page" x-data="{ step: 1 }">
    <h1 class="user-page__title"><?= $edit ? Helpers::e(Helpers::__('user_edit_title')) : Helpers::e(Helpers::__('nav_add_listing')) ?></h1>

    <div class="stepper" aria-hidden="true">
        <div class="stepper__item" :class="step >= 1 ? 'is-active' : ''">1</div>
        <div class="stepper__line"></div>
        <div class="stepper__item" :class="step >= 2 ? 'is-active' : ''">2</div>
        <div class="stepper__line"></div>
        <div class="stepper__item" :class="step >= 3 ? 'is-active' : ''">3</div>
    </div>

    <form class="listing-form" method="post" action="<?= Helpers::e($action) ?>" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="csrf" value="<?= Helpers::e($csrf) ?>">

        <div class="listing-step" x-show="step === 1" x-cloak>
            <h2 class="listing-step__title"><?= Helpers::e(Helpers::__('user_step1')) ?></h2>
            <fieldset class="type-grid">
                <legend class="form-label"><?= Helpers::e(Helpers::__('filter_type')) ?></legend>
                <?php
                $types = ['apartment', 'house', 'cottage', 'land', 'commercial', 'hotel_room'];
                foreach ($types as $t):
                ?>
                    <label class="type-card">
                        <input type="radio" name="type" value="<?= Helpers::e($t) ?>" <?= $typeVal === $t ? 'checked' : '' ?>>
                        <span><?= Helpers::e(Helpers::__('type_' . $t)) ?></span>
                    </label>
                <?php endforeach; ?>
            </fieldset>

            <div class="form-field">
                <span class="form-label"><?= Helpers::e(Helpers::__('filter_deal')) ?></span>
                <div class="deal-row">
                    <label class="filter-radio"><input type="radio" name="deal_type" value="sale" <?= $dealVal === 'sale' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_sale')) ?></label>
                    <label class="filter-radio"><input type="radio" name="deal_type" value="rent" <?= $dealVal === 'rent' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_rent')) ?></label>
                    <label class="filter-radio"><input type="radio" name="deal_type" value="daily_rent" <?= $dealVal === 'daily_rent' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_daily')) ?></label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label class="form-label"><?= Helpers::e(Helpers::__('user_price')) ?></label>
                    <input class="input" type="number" name="price" step="0.01" min="0" value="<?= Helpers::e((string) ($p['price'] ?? '')) ?>">
                </div>
                <div class="form-field">
                    <label class="form-label"><?= Helpers::e(Helpers::__('user_currency')) ?></label>
                    <select class="input" name="currency">
                        <option value="USD" <?= $currencyVal === 'USD' ? 'selected' : '' ?>>USD</option>
                        <option value="GEL" <?= $currencyVal === 'GEL' ? 'selected' : '' ?>>GEL</option>
                        <option value="EUR" <?= $currencyVal === 'EUR' ? 'selected' : '' ?>>EUR</option>
                    </select>
                </div>
            </div>
            <label class="filter-check"><input type="checkbox" name="price_negotiable" value="1" <?= !empty($p['price_negotiable']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('price_negotiable_short')) ?></label>

            <div class="form-row">
                <div class="form-field">
                    <label class="form-label"><?= Helpers::e(Helpers::__('spec_area')) ?> (m²)</label>
                    <input class="input" type="number" name="area_m2" step="0.01" min="0" value="<?= Helpers::e((string) ($p['area_m2'] ?? '')) ?>">
                </div>
                <div class="form-field">
                    <label class="form-label"><?= Helpers::e(Helpers::__('spec_rooms')) ?></label>
                    <input class="input" type="number" name="rooms" min="0" value="<?= Helpers::e((string) ($p['rooms'] ?? '')) ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-field">
                    <label class="form-label"><?= Helpers::e(Helpers::__('user_bedrooms')) ?></label>
                    <input class="input" type="number" name="bedrooms" min="0" value="<?= Helpers::e((string) ($p['bedrooms'] ?? '')) ?>">
                </div>
                <div class="form-field">
                    <label class="form-label"><?= Helpers::e(Helpers::__('user_bathrooms')) ?></label>
                    <input class="input" type="number" name="bathrooms" min="0" value="<?= Helpers::e((string) ($p['bathrooms'] ?? '')) ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-field">
                    <label class="form-label"><?= Helpers::e(Helpers::__('user_floors_total')) ?></label>
                    <input class="input" type="number" name="floors_total" min="0" value="<?= Helpers::e((string) ($p['floors_total'] ?? '')) ?>">
                </div>
                <div class="form-field">
                    <label class="form-label"><?= Helpers::e(Helpers::__('user_floor')) ?></label>
                    <input class="input" type="number" name="floor_number" value="<?= Helpers::e((string) ($p['floor_number'] ?? '')) ?>">
                </div>
            </div>

            <div class="listing-step__nav">
                <span></span>
                <button type="button" class="btn btn--primary btn--pill" @click="step = 2"><?= Helpers::e(Helpers::__('user_next')) ?></button>
            </div>
        </div>

        <div class="listing-step" x-show="step === 2" x-cloak style="display:none;">
            <h2 class="listing-step__title"><?= Helpers::e(Helpers::__('user_step2')) ?></h2>

            <label class="form-label"><?= Helpers::e(Helpers::__('user_address')) ?></label>
            <input class="input" type="text" name="address" value="<?= Helpers::e((string) ($p['address'] ?? '')) ?>">

            <label class="form-label"><?= Helpers::e(Helpers::__('filter_district')) ?></label>
            <select class="input" name="district">
                <option value=""><?= Helpers::e(Helpers::__('filter_any')) ?></option>
                <option value="ჩაქვი" <?= (($p['district'] ?? '') === 'ჩაქვი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_chakvi')) ?></option>
                <option value="ცენტრი" <?= (($p['district'] ?? '') === 'ცენტრი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_center')) ?></option>
                <option value="სანახარებო" <?= (($p['district'] ?? '') === 'სანახარებო') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_sakhareb')) ?></option>
                <option value="ეკო-პარკი" <?= (($p['district'] ?? '') === 'ეკო-პარკი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_ecopark')) ?></option>
            </select>

            <label class="form-label"><?= Helpers::e(Helpers::__('filter_sea')) ?></label>
            <select class="input" name="sea_distance_m">
                <option value=""><?= Helpers::e(Helpers::__('filter_any')) ?></option>
                <?php $sd = $p['sea_distance_m'] ?? null; ?>
                <option value="50" <?= ($sd !== null && (int) $sd === 50) ? 'selected' : '' ?>>50 m</option>
                <option value="100" <?= ($sd !== null && (int) $sd === 100) ? 'selected' : '' ?>>100 m</option>
                <option value="200" <?= ($sd !== null && (int) $sd === 200) ? 'selected' : '' ?>>200 m</option>
                <option value="300" <?= ($sd !== null && (int) $sd === 300) ? 'selected' : '' ?>>300 m</option>
                <option value="500" <?= ($sd !== null && (int) $sd === 500) ? 'selected' : '' ?>>500 m</option>
                <option value="1000" <?= ($sd !== null && (int) $sd === 1000) ? 'selected' : '' ?>>1 km+</option>
            </select>

            <div class="form-row">
                <div class="form-field">
                    <label class="form-label">Lat</label>
                    <input class="input" type="text" name="lat" placeholder="41.82" value="<?= Helpers::e((string) ($p['lat'] ?? '')) ?>">
                </div>
                <div class="form-field">
                    <label class="form-label">Lng</label>
                    <input class="input" type="text" name="lng" placeholder="41.78" value="<?= Helpers::e((string) ($p['lng'] ?? '')) ?>">
                </div>
            </div>

            <fieldset class="comfort-grid">
                <legend class="form-label"><?= Helpers::e(Helpers::__('user_comfort')) ?></legend>
                <label class="filter-check"><input type="checkbox" name="has_pool" value="1" <?= !empty($p['has_pool']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('feat_pool')) ?></label>
                <label class="filter-check"><input type="checkbox" name="has_garden" value="1" <?= !empty($p['has_garden']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('feat_garden')) ?></label>
                <label class="filter-check"><input type="checkbox" name="has_balcony" value="1" <?= !empty($p['has_balcony']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('feat_balcony')) ?></label>
                <label class="filter-check"><input type="checkbox" name="has_garage" value="1" <?= !empty($p['has_garage']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('feat_garage')) ?></label>
            </fieldset>

            <label class="form-label"><?= Helpers::e(Helpers::__('user_listing_title')) ?></label>
            <input class="input" type="text" name="title" required maxlength="500" value="<?= Helpers::e($ka['title']) ?>">

            <label class="form-label"><?= Helpers::e(Helpers::__('user_listing_desc')) ?></label>
            <textarea class="input input--area" name="description" required minlength="50" rows="6"><?= Helpers::e($ka['description']) ?></textarea>

            <h3 class="listing-step__subtitle"><?= Helpers::e(Helpers::__('user_contact_section')) ?></h3>
            <label class="form-label"><?= Helpers::e(Helpers::__('user_contact_name')) ?></label>
            <input class="input" type="text" name="contact_name" value="<?= Helpers::e((string) ($p['contact_name'] ?? $user['name'] ?? '')) ?>">

            <label class="form-label"><?= Helpers::e(Helpers::__('user_contact_phone_req')) ?></label>
            <input class="input" type="text" name="contact_phone" required value="<?= Helpers::e((string) ($p['contact_phone'] ?? $user['phone'] ?? '')) ?>">

            <label class="form-label"><?= Helpers::e(Helpers::__('user_contact_wa')) ?></label>
            <input class="input" type="text" name="contact_whatsapp" value="<?= Helpers::e((string) ($p['contact_whatsapp'] ?? $user['whatsapp_number'] ?? '')) ?>">

            <label class="form-label"><?= Helpers::e(Helpers::__('user_contact_tg')) ?></label>
            <input class="input" type="text" name="contact_telegram" value="<?= Helpers::e((string) ($p['contact_telegram'] ?? $user['telegram_username'] ?? '')) ?>">

            <label class="form-label"><?= Helpers::e(Helpers::__('user_contact_email')) ?></label>
            <input class="input" type="email" name="contact_email" value="<?= Helpers::e((string) ($p['contact_email'] ?? $user['email'] ?? '')) ?>">

            <div class="listing-step__nav">
                <button type="button" class="btn btn--ghost btn--pill" @click="step = 1"><?= Helpers::e(Helpers::__('user_back')) ?></button>
                <button type="button" class="btn btn--primary btn--pill" @click="step = 3"><?= Helpers::e(Helpers::__('user_next')) ?></button>
            </div>
        </div>

        <div class="listing-step" x-show="step === 3" x-cloak style="display:none;">
            <h2 class="listing-step__title"><?= Helpers::e(Helpers::__('user_step3')) ?></h2>
            <p class="form-hint"><?= Helpers::e(Helpers::__('user_step3_hint')) ?></p>

            <?php if ($edit && $images !== []): ?>
                <div class="existing-images">
                    <span class="form-label"><?= Helpers::e(Helpers::__('user_existing_photos')) ?></span>
                    <?php foreach ($images as $img): ?>
                        <label class="existing-images__item">
                            <input type="checkbox" name="delete_images[]" value="<?= (int) ($img['id'] ?? 0) ?>">
                            <img src="<?= Helpers::e(Image::getImageUrl((string) ($img['filename'] ?? ''), 'thumb')) ?>" alt="" width="120" height="90" loading="lazy">
                            <span><?= Helpers::e(Helpers::__('user_delete_photo')) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label class="upload-zone">
                <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple <?= $edit ? '' : 'required' ?>>
                <span><?= Helpers::e(Helpers::__('user_drop_photos')) ?></span>
            </label>

            <div class="listing-step__nav">
                <button type="button" class="btn btn--ghost btn--pill" @click="step = 2"><?= Helpers::e(Helpers::__('user_back')) ?></button>
                <button type="submit" class="btn btn--accent btn--pill"><?= $edit ? Helpers::e(Helpers::__('user_save_listing')) : Helpers::e(Helpers::__('user_publish_listing')) ?></button>
            </div>
        </div>
    </form>
</div>
