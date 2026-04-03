<?php

declare(strict_types=1);

/** @var list<array<string, mixed>> $rows */
/** @var int $page */
/** @var int $totalPages */
/** @var int $total */
?>
<h1 class="admin-page__title"><?= Helpers::e(Helpers::__('admin_users_title')) ?></h1>
<p class="admin-page__lead"><?= Helpers::e(Helpers::__('admin_users_lead')) ?></p>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= Helpers::e(Helpers::__('contact_form_name')) ?></th>
                <th><?= Helpers::e(Helpers::__('contact_form_email')) ?></th>
                <th><?= Helpers::e(Helpers::__('auth_phone')) ?></th>
                <th><?= Helpers::e(Helpers::__('admin_col_role')) ?></th>
                <th><?= Helpers::e(Helpers::__('admin_col_active')) ?></th>
                <th><?= Helpers::e(Helpers::__('admin_col_created')) ?></th>
                <th><?= Helpers::e(Helpers::__('user_col_actions')) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $u): ?>
                <tr>
                    <td><?= (int) $u['id'] ?></td>
                    <td><?= Helpers::e((string) ($u['name'] ?? '')) ?></td>
                    <td><?= Helpers::e((string) ($u['email'] ?? '')) ?></td>
                    <td><?= Helpers::e((string) ($u['phone'] ?? '—')) ?></td>
                    <td><?= Helpers::e((string) ($u['role'] ?? '')) ?></td>
                    <td><?= !empty($u['is_active']) ? Helpers::e(Helpers::__('admin_yes')) : Helpers::e(Helpers::__('admin_no')) ?></td>
                    <td><?= Helpers::e((string) ($u['created_at'] ?? '')) ?></td>
                    <td>
                        <?php if (($u['role'] ?? '') !== 'user'): ?>
                            —
                        <?php else: ?>
                            <form method="post" action="<?= Helpers::e(BASE_URL) ?>/users/<?= (int) $u['id'] ?>/active" style="display:inline">
                                <input type="hidden" name="csrf" value="<?= Helpers::e(Helpers::csrfToken()) ?>">
                                <?php if (!empty($u['is_active'])): ?>
                                    <input type="hidden" name="active" value="0">
                                    <button type="submit" class="btn btn--ghost btn--sm"><?= Helpers::e(Helpers::__('admin_user_disable')) ?></button>
                                <?php else: ?>
                                    <input type="hidden" name="active" value="1">
                                    <button type="submit" class="btn btn--primary btn--sm"><?= Helpers::e(Helpers::__('admin_user_enable')) ?></button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="admin-pagination">
    <span><?= Helpers::e(Helpers::__('admin_pagination', ['total' => (string) $total, 'page' => (string) $page, 'pages' => (string) $totalPages])) ?></span>
    <?php if ($page > 1): ?>
        <a href="<?= Helpers::e(BASE_URL) ?>/users?<?= Helpers::e(http_build_query(['page' => $page - 1])) ?>"><?= Helpers::e(Helpers::__('pagination_prev')) ?></a>
    <?php endif; ?>
    <?php if ($page < $totalPages): ?>
        <a href="<?= Helpers::e(BASE_URL) ?>/users?<?= Helpers::e(http_build_query(['page' => $page + 1])) ?>"><?= Helpers::e(Helpers::__('pagination_next')) ?></a>
    <?php endif; ?>
</div>
