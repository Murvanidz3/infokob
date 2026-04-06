<?php

declare(strict_types=1);

/** @var array<string, mixed> $property */
/** @var list<array<string, mixed>> $images */
/** @var array<string, array{title:string,description:string}> $translations */
$p = $property;
$id = (int) ($p['id'] ?? 0);
$ka = $translations['ka'] ?? ['title' => '', 'description' => ''];
?>
<h1 class="admin-page__title"><?= Helpers::e(Helpers::__('user_edit_title')) ?> #<?= $id ?></h1>
<p class="admin-page__lead">
    <a href="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>"><?= Helpers::e(Helpers::__('admin_property_title')) ?></a>
    · <a href="<?= Helpers::e(BASE_URL) ?>/properties"><?= Helpers::e(Helpers::__('admin_back_list')) ?></a>
</p>

<div class="admin-card">
    <form method="post" action="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>/edit" enctype="multipart/form-data" class="form-stack">
        <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">

        <label class="form-label" for="type"><?= Helpers::e(Helpers::__('filter_type')) ?></label>
        <select id="type" name="type" class="input admin-input">
            <?php foreach (['apartment', 'house', 'cottage', 'land', 'commercial', 'hotel_room'] as $t): ?>
                <option value="<?= Helpers::e($t) ?>" <?= (string) ($p['type'] ?? 'apartment') === $t ? 'selected' : '' ?>>
                    <?= Helpers::e(Helpers::__('type_' . $t)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label class="form-label"><?= Helpers::e(Helpers::__('filter_deal')) ?></label>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem">
            <label class="admin-check"><input type="radio" name="deal_type" value="sale" <?= (string) ($p['deal_type'] ?? 'sale') === 'sale' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_sale')) ?></label>
            <label class="admin-check"><input type="radio" name="deal_type" value="rent" <?= (string) ($p['deal_type'] ?? '') === 'rent' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_rent')) ?></label>
            <label class="admin-check"><input type="radio" name="deal_type" value="daily_rent" <?= (string) ($p['deal_type'] ?? '') === 'daily_rent' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_daily')) ?></label>
        </div>

        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem">
            <div>
                <label class="form-label" for="price"><?= Helpers::e(Helpers::__('user_price')) ?></label>
                <input id="price" class="input admin-input" type="number" name="price" min="0" step="0.01" value="<?= Helpers::e((string) ($p['price'] ?? '')) ?>">
            </div>
            <div>
                <label class="form-label" for="currency"><?= Helpers::e(Helpers::__('user_currency')) ?></label>
                <select id="currency" class="input admin-input" name="currency">
                    <?php foreach (['USD', 'GEL', 'EUR'] as $c): ?>
                        <option value="<?= $c ?>" <?= (string) ($p['currency'] ?? 'USD') === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <label class="admin-check"><input type="checkbox" name="price_negotiable" value="1" <?= !empty($p['price_negotiable']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('price_negotiable_short')) ?></label>

        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem">
            <div><label class="form-label">m²</label><input class="input admin-input" type="number" name="area_m2" step="0.01" min="0" value="<?= Helpers::e((string) ($p['area_m2'] ?? '')) ?>"></div>
            <div><label class="form-label"><?= Helpers::e(Helpers::__('spec_rooms')) ?></label><input class="input admin-input" type="number" name="rooms" min="0" value="<?= Helpers::e((string) ($p['rooms'] ?? '')) ?>"></div>
            <div><label class="form-label"><?= Helpers::e(Helpers::__('user_bedrooms')) ?></label><input class="input admin-input" type="number" name="bedrooms" min="0" value="<?= Helpers::e((string) ($p['bedrooms'] ?? '')) ?>"></div>
            <div><label class="form-label"><?= Helpers::e(Helpers::__('user_bathrooms')) ?></label><input class="input admin-input" type="number" name="bathrooms" min="0" value="<?= Helpers::e((string) ($p['bathrooms'] ?? '')) ?>"></div>
            <div><label class="form-label"><?= Helpers::e(Helpers::__('user_floors_total')) ?></label><input class="input admin-input" type="number" name="floors_total" min="0" value="<?= Helpers::e((string) ($p['floors_total'] ?? '')) ?>"></div>
            <div><label class="form-label"><?= Helpers::e(Helpers::__('user_floor')) ?></label><input class="input admin-input" type="number" name="floor_number" value="<?= Helpers::e((string) ($p['floor_number'] ?? '')) ?>"></div>
        </div>

        <label class="form-label" for="address"><?= Helpers::e(Helpers::__('user_address')) ?></label>
        <input id="address" class="input admin-input" type="text" name="address" value="<?= Helpers::e((string) ($p['address'] ?? '')) ?>">

        <label class="form-label" for="district"><?= Helpers::e(Helpers::__('filter_district')) ?></label>
        <select id="district" class="input admin-input" name="district">
            <option value=""><?= Helpers::e(Helpers::__('filter_any')) ?></option>
            <option value="ჩაქვი" <?= (($p['district'] ?? '') === 'ჩაქვი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_chakvi')) ?></option>
            <option value="ცენტრი" <?= (($p['district'] ?? '') === 'ცენტრი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_center')) ?></option>
            <option value="სანახარებო" <?= (($p['district'] ?? '') === 'სანახარებო') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_sakhareb')) ?></option>
            <option value="ეკო-პარკი" <?= (($p['district'] ?? '') === 'ეკო-პარკი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_ecopark')) ?></option>
        </select>

        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem">
            <div><label class="form-label">Lat</label><input class="input admin-input" type="text" name="lat" value="<?= Helpers::e((string) ($p['lat'] ?? '')) ?>"></div>
            <div><label class="form-label">Lng</label><input class="input admin-input" type="text" name="lng" value="<?= Helpers::e((string) ($p['lng'] ?? '')) ?>"></div>
        </div>

        <label class="form-label" for="title"><?= Helpers::e(Helpers::__('user_listing_title')) ?></label>
        <input id="title" class="input admin-input" type="text" name="title" required maxlength="500" value="<?= Helpers::e((string) $ka['title']) ?>">

        <label class="form-label" for="description"><?= Helpers::e(Helpers::__('user_listing_desc')) ?></label>
        <textarea id="description" class="input admin-input" name="description" required minlength="50" rows="6"><?= Helpers::e((string) $ka['description']) ?></textarea>

        <h3 class="admin-card__title"><?= Helpers::e(Helpers::__('user_contact_section')) ?></h3>
        <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0.75rem">
            <div><label class="form-label"><?= Helpers::e(Helpers::__('user_contact_name')) ?></label><input class="input admin-input" type="text" name="contact_name" value="<?= Helpers::e((string) ($p['contact_name'] ?? '')) ?>"></div>
            <div><label class="form-label"><?= Helpers::e(Helpers::__('user_contact_phone_req')) ?></label><input class="input admin-input" type="text" name="contact_phone" required value="<?= Helpers::e((string) ($p['contact_phone'] ?? '')) ?>"></div>
            <div><label class="form-label"><?= Helpers::e(Helpers::__('user_contact_wa')) ?></label><input class="input admin-input" type="text" name="contact_whatsapp" value="<?= Helpers::e((string) ($p['contact_whatsapp'] ?? '')) ?>"></div>
            <div><label class="form-label"><?= Helpers::e(Helpers::__('user_contact_tg')) ?></label><input class="input admin-input" type="text" name="contact_telegram" value="<?= Helpers::e((string) ($p['contact_telegram'] ?? '')) ?>"></div>
        </div>
        <label class="form-label"><?= Helpers::e(Helpers::__('user_contact_email')) ?></label>
        <input class="input admin-input" type="email" name="contact_email" value="<?= Helpers::e((string) ($p['contact_email'] ?? '')) ?>">

        <?php if ($images !== []): ?>
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

        <label class="form-label" for="images"><?= Helpers::e(Helpers::__('user_step3')) ?></label>
        <input id="images" class="input admin-input" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>

        <div style="display:flex;gap:0.75rem;flex-wrap:wrap">
            <a class="btn btn--ghost btn--pill" href="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>"><?= Helpers::e(Helpers::__('user_back')) ?></a>
            <button type="submit" class="btn btn--primary btn--pill"><?= Helpers::e(Helpers::__('user_save_listing')) ?></button>
        </div>
    </form>
</div>
