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

<div class="admin-editor">
    <form method="post" action="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>/edit" enctype="multipart/form-data" class="form-stack admin-editor__form">
        <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
        <input type="hidden" name="main_image_id" id="main_image_id" value="<?= $images !== [] ? (int) ($images[0]['id'] ?? 0) : '' ?>">

        <section class="admin-card admin-editor__section">
            <h2 class="admin-card__title">1) <?= Helpers::e(Helpers::__('filter_type')) ?> / <?= Helpers::e(Helpers::__('filter_deal')) ?></h2>
            <div class="admin-editor__grid">
                <div>
                    <label class="form-label" for="type"><?= Helpers::e(Helpers::__('filter_type')) ?></label>
                    <select id="type" name="type" class="input admin-input">
                        <?php foreach (['apartment', 'house', 'cottage', 'land', 'commercial', 'hotel_room'] as $t): ?>
                            <option value="<?= Helpers::e($t) ?>" <?= (string) ($p['type'] ?? 'apartment') === $t ? 'selected' : '' ?>>
                                <?= Helpers::e(Helpers::__('type_' . $t)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label"><?= Helpers::e(Helpers::__('filter_deal')) ?></label>
                    <div class="admin-editor__deal-row">
                        <label class="admin-check"><input type="radio" name="deal_type" value="sale" <?= (string) ($p['deal_type'] ?? 'sale') === 'sale' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_sale')) ?></label>
                        <label class="admin-check"><input type="radio" name="deal_type" value="rent" <?= (string) ($p['deal_type'] ?? '') === 'rent' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_rent')) ?></label>
                        <label class="admin-check"><input type="radio" name="deal_type" value="daily_rent" <?= (string) ($p['deal_type'] ?? '') === 'daily_rent' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_daily')) ?></label>
                    </div>
                </div>
            </div>
        </section>

        <section class="admin-card admin-editor__section">
            <h2 class="admin-card__title">2) <?= Helpers::e(Helpers::__('user_price')) ?> / Specs</h2>
            <div class="admin-editor__grid">
                <div><label class="form-label" for="price"><?= Helpers::e(Helpers::__('user_price')) ?></label><input id="price" class="input admin-input" type="number" name="price" min="0" step="0.01" value="<?= Helpers::e((string) ($p['price'] ?? '')) ?>"></div>
                <div><label class="form-label" for="currency"><?= Helpers::e(Helpers::__('user_currency')) ?></label><select id="currency" class="input admin-input" name="currency"><?php foreach (['USD', 'GEL', 'EUR'] as $c): ?><option value="<?= $c ?>" <?= (string) ($p['currency'] ?? 'USD') === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?></select></div>
                <div><label class="form-label">m²</label><input class="input admin-input" type="number" name="area_m2" step="0.01" min="0" value="<?= Helpers::e((string) ($p['area_m2'] ?? '')) ?>"></div>
                <div><label class="form-label"><?= Helpers::e(Helpers::__('spec_rooms')) ?></label><input class="input admin-input" type="number" name="rooms" min="0" value="<?= Helpers::e((string) ($p['rooms'] ?? '')) ?>"></div>
                <div><label class="form-label"><?= Helpers::e(Helpers::__('user_bedrooms')) ?></label><input class="input admin-input" type="number" name="bedrooms" min="0" value="<?= Helpers::e((string) ($p['bedrooms'] ?? '')) ?>"></div>
                <div><label class="form-label"><?= Helpers::e(Helpers::__('user_bathrooms')) ?></label><input class="input admin-input" type="number" name="bathrooms" min="0" value="<?= Helpers::e((string) ($p['bathrooms'] ?? '')) ?>"></div>
                <div><label class="form-label"><?= Helpers::e(Helpers::__('user_floors_total')) ?></label><input class="input admin-input" type="number" name="floors_total" min="0" value="<?= Helpers::e((string) ($p['floors_total'] ?? '')) ?>"></div>
                <div><label class="form-label"><?= Helpers::e(Helpers::__('user_floor')) ?></label><input class="input admin-input" type="number" name="floor_number" value="<?= Helpers::e((string) ($p['floor_number'] ?? '')) ?>"></div>
            </div>
            <label class="admin-check"><input type="checkbox" name="price_negotiable" value="1" <?= !empty($p['price_negotiable']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('price_negotiable_short')) ?></label>
            <div class="admin-editor__comfort">
                <label class="admin-check"><input type="checkbox" name="has_pool" value="1" <?= !empty($p['has_pool']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('feat_pool')) ?></label>
                <label class="admin-check"><input type="checkbox" name="has_garden" value="1" <?= !empty($p['has_garden']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('feat_garden')) ?></label>
                <label class="admin-check"><input type="checkbox" name="has_balcony" value="1" <?= !empty($p['has_balcony']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('feat_balcony')) ?></label>
                <label class="admin-check"><input type="checkbox" name="has_garage" value="1" <?= !empty($p['has_garage']) ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('feat_garage')) ?></label>
            </div>
        </section>

        <section class="admin-card admin-editor__section">
            <h2 class="admin-card__title">3) Location</h2>
            <div class="admin-editor__grid">
                <div><label class="form-label" for="address"><?= Helpers::e(Helpers::__('user_address')) ?></label><input id="address" class="input admin-input" type="text" name="address" value="<?= Helpers::e((string) ($p['address'] ?? '')) ?>"></div>
                <div><label class="form-label" for="district"><?= Helpers::e(Helpers::__('filter_district')) ?></label><select id="district" class="input admin-input" name="district"><option value=""><?= Helpers::e(Helpers::__('filter_any')) ?></option><option value="ჩაქვი" <?= (($p['district'] ?? '') === 'ჩაქვი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_chakvi')) ?></option><option value="ცენტრი" <?= (($p['district'] ?? '') === 'ცენტრი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_center')) ?></option><option value="სანახარებო" <?= (($p['district'] ?? '') === 'სანახარებო') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_sakhareb')) ?></option><option value="ეკო-პარკი" <?= (($p['district'] ?? '') === 'ეკო-პარკი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_ecopark')) ?></option></select></div>
                <div><label class="form-label">Lat</label><input class="input admin-input" type="text" name="lat" value="<?= Helpers::e((string) ($p['lat'] ?? '')) ?>"></div>
                <div><label class="form-label">Lng</label><input class="input admin-input" type="text" name="lng" value="<?= Helpers::e((string) ($p['lng'] ?? '')) ?>"></div>
                <div><label class="form-label" for="sea_distance_m"><?= Helpers::e(Helpers::__('filter_sea')) ?></label><input id="sea_distance_m" class="input admin-input" type="number" min="0" name="sea_distance_m" value="<?= Helpers::e((string) ($p['sea_distance_m'] ?? '')) ?>"></div>
            </div>
        </section>

        <section class="admin-card admin-editor__section">
            <h2 class="admin-card__title">4) Content</h2>
            <label class="form-label" for="title"><?= Helpers::e(Helpers::__('user_listing_title')) ?></label>
            <input id="title" class="input admin-input" type="text" name="title" required maxlength="500" value="<?= Helpers::e((string) $ka['title']) ?>">
            <label class="form-label" for="description"><?= Helpers::e(Helpers::__('user_listing_desc')) ?></label>
            <textarea id="description" class="input admin-input" name="description" required minlength="50" rows="7"><?= Helpers::e((string) $ka['description']) ?></textarea>
        </section>

        <section class="admin-card admin-editor__section">
            <h2 class="admin-card__title">5) <?= Helpers::e(Helpers::__('user_contact_section')) ?></h2>
            <div class="admin-editor__grid">
                <div><label class="form-label"><?= Helpers::e(Helpers::__('user_contact_name')) ?></label><input class="input admin-input" type="text" name="contact_name" value="<?= Helpers::e((string) ($p['contact_name'] ?? '')) ?>"></div>
                <div><label class="form-label"><?= Helpers::e(Helpers::__('user_contact_phone_req')) ?></label><input class="input admin-input" type="text" name="contact_phone" required value="<?= Helpers::e((string) ($p['contact_phone'] ?? '')) ?>"></div>
                <div><label class="form-label"><?= Helpers::e(Helpers::__('user_contact_wa')) ?></label><input class="input admin-input" type="text" name="contact_whatsapp" value="<?= Helpers::e((string) ($p['contact_whatsapp'] ?? '')) ?>"></div>
                <div><label class="form-label"><?= Helpers::e(Helpers::__('user_contact_tg')) ?></label><input class="input admin-input" type="text" name="contact_telegram" value="<?= Helpers::e((string) ($p['contact_telegram'] ?? '')) ?>"></div>
            </div>
            <label class="form-label"><?= Helpers::e(Helpers::__('user_contact_email')) ?></label>
            <input class="input admin-input" type="email" name="contact_email" value="<?= Helpers::e((string) ($p['contact_email'] ?? '')) ?>">
        </section>

        <section class="admin-card admin-editor__section">
            <h2 class="admin-card__title">6) File manager</h2>
            <p class="admin-page__lead" style="margin-top:0"><?= Helpers::e(Helpers::__('user_existing_photos')) ?> — Main, order, delete, upload.</p>

            <div id="admin-image-manager" class="admin-image-manager">
                <?php foreach ($images as $idx => $img): ?>
                    <?php $imgId = (int) ($img['id'] ?? 0); $fn = (string) ($img['filename'] ?? ''); ?>
                    <div class="admin-image-item" data-image-id="<?= $imgId ?>">
                        <input type="hidden" name="image_order[]" value="<?= $imgId ?>" class="js-image-order">
                        <img src="<?= Helpers::e(Image::getImageUrl($fn, 'thumb')) ?>" alt="" class="admin-image-item__thumb">
                        <div class="admin-image-item__meta">
                            <div class="admin-image-item__title"><?= Helpers::e($fn) ?></div>
                            <div class="admin-image-item__controls">
                                <label class="admin-check"><input type="radio" name="main_image_radio" value="<?= $imgId ?>" <?= $idx === 0 ? 'checked' : '' ?> class="js-main-image-radio"> Main</label>
                                <label class="admin-check"><input type="checkbox" name="delete_images[]" value="<?= $imgId ?>" class="js-delete-image"> <?= Helpers::e(Helpers::__('user_delete_photo')) ?></label>
                            </div>
                        </div>
                        <div class="admin-image-item__actions">
                            <button type="button" class="btn btn--ghost btn--sm js-move-up">↑</button>
                            <button type="button" class="btn btn--ghost btn--sm js-move-down">↓</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <label class="form-label" for="images"><?= Helpers::e(Helpers::__('user_step3')) ?></label>
            <input id="images" class="input admin-input" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
            <div id="admin-new-image-preview" class="admin-new-image-preview"></div>
        </section>

        <div class="admin-editor__footer">
            <a class="btn btn--ghost btn--pill" href="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>"><?= Helpers::e(Helpers::__('user_back')) ?></a>
            <button type="submit" class="btn btn--primary btn--pill"><?= Helpers::e(Helpers::__('user_save_listing')) ?></button>
        </div>
    </form>
