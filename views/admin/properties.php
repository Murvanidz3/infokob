<?php

declare(strict_types=1);

/** @var list<array<string, mixed>> $rows */
/** @var array{status:string,q:string} $filters */
/** @var int $page */
/** @var int $totalPages */
/** @var int $total */
$st = $filters['status'];
$q = $filters['q'];
?>
<h1 class="admin-page__title"><?= Helpers::e(Helpers::__('admin_properties_title')) ?></h1>
<p class="admin-page__lead"><?= Helpers::e(Helpers::__('admin_properties_lead')) ?></p>

<form class="admin-filters" method="get" action="<?= Helpers::e(BASE_URL) ?>/properties">
    <label>
        <?= Helpers::e(Helpers::__('admin_filter_status')) ?>
        <select name="status">
            <option value="all" <?= $st === 'all' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('admin_status_all')) ?></option>
            <option value="pending" <?= $st === 'pending' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('status_pending')) ?></option>
            <option value="active" <?= $st === 'active' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('status_active')) ?></option>
            <option value="rejected" <?= $st === 'rejected' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('status_rejected')) ?></option>
            <option value="sold" <?= $st === 'sold' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('status_sold')) ?></option>
            <option value="archived" <?= $st === 'archived' ? 'selected' : '' ?>><?= Helpers::e(Helpers::__('status_archived')) ?></option>
        </select>
    </label>
    <label>
        <?= Helpers::e(Helpers::__('admin_filter_search')) ?>
        <input type="search" name="q" value="<?= Helpers::e($q) ?>" placeholder="<?= Helpers::e(Helpers::__('admin_search_ph')) ?>">
    </label>
    <button type="submit" class="btn btn--primary btn--sm btn--pill"><?= Helpers::e(Helpers::__('btn_search')) ?></button>
</form>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th></th>
                <th><?= Helpers::e(Helpers::__('user_col_title')) ?></th>
                <th><?= Helpers::e(Helpers::__('user_col_type')) ?></th>
                <th><?= Helpers::e(Helpers::__('user_col_price')) ?></th>
                <th><?= Helpers::e(Helpers::__('user_col_status')) ?></th>
                <th><?= Helpers::e(Helpers::__('admin_col_owner')) ?></th>
                <th><?= Helpers::e(Helpers::__('admin_col_created')) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <?php if (!empty($row['main_image'])): ?>
                            <img class="admin-thumb" src="<?= Helpers::e(Image::getImageUrl((string) $row['main_image'], 'thumb')) ?>" alt="">
                        <?php else: ?>
                            <span class="admin-thumb" aria-hidden="true"></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= Helpers::e(BASE_URL) ?>/properties/<?= (int) $row['id'] ?>"><?= Helpers::e((string) ($row['title'] ?? $row['slug'])) ?></a>
                    </td>
                    <td><?= Helpers::e(Helpers::__('type_' . (string) ($row['type'] ?? 'apartment'))) ?></td>
                    <td><?= Helpers::e(Helpers::formatPrice(isset($row['price']) ? (float) $row['price'] : null, (string) ($row['currency'] ?? 'USD'))) ?></td>
                    <td><span class="admin-badge admin-badge--<?= Helpers::e((string) ($row['status'] ?? '')) ?>"><?= Helpers::e(Helpers::__('status_' . (string) ($row['status'] ?? ''))) ?></span></td>
                    <td><?= Helpers::e((string) ($row['owner_name'] ?? '')) ?><br><small style="color:#94a3b8"><?= Helpers::e((string) ($row['owner_email'] ?? '')) ?></small></td>
                    <td><?= Helpers::e((string) ($row['created_at'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($rows === []): ?>
    <p class="admin-page__lead"><?= Helpers::e(Helpers::__('admin_no_results')) ?></p>
<?php endif; ?>

<div class="admin-pagination">
    <span><?= Helpers::e(Helpers::__('admin_pagination', ['total' => (string) $total, 'page' => (string) $page, 'pages' => (string) $totalPages])) ?></span>
    <?php if ($page > 1): ?>
        <a href="<?= Helpers::e(BASE_URL) ?>/properties?<?= Helpers::e(http_build_query(['status' => $st, 'q' => $q, 'page' => $page - 1])) ?>"><?= Helpers::e(Helpers::__('pagination_prev')) ?></a>
    <?php endif; ?>
    <?php if ($page < $totalPages): ?>
        <a href="<?= Helpers::e(BASE_URL) ?>/properties?<?= Helpers::e(http_build_query(['status' => $st, 'q' => $q, 'page' => $page + 1])) ?>"><?= Helpers::e(Helpers::__('pagination_next')) ?></a>
    <?php endif; ?>
</div>
