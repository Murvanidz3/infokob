<?php

declare(strict_types=1);

/** @var string $variant hero|compact */
$variant = $variant ?? 'compact';
$deal = $deal ?? 'sale';
$action = rtrim(BASE_URL, '/') . '/listings';
$tabClass = $variant === 'hero' ? 'search-hero__tabs' : 'search-compact__tabs';
$boxClass = $variant === 'hero' ? 'search-hero' : 'search-compact';
?>
<form class="<?= Helpers::e($boxClass) ?>" method="get" action="<?= Helpers::e($action) ?>">
    <div class="<?= Helpers::e($tabClass) ?>" role="tablist">
        <button type="button" class="deal-tab <?= $deal === 'sale' ? 'is-active' : '' ?>" data-deal="sale"><?= Helpers::e(Helpers::__('deal_sale')) ?></button>
        <button type="button" class="deal-tab <?= $deal === 'rent' ? 'is-active' : '' ?>" data-deal="rent"><?= Helpers::e(Helpers::__('deal_rent')) ?></button>
        <button type="button" class="deal-tab <?= $deal === 'daily_rent' ? 'is-active' : '' ?>" data-deal="daily_rent"><?= Helpers::e(Helpers::__('deal_daily')) ?></button>
    </div>
    <input type="hidden" name="deal" value="<?= Helpers::e($deal) ?>" class="js-deal-input">

    <div class="<?= $variant === 'hero' ? 'search-hero__row' : 'search-compact__row' ?>">
        <label class="visually-hidden" for="sq-<?= Helpers::e($variant) ?>"><?= Helpers::e(Helpers::__('filter_type')) ?></label>
        <select id="sq-<?= Helpers::e($variant) ?>" name="type" class="input">
            <option value=""><?= Helpers::e(Helpers::__('filter_type_any')) ?></option>
            <option value="apartment"><?= Helpers::e(Helpers::__('type_apartment')) ?></option>
            <option value="house"><?= Helpers::e(Helpers::__('type_house')) ?></option>
            <option value="cottage"><?= Helpers::e(Helpers::__('type_cottage')) ?></option>
            <option value="land"><?= Helpers::e(Helpers::__('type_land')) ?></option>
            <option value="commercial"><?= Helpers::e(Helpers::__('type_commercial')) ?></option>
            <option value="hotel_room"><?= Helpers::e(Helpers::__('type_hotel_room')) ?></option>
        </select>

        <label class="visually-hidden" for="dist-<?= Helpers::e($variant) ?>"><?= Helpers::e(Helpers::__('filter_district')) ?></label>
        <select id="dist-<?= Helpers::e($variant) ?>" name="district" class="input">
            <option value=""><?= Helpers::e(Helpers::__('filter_district_all')) ?></option>
            <option value="ჩაქვი"><?= Helpers::e(Helpers::__('district_chakvi')) ?></option>
            <option value="ცენტრი"><?= Helpers::e(Helpers::__('district_center')) ?></option>
            <option value="სანახარებო"><?= Helpers::e(Helpers::__('district_sakhareb')) ?></option>
            <option value="ეკო-პარკი"><?= Helpers::e(Helpers::__('district_ecopark')) ?></option>
        </select>

        <label class="visually-hidden" for="pmin-<?= Helpers::e($variant) ?>"><?= Helpers::e(Helpers::__('filter_price_min')) ?></label>
        <input id="pmin-<?= Helpers::e($variant) ?>" class="input" type="number" name="price_min" min="0" step="1" placeholder="<?= Helpers::e(Helpers::__('filter_price_min')) ?>">

        <label class="visually-hidden" for="pmax-<?= Helpers::e($variant) ?>"><?= Helpers::e(Helpers::__('filter_price_max')) ?></label>
        <input id="pmax-<?= Helpers::e($variant) ?>" class="input" type="number" name="price_max" min="0" step="1" placeholder="<?= Helpers::e(Helpers::__('filter_price_max')) ?>">

        <button type="submit" class="btn btn--primary btn--pill search-bar__submit">
            <i class="ph ph-magnifying-glass" aria-hidden="true"></i> <?= Helpers::e(Helpers::__('btn_search')) ?>
        </button>
    </div>
</form>
<script>
document.querySelectorAll('.<?= $variant === 'hero' ? 'search-hero' : 'search-compact' ?> .deal-tab').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var root = btn.closest('.<?= $variant === 'hero' ? 'search-hero' : 'search-compact' ?>');
        root.querySelectorAll('.deal-tab').forEach(function (b) { b.classList.remove('is-active'); });
        btn.classList.add('is-active');
        root.querySelector('.js-deal-input').value = btn.getAttribute('data-deal');
    });
});
</script>