</div>

<script>
(function () {
  var manager = document.getElementById('admin-image-manager');
  var mainImage = document.getElementById('main_image_id');
  var fileInput = document.getElementById('images');
  var preview = document.getElementById('admin-new-image-preview');
  var dt = new DataTransfer();

  function refreshOrder() {
    if (!manager) return;
    var items = manager.querySelectorAll('.admin-image-item');
    items.forEach(function (item, i) {
      var orderInput = item.querySelector('.js-image-order');
      if (orderInput) {
        orderInput.value = item.getAttribute('data-image-id') || '';
      }
      item.setAttribute('data-order', String(i + 1));
    });
  }

  function refreshMainFromRadio() {
    if (!manager || !mainImage) return;
    var selected = manager.querySelector('.js-main-image-radio:checked');
    if (selected) {
      mainImage.value = selected.value || '';
    }
  }

  function ensureValidMainIfDeleted() {
    if (!manager) return;
    var radios = Array.prototype.slice.call(manager.querySelectorAll('.js-main-image-radio'));
    var selected = manager.querySelector('.js-main-image-radio:checked');
    if (!selected || selected.closest('.admin-image-item').querySelector('.js-delete-image').checked) {
      var fallback = radios.find(function (r) {
        var del = r.closest('.admin-image-item').querySelector('.js-delete-image');
        return del && !del.checked;
      });
      if (fallback) {
        fallback.checked = true;
      }
    }
    refreshMainFromRadio();
  }

  if (manager) {
    manager.addEventListener('click', function (e) {
      var up = e.target.closest('.js-move-up');
      var down = e.target.closest('.js-move-down');
      if (!up && !down) return;
      var item = e.target.closest('.admin-image-item');
      if (!item) return;
      if (up) {
        var prev = item.previousElementSibling;
        if (prev) manager.insertBefore(item, prev);
      }
      if (down) {
        var next = item.nextElementSibling;
        if (next) manager.insertBefore(next, item);
      }
      refreshOrder();
    });

    manager.addEventListener('change', function (e) {
      if (e.target.matches('.js-main-image-radio')) {
        refreshMainFromRadio();
      }
      if (e.target.matches('.js-delete-image')) {
        ensureValidMainIfDeleted();
      }
    });
  }

  if (fileInput && preview) {
    fileInput.addEventListener('change', function () {
      dt = new DataTransfer();
      preview.innerHTML = '';
      Array.prototype.forEach.call(fileInput.files, function (file, idx) {
        dt.items.add(file);
        var item = document.createElement('div');
        item.className = 'admin-new-image-item';

        var img = document.createElement('img');
        img.className = 'admin-new-image-item__thumb';
        img.alt = file.name;
        img.src = URL.createObjectURL(file);
        img.onload = function () { URL.revokeObjectURL(img.src); };

        var cap = document.createElement('div');
        cap.className = 'admin-new-image-item__name';
        cap.textContent = file.name;

        var rm = document.createElement('button');
        rm.type = 'button';
        rm.className = 'btn btn--ghost btn--sm';
        rm.textContent = 'Remove';
        rm.addEventListener('click', function () {
          var next = new DataTransfer();
          Array.prototype.forEach.call(dt.files, function (f, j) {
            if (j !== idx) next.items.add(f);
          });
          dt = next;
          fileInput.files = dt.files;
          item.remove();
        });

        item.appendChild(img);
        item.appendChild(cap);
        item.appendChild(rm);
        preview.appendChild(item);
      });
      fileInput.files = dt.files;
    });
  }

  refreshOrder();
  refreshMainFromRadio();
})();
</script>
