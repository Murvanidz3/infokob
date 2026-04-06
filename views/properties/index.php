<?php

declare(strict_types=1);

/** @var array<string, string> $meta */
/** @var list<array<string, mixed>> $listings */
/** @var array<string, mixed> $filters */
/** @var int $page */
/** @var int $totalPages */
/** @var int $total */
/** @var string $paginationBase */

$f = $filters;
$deal = (string) ($f['deal_type'] ?? 'sale');
$typesSel = $f['types'] ?? [];
?>
<div class="listings-page">
    <div class="listings-top container">
        <div class="listings-top__row">
            <?php View::partial('search-bar', ['variant' => 'compact', 'deal' => $deal]); ?>
            <div class="layout-toggle" role="group" aria-label="View">
                <button type="button" class="is-active" id="layout-grid" title="Grid">⊞</button>
                <button type="button" id="layout-list" title="List">☰</button>
            </div>
        </div>
    </div>

    <div class="container listings-layout">
        <button type="button" class="filter-sheet-trigger" id="filter-sheet-open"><?= Helpers::e(Helpers::__('filter_title')) ?> ⚙</button>
        <aside class="listings-sidebar" id="listings-sidebar" aria-label="<?= Helpers::e(Helpers::__('filter_title')) ?>">
            <form id="listing-filters" class="filter-panel" method="get" action="<?= Helpers::e(rtrim(BASE_URL, '/') . '/listings') ?>">
                <h3 class="filter-panel__title"><?= Helpers::e(Helpers::__('filter_title')) ?></h3>

                <fieldset class="filter-fieldset">
                    <legend><?= Helpers::e(Helpers::__('filter_type')) ?></legend>
                    <?php
                    $typeOptions = [
                        'apartment' => 'type_apartment',
                        'house' => 'type_house',
                        'cottage' => 'type_cottage',
                        'land' => 'type_land',
                        'commercial' => 'type_commercial',
                        'hotel_room' => 'type_hotel_room',
                    ];
                    foreach ($typeOptions as $val => $labelKey):
                        $checked = in_array($val, $typesSel, true);
                    ?>
                        <label class="filter-check">
                            <input type="checkbox" name="types[]" value="<?= Helpers::e($val) ?>" <?= $checked ? 'checked' : '' ?>>
                            <?= Helpers::e(Helpers::__($labelKey)) ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>

                <fieldset class="filter-fieldset">
                    <legend><?= Helpers::e(Helpers::__('filter_deal')) ?></legend>
                    <label class="filter-radio"><input type="radio" name="deal" value="sale" <?= $deal === 'sale' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_sale')) ?></label>
                    <label class="filter-radio"><input type="radio" name="deal" value="rent" <?= $deal === 'rent' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_rent')) ?></label>
                    <label class="filter-radio"><input type="radio" name="deal" value="daily_rent" <?= $deal === 'daily_rent' ? 'checked' : '' ?>> <?= Helpers::e(Helpers::__('deal_daily')) ?></label>
                </fieldset>

                <div class="filter-field">
                    <label for="price_min"><?= Helpers::e(Helpers::__('filter_price_min')) ?></label>
                    <div class="filter-range">
                        <input id="price_min" name="price_min" type="number" min="0" step="1" value="<?= Helpers::e((string) ($f['price_min'] ?? '')) ?>" placeholder="0">
                        <span>—</span>
                        <input name="price_max" type="number" min="0" step="1" value="<?= Helpers::e((string) ($f['price_max'] ?? '')) ?>" placeholder="∞">
                    </div>
                </div>

                <div class="filter-field">
                    <span class="filter-field__label"><?= Helpers::e(Helpers::__('filter_rooms')) ?></span>
                    <div class="pill-row">
                        <label class="pill-btn">
                            <input type="radio" name="rooms" value="" <?= ($f['rooms'] ?? '') === '' ? 'checked' : '' ?>>
                            <?= Helpers::e(Helpers::__('filter_any')) ?>
                        </label>
                        <?php foreach (['1', '2', '3', '4', '5'] as $r): ?>
                            <label class="pill-btn">
                                <input type="radio" name="rooms" value="<?= $r === '5' ? '5' : $r ?>" <?= (string) ($f['rooms'] ?? '') === $r ? 'checked' : '' ?>>
                                <?= $r === '5' ? '5+' : Helpers::e($r) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-field">
                    <label for="sea"><?= Helpers::e(Helpers::__('filter_sea')) ?></label>
                    <select id="sea" name="sea" class="input">
                        <option value=""><?= Helpers::e(Helpers::__('filter_any')) ?></option>
                        <?php $sm = $f['sea_max'] ?? null; ?>
                        <option value="50" <?= $sm !== null && (int) $sm === 50 ? 'selected' : '' ?>>50 m</option>
                        <option value="100" <?= $sm !== null && (int) $sm === 100 ? 'selected' : '' ?>>100 m</option>
                        <option value="200" <?= $sm !== null && (int) $sm === 200 ? 'selected' : '' ?>>200 m</option>
                        <option value="300" <?= $sm !== null && (int) $sm === 300 ? 'selected' : '' ?>>300 m</option>
                        <option value="500" <?= $sm !== null && (int) $sm === 500 ? 'selected' : '' ?>>500 m</option>
                        <option value="1000" <?= $sm !== null && (int) $sm === 1000 ? 'selected' : '' ?>>1 km+</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="district"><?= Helpers::e(Helpers::__('filter_district')) ?></label>
                    <select id="district" name="district" class="input">
                        <option value=""><?= Helpers::e(Helpers::__('filter_district_all')) ?></option>
                        <option value="ჩაქვი" <?= ($f['district'] ?? '') === 'ჩაქვი' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_chakvi')) ?></option>
                        <option value="ცენტრი" <?= ($f['district'] ?? '') === 'ცენტრი' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_center')) ?></option>
                        <option value="სანახარებო" <?= ($f['district'] ?? '') === 'სანახარებო' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_sakhareb')) ?></option>
                        <option value="ეკო-პარკი" <?= ($f['district'] ?? '') === 'ეკო-პარკი' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('district_ecopark')) ?></option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="sort"><?= Helpers::e(Helpers::__('filter_sort')) ?></label>
                    <select id="sort" name="sort" class="input">
                        <option value="newest" <?= ($f['sort'] ?? '') === 'newest' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('sort_newest')) ?></option>
                        <option value="price_asc" <?= ($f['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('sort_price_asc')) ?></option>
                        <option value="price_desc" <?= ($f['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('sort_price_desc')) ?></option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="q"><?= Helpers::e(Helpers::__('filter_keywords')) ?></label>
                    <input id="q" class="input" type="search" name="q" value="<?= Helpers::e((string) ($f['q'] ?? '')) ?>" placeholder="<?= Helpers::e(Helpers::__('filter_keywords_ph')) ?>">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn--primary btn--pill btn--block"><?= Helpers::e(Helpers::__('btn_search')) ?></button>
                    <a class="btn btn--ghost btn--pill btn--block" href="<?= Helpers::e(rtrim(BASE_URL, '/') . '/listings') ?>"><?= Helpers::e(Helpers::__('filter_clear')) ?></a>
                </div>
            </form>
        </aside>

        <div class="listings-main">
            <div class="results-bar">
                <p class="results-bar__count" id="results-count"><?= Helpers::e(Helpers::__('results_found', ['n' => (string) $total])) ?></p>
            </div>

            <div class="grid grid--4" id="listing-results">
                <?php foreach ($listings as $property): ?>
                    <?php View::partial('property-card', ['property' => $property]); ?>
                <?php endforeach; ?>
            </div>

            <p class="empty-state" id="listing-empty" style="<?= $listings === [] ? '' : 'display:none;' ?>"><?= Helpers::e(Helpers::__('results_empty')) ?></p>

            <div id="pagination-wrap">
                <?php View::partial('pagination', [
                    'page' => $page,
                    'totalPages' => $totalPages,
                    'total' => $total,
                    'paginationBase' => $paginationBase,
                ]); ?>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
  var g = document.getElementById('layout-grid');
  var l = document.getElementById('layout-list');
  var r = document.getElementById('listing-results');
  if (g && l && r) {
    g.addEventListener('click', function () {
      r.classList.remove('list-view');
      g.classList.add('is-active');
      l.classList.remove('is-active');
    });
    l.addEventListener('click', function () {
      r.classList.add('list-view');
      l.classList.add('is-active');
      g.classList.remove('is-active');
    });
  }
  var open = document.getElementById('filter-sheet-open');
  var side = document.getElementById('listings-sidebar');
  if (open && side) {
    open.addEventListener('click', function () {
      side.classList.toggle('is-open');
    });
  }
})();
</script>
