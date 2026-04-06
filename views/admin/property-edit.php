<?php

declare(strict_types=1);

/** @var array<string, mixed> $property */
/** @var list<array<string, mixed>> $images */
/** @var array<string, array{title:string,description:string}> $translations */
$p = $property;
$id = (int) ($p['id'] ?? 0);
$ka = $translations['ka'] ?? ['title' => '', 'description' => ''];
$mainId = 0;
foreach ($images as $im) {
    if (!empty($im['is_main'])) {
        $mainId = (int) ($im['id'] ?? 0);
        break;
    }
}
if ($mainId === 0 && $images !== []) {
    $mainId = (int) ($images[0]['id'] ?? 0);
}
?>
<div class="admin-editor-page">
    <header class="admin-editor-hero">
        <div class="admin-editor-hero__text">
            <span class="admin-editor-hero__badge"><?= Helpers::e(Helpers::__('admin_property_title')) ?> · #<?= $id ?></span>
            <h1 class="admin-editor-hero__title"><?= Helpers::e(Helpers::__('user_edit_title')) ?></h1>
            <p class="admin-editor-hero__lead">
                <a class="admin-editor-link" href="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>"><i class="ph ph-arrow-square-out" aria-hidden="true"></i> <?= Helpers::e(Helpers::__('admin_property_title')) ?></a>
                <span class="admin-editor-hero__sep">·</span>
                <a class="admin-editor-link" href="<?= Helpers::e(BASE_URL) ?>/properties"><i class="ph ph-list" aria-hidden="true"></i> <?= Helpers::e(Helpers::__('admin_back_list')) ?></a>
            </p>
        </div>
    </header>

    <form method="post" action="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>/edit" enctype="multipart/form-data" class="admin-editor-form">
        <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
        <input type="hidden" name="main_image_id" id="main_image_id" value="<?= $mainId > 0 ? $mainId : '' ?>">

        <section class="admin-editor-card">
            <div class="admin-editor-card__head">
                <span class="admin-editor-card__icon" aria-hidden="true"><i class="ph ph-buildings"></i></span>
                <div>
                    <h2 class="admin-editor-card__title"><?= Helpers::e(Helpers::__('filter_type')) ?> &amp; <?= Helpers::e(Helpers::__('filter_deal')) ?></h2>
                    <p class="admin-editor-card__hint"><?= Helpers::e(Helpers::__('admin_editor_hint_type_deal')) ?></p>
                </div>
            </div>
            <div class="admin-editor-grid">
                <div class="admin-field">
                    <label class="admin-field__label" for="type"><?= Helpers::e(Helpers::__('filter_type')) ?></label>
                    <div class="admin-field__control">
                        <select id="type" name="type" class="admin-field__input admin-field__input--select">
                            <?php foreach (['apartment', 'house', 'cottage', 'land', 'commercial', 'hotel_room'] as $t): ?>
                                <option value="<?= Helpers::e($t) ?>" <?= (string) ($p['type'] ?? 'apartment') === $t ? 'selected' : '' ?>>
                                    <?= Helpers::e(Helpers::__('type_' . $t)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="admin-field admin-field--full">
                    <span class="admin-field__label"><?= Helpers::e(Helpers::__('filter_deal')) ?></span>
                    <div class="admin-editor-segment" role="radiogroup" aria-label="<?= Helpers::e(Helpers::__('filter_deal')) ?>">
                        <label class="admin-editor-segment__item">
                            <input type="radio" name="deal_type" value="sale" class="admin-editor-segment__input" <?= (string) ($p['deal_type'] ?? 'sale') === 'sale' ? 'checked' : '' ?>>
                            <span class="admin-editor-segment__face"><?= Helpers::e(Helpers::__('deal_sale')) ?></span>
                        </label>
                        <label class="admin-editor-segment__item">
                            <input type="radio" name="deal_type" value="rent" class="admin-editor-segment__input" <?= (string) ($p['deal_type'] ?? '') === 'rent' ? 'checked' : '' ?>>
                            <span class="admin-editor-segment__face"><?= Helpers::e(Helpers::__('deal_rent')) ?></span>
                        </label>
                        <label class="admin-editor-segment__item">
                            <input type="radio" name="deal_type" value="daily_rent" class="admin-editor-segment__input" <?= (string) ($p['deal_type'] ?? '') === 'daily_rent' ? 'checked' : '' ?>>
                            <span class="admin-editor-segment__face"><?= Helpers::e(Helpers::__('deal_daily')) ?></span>
                        </label>
                    </div>
                </div>
            </div>
        </section>

        <section class="admin-editor-card">
            <div class="admin-editor-card__head">
                <span class="admin-editor-card__icon" aria-hidden="true"><i class="ph ph-currency-circle-dollar"></i></span>
                <div>
                    <h2 class="admin-editor-card__title"><?= Helpers::e(Helpers::__('admin_editor_title_price_specs')) ?></h2>
                    <p class="admin-editor-card__hint"><?= Helpers::e(Helpers::__('admin_editor_hint_price_specs')) ?></p>
                </div>
            </div>
            <div class="admin-editor-grid admin-editor-grid--4">
                <div class="admin-field">
                    <label class="admin-field__label" for="price"><?= Helpers::e(Helpers::__('user_price')) ?></label>
                    <input id="price" class="admin-field__input" type="number" name="price" min="0" step="0.01" value="<?= Helpers::e((string) ($p['price'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label" for="currency"><?= Helpers::e(Helpers::__('user_currency')) ?></label>
                    <select id="currency" class="admin-field__input admin-field__input--select" name="currency">
                        <?php foreach (['USD', 'GEL', 'EUR'] as $c): ?>
                            <option value="<?= $c ?>" <?= (string) ($p['currency'] ?? 'USD') === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('spec_area')) ?> (m²)</label>
                    <input class="admin-field__input" type="number" name="area_m2" step="0.01" min="0" value="<?= Helpers::e((string) ($p['area_m2'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('spec_rooms')) ?></label>
                    <input class="admin-field__input" type="number" name="rooms" min="0" value="<?= Helpers::e((string) ($p['rooms'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_bedrooms')) ?></label>
                    <input class="admin-field__input" type="number" name="bedrooms" min="0" value="<?= Helpers::e((string) ($p['bedrooms'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_bathrooms')) ?></label>
                    <input class="admin-field__input" type="number" name="bathrooms" min="0" value="<?= Helpers::e((string) ($p['bathrooms'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_floors_total')) ?></label>
                    <input class="admin-field__input" type="number" name="floors_total" min="0" value="<?= Helpers::e((string) ($p['floors_total'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_floor')) ?></label>
                    <input class="admin-field__input" type="number" name="floor_number" value="<?= Helpers::e((string) ($p['floor_number'] ?? '')) ?>">
                </div>
            </div>
            <label class="admin-editor-switch">
                <input type="checkbox" name="price_negotiable" value="1" class="admin-editor-switch__input" <?= !empty($p['price_negotiable']) ? 'checked' : '' ?>>
                <span class="admin-editor-switch__track"></span>
                <span class="admin-editor-switch__label"><?= Helpers::e(Helpers::__('price_negotiable_short')) ?></span>
            </label>
            <div class="admin-editor-chips" role="group" aria-label="<?= Helpers::e(Helpers::__('admin_editor_comfort_group')) ?>">
                <label class="admin-editor-chip"><input type="checkbox" name="has_pool" value="1" class="admin-editor-chip__input" <?= !empty($p['has_pool']) ? 'checked' : '' ?>><span class="admin-editor-chip__face"><i class="ph ph-swimming-pool"></i> <?= Helpers::e(Helpers::__('feat_pool')) ?></span></label>
                <label class="admin-editor-chip"><input type="checkbox" name="has_garden" value="1" class="admin-editor-chip__input" <?= !empty($p['has_garden']) ? 'checked' : '' ?>><span class="admin-editor-chip__face"><i class="ph ph-tree"></i> <?= Helpers::e(Helpers::__('feat_garden')) ?></span></label>
                <label class="admin-editor-chip"><input type="checkbox" name="has_balcony" value="1" class="admin-editor-chip__input" <?= !empty($p['has_balcony']) ? 'checked' : '' ?>><span class="admin-editor-chip__face"><i class="ph ph-frame-corners"></i> <?= Helpers::e(Helpers::__('feat_balcony')) ?></span></label>
                <label class="admin-editor-chip"><input type="checkbox" name="has_garage" value="1" class="admin-editor-chip__input" <?= !empty($p['has_garage']) ? 'checked' : '' ?>><span class="admin-editor-chip__face"><i class="ph ph-car"></i> <?= Helpers::e(Helpers::__('feat_garage')) ?></span></label>
            </div>
        </section>

        <section class="admin-editor-card">
            <div class="admin-editor-card__head">
                <span class="admin-editor-card__icon" aria-hidden="true"><i class="ph ph-map-pin"></i></span>
                <div>
                    <h2 class="admin-editor-card__title"><?= Helpers::e(Helpers::__('admin_editor_section_location')) ?></h2>
                    <p class="admin-editor-card__hint"><?= Helpers::e(Helpers::__('admin_editor_hint_location')) ?></p>
                </div>
            </div>
            <div class="admin-editor-grid">
                <div class="admin-field admin-field--full">
                    <label class="admin-field__label" for="address"><?= Helpers::e(Helpers::__('user_address')) ?></label>
                    <input id="address" class="admin-field__input" type="text" name="address" value="<?= Helpers::e((string) ($p['address'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label" for="district"><?= Helpers::e(Helpers::__('filter_district')) ?></label>
                    <select id="district" class="admin-field__input admin-field__input--select" name="district">
                        <option value=""><?= Helpers::e(Helpers::__('filter_any')) ?></option>
                        <option value="ჩაქვი" <?= (($p['district'] ?? '') === 'ჩაქვი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_chakvi')) ?></option>
                        <option value="ცენტრი" <?= (($p['district'] ?? '') === 'ცენტრი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_center')) ?></option>
                        <option value="სანახარებო" <?= (($p['district'] ?? '') === 'სანახარებო') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_sakhareb')) ?></option>
                        <option value="ეკო-პარკი" <?= (($p['district'] ?? '') === 'ეკო-პარკი') ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_ecopark')) ?></option>
                    </select>
                </div>
                <div class="admin-field">
                    <label class="admin-field__label" for="sea_distance_m"><?= Helpers::e(Helpers::__('filter_sea')) ?> (m)</label>
                    <input id="sea_distance_m" class="admin-field__input" type="number" min="0" name="sea_distance_m" value="<?= Helpers::e((string) ($p['sea_distance_m'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label">Lat</label>
                    <input class="admin-field__input" type="text" name="lat" value="<?= Helpers::e((string) ($p['lat'] ?? '')) ?>" placeholder="41.82">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label">Lng</label>
                    <input class="admin-field__input" type="text" name="lng" value="<?= Helpers::e((string) ($p['lng'] ?? '')) ?>" placeholder="41.78">
                </div>
            </div>
        </section>

        <section class="admin-editor-card">
            <div class="admin-editor-card__head">
                <span class="admin-editor-card__icon" aria-hidden="true"><i class="ph ph-text-aa"></i></span>
                <div>
                    <h2 class="admin-editor-card__title"><?= Helpers::e(Helpers::__('admin_editor_section_content')) ?></h2>
                    <p class="admin-editor-card__hint"><?= Helpers::e(Helpers::__('admin_editor_hint_content')) ?></p>
                </div>
            </div>
            <div class="admin-field">
                <label class="admin-field__label" for="title"><?= Helpers::e(Helpers::__('user_listing_title')) ?></label>
                <input id="title" class="admin-field__input" type="text" name="title" required maxlength="500" value="<?= Helpers::e((string) $ka['title']) ?>">
            </div>
            <div class="admin-field">
                <div class="admin-field__label-row">
                    <label class="admin-field__label" for="description"><?= Helpers::e(Helpers::__('user_listing_desc')) ?></label>
                    <span id="desc-count" class="admin-field__counter" aria-live="polite">0</span>
                </div>
                <textarea id="description" class="admin-field__textarea" name="description" required minlength="50" rows="10" placeholder="<?= Helpers::e(Helpers::__('admin_editor_desc_placeholder')) ?>"><?= Helpers::e((string) $ka['description']) ?></textarea>
            </div>
        </section>

        <section class="admin-editor-card">
            <div class="admin-editor-card__head">
                <span class="admin-editor-card__icon" aria-hidden="true"><i class="ph ph-phone"></i></span>
                <div>
                    <h2 class="admin-editor-card__title"><?= Helpers::e(Helpers::__('user_contact_section')) ?></h2>
                    <p class="admin-editor-card__hint"><?= Helpers::e(Helpers::__('admin_editor_hint_contact')) ?></p>
                </div>
            </div>
            <div class="admin-editor-grid">
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_contact_name')) ?></label>
                    <input class="admin-field__input" type="text" name="contact_name" value="<?= Helpers::e((string) ($p['contact_name'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_contact_phone_req')) ?></label>
                    <input class="admin-field__input" type="text" name="contact_phone" required value="<?= Helpers::e((string) ($p['contact_phone'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_contact_wa')) ?></label>
                    <input class="admin-field__input" type="text" name="contact_whatsapp" value="<?= Helpers::e((string) ($p['contact_whatsapp'] ?? '')) ?>">
                </div>
                <div class="admin-field">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_contact_tg')) ?></label>
                    <input class="admin-field__input" type="text" name="contact_telegram" value="<?= Helpers::e((string) ($p['contact_telegram'] ?? '')) ?>">
                </div>
                <div class="admin-field admin-field--full">
                    <label class="admin-field__label"><?= Helpers::e(Helpers::__('user_contact_email')) ?></label>
                    <input class="admin-field__input" type="email" name="contact_email" value="<?= Helpers::e((string) ($p['contact_email'] ?? '')) ?>">
                </div>
            </div>
        </section>

        <section class="admin-editor-card">
            <div class="admin-editor-card__head">
                <span class="admin-editor-card__icon" aria-hidden="true"><i class="ph ph-images-square"></i></span>
                <div>
                    <h2 class="admin-editor-card__title"><?= Helpers::e(Helpers::__('admin_editor_section_media')) ?></h2>
                    <p class="admin-editor-card__hint"><?= Helpers::e(Helpers::__('admin_editor_hint_media')) ?></p>
                </div>
            </div>

            <div id="admin-image-manager" class="admin-image-manager">
                <?php foreach ($images as $idx => $img): ?>
                    <?php $imgId = (int) ($img['id'] ?? 0); $fn = (string) ($img['filename'] ?? ''); $isMain = $mainId > 0 ? $imgId === $mainId : $idx === 0; ?>
                    <div class="admin-image-item" data-image-id="<?= $imgId ?>">
                        <input type="hidden" name="image_order[]" value="<?= $imgId ?>" class="js-image-order">
                        <img src="<?= Helpers::e(Image::getImageUrl($fn, 'thumb')) ?>" alt="" class="admin-image-item__thumb">
                        <div class="admin-image-item__meta">
                            <div class="admin-image-item__title"><?= Helpers::e($fn) ?></div>
                            <div class="admin-image-item__controls">
                                <label class="admin-image-item__radio"><input type="radio" name="main_image_radio" value="<?= $imgId ?>" <?= $isMain ? 'checked' : '' ?> class="js-main-image-radio"> <span><?= Helpers::e(Helpers::__('admin_photo_main')) ?></span></label>
                                <label class="admin-image-item__del"><input type="checkbox" name="delete_images[]" value="<?= $imgId ?>" class="js-delete-image"> <?= Helpers::e(Helpers::__('user_delete_photo')) ?></label>
                            </div>
                        </div>
                        <div class="admin-image-item__actions">
                            <button type="button" class="admin-icon-btn js-move-up" title="Up" aria-label="Move up"><i class="ph ph-caret-up"></i></button>
                            <button type="button" class="admin-icon-btn js-move-down" title="Down" aria-label="Move down"><i class="ph ph-caret-down"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <label class="admin-upload" for="images">
                <input id="images" class="admin-upload__input" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                <span class="admin-upload__ui">
                    <i class="ph ph-cloud-arrow-up admin-upload__icon" aria-hidden="true"></i>
                    <span class="admin-upload__title"><?= Helpers::e(Helpers::__('user_step3')) ?></span>
                    <span class="admin-upload__sub">JPEG, PNG, WebP — ჩააგდე ან აირჩიე ფაილები</span>
                </span>
            </label>
            <div id="admin-new-image-preview" class="admin-new-image-preview"></div>
        </section>

        <div class="admin-editor-bar">
            <a class="btn btn--ghost btn--pill" href="<?= Helpers::e(BASE_URL) ?>/properties/<?= $id ?>"><?= Helpers::e(Helpers::__('user_back')) ?></a>
            <button type="submit" class="btn btn--primary btn--pill admin-editor-bar__save"><i class="ph ph-floppy-disk" aria-hidden="true"></i> <?= Helpers::e(Helpers::__('user_save_listing')) ?></button>
        </div>
    </form>
