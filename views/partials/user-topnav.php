<?php

declare(strict_types=1);

$path = $_SERVER['REQUEST_URI'] ?? '';
$isDash = str_contains($path, '/my/dashboard');
$isCreate = str_contains($path, '/my/listings/create');
$isProfile = str_contains($path, '/my/profile');
$isListings = str_contains($path, '/my/listings') && !$isCreate;
?>
<nav class="user-topnav" aria-label="Account">
    <div class="user-topnav__inner">
        <a class="user-topnav__link <?= $isDash || $isListings ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/my/dashboard">📋 <?= Helpers::e(Helpers::__('nav_my_dashboard')) ?></a>
        <a class="user-topnav__link <?= $isCreate ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/my/listings/create">➕ <?= Helpers::e(Helpers::__('nav_add_listing')) ?></a>
        <a class="user-topnav__link <?= $isProfile ? 'is-active' : '' ?>" href="<?= Helpers::e(BASE_URL) ?>/my/profile">👤 <?= Helpers::e(Helpers::__('profile_title')) ?></a>
        <a class="user-topnav__link" href="<?= Helpers::e(BASE_URL) ?>/logout">🚪 <?= Helpers::e(Helpers::__('user_nav_logout')) ?></a>
    </div>
</nav>
