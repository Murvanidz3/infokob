<?php
/**
 * User Dashboard View
 * Stats + Listings table
 */
?>

<h2 style="margin-bottom: var(--space-6);"><?= __('dashboard_title') ?></h2>

<!-- Stats -->
<div class="dashboard-stats">
    <div class="dash-stat-card">
        <div class="dash-stat-icon green"><i class="ph ph-check-circle"></i></div>
        <div>
            <div class="dash-stat-value"><?= $stats['active'] ?></div>
            <div class="dash-stat-label"><?= __('dashboard_active') ?></div>
        </div>
    </div>
    <div class="dash-stat-card">
        <div class="dash-stat-icon yellow"><i class="ph ph-clock"></i></div>
        <div>
            <div class="dash-stat-value"><?= $stats['pending'] ?></div>
            <div class="dash-stat-label"><?= __('dashboard_pending') ?></div>
        </div>
    </div>
    <div class="dash-stat-card">
        <div class="dash-stat-icon blue"><i class="ph ph-eye"></i></div>
        <div>
            <div class="dash-stat-value"><?= number_format($stats['views']) ?></div>
            <div class="dash-stat-label"><?= __('dashboard_total_views') ?></div>
        </div>
    </div>
</div>

<!-- Listings -->
<div class="table-wrap">
    <div class="table-header">
        <h3><?= __('my_listings_title') ?></h3>
        <a href="<?= BASE_URL ?>/my/listings/create" class="btn btn-accent btn-sm">
            <i class="ph ph-plus"></i> <?= __('nav_add_listing') ?>
        </a>
    </div>
    
    <?php if (!empty($listings)): ?>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th></th>
                    <th><?= __('field_title') ?></th>
                    <th><?= __('filter_type') ?></th>
                    <th><?= __('field_price') ?></th>
                    <th>Status</th>
                    <th><?= __('property_views') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listings as $listing): ?>
                <tr>
                    <td>
                        <?php if (!empty($listing['main_image'])): ?>
                        <img src="<?= getImageUrl($listing['main_image'], 'thumb') ?>" alt="" class="thumb">
                        <?php else: ?>
                        <div class="thumb" style="background: var(--bg-alt); display: flex; align-items: center; justify-content: center;">
                            <i class="ph ph-image" style="color: var(--muted);"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/listings/<?= e($listing['slug']) ?>" style="font-weight: 600;">
                            <?= e(truncate($listing['title'] ?? 'Listing #' . $listing['id'], 40)) ?>
                        </a>
                    </td>
                    <td><?= getTypeLabel($listing['type']) ?></td>
                    <td><?= formatPrice($listing['price'], $listing['currency']) ?></td>
                    <td>
                        <?php
                        $statusClass = match($listing['status']) {
                            'active' => 'badge-active',
                            'pending' => 'badge-pending',
                            'rejected' => 'badge-rejected',
                            'sold' => 'badge-sold',
                            default => 'badge-pending',
                        };
                        ?>
                        <span class="badge <?= $statusClass ?>"><?= getStatusLabel($listing['status']) ?></span>
                        <?php if ($listing['status'] === 'rejected' && !empty($listing['admin_note'])): ?>
                        <div class="text-xs text-danger mt-1" title="<?= e($listing['admin_note']) ?>">
                            ⓘ <?= e(truncate($listing['admin_note'], 30)) ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($listing['views']) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="<?= BASE_URL ?>/my/listings/<?= $listing['id'] ?>/edit" class="btn btn-ghost btn-sm" title="<?= __('btn_edit') ?>">
                                <i class="ph ph-pencil"></i>
                            </a>
                            <form method="POST" action="<?= BASE_URL ?>/my/listings/<?= $listing['id'] ?>/delete" 
                                  onsubmit="return confirm('<?= __('btn_delete') ?>?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--danger);" title="<?= __('btn_delete') ?>">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="text-center p-6">
        <i class="ph ph-buildings" style="font-size: 3rem; color: var(--muted); display: block; margin-bottom: var(--space-4);"></i>
        <h3 class="text-muted"><?= __('no_listings') ?></h3>
        <p class="text-sm text-muted mb-4"><?= __('no_listings_desc') ?></p>
        <a href="<?= BASE_URL ?>/my/listings/create" class="btn btn-primary">
            <i class="ph ph-plus"></i> <?= __('nav_add_listing') ?>
        </a>
    </div>
    <?php endif; ?>
</div>