</div>

<script>
(function () {
  var manager = document.getElementById('admin-image-manager');
  var mainImage = document.getElementById('main_image_id');
  var fileInput = document.getElementById('images');
  var preview = document.getElementById('admin-new-image-preview');
  var desc = document.getElementById('description');
  var descCount = document.getElementById('desc-count');
  function refreshDescCount() {
    if (!desc || !descCount) return;
    var n = desc.value.length;
    descCount.textContent = n + ' / 50+';
    descCount.style.color = n < 50 ? '#c13515' : '#64748b';
  }

  if (desc) {
    desc.addEventListener('input', refreshDescCount);
    refreshDescCount();
  }

  function refreshOrder() {
    if (!manager) return;
    var items = manager.querySelectorAll('.admin-image-item');
    items.forEach(function (item) {
      var orderInput = item.querySelector('.js-image-order');
      if (orderInput) {
        orderInput.value = item.getAttribute('data-image-id') || '';
      }
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

  function renderNewFilePreview() {
    if (!fileInput || !preview) return;
    preview.innerHTML = '';
    var files = fileInput.files;
    if (!files || !files.length) return;
    Array.prototype.forEach.call(files, function (file, idx) {
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
      rm.textContent = <?= json_encode(Helpers::__('user_delete_photo'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
      rm.addEventListener('click', function () {
        var next = new DataTransfer();
        Array.prototype.forEach.call(fileInput.files, function (f, j) {
          if (j !== idx) next.items.add(f);
        });
        fileInput.files = next.files;
        renderNewFilePreview();
      });

      item.appendChild(img);
      item.appendChild(cap);
      item.appendChild(rm);
      preview.appendChild(item);
    });
  }

  if (fileInput && preview) {
    fileInput.addEventListener('change', renderNewFilePreview);
  }

  refreshOrder();
  refreshMainFromRadio();
})();
</script>
