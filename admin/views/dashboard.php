<div class="admin-header">
    <h1>Dashboard</h1>
</div>

<!-- Stats -->
<div class="dashboard-stats" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
    <div class="dash-stat-card">
        <div class="dash-stat-icon blue"><i class="ph ph-buildings"></i></div>
        <div>
            <div class="dash-stat-value"><?= $stats['total'] ?></div>
            <div class="dash-stat-label">Total Listings</div>
        </div>
    </div>
    <div class="dash-stat-card">
        <div class="dash-stat-icon yellow"><i class="ph ph-clock"></i></div>
        <div>
            <div class="dash-stat-value"><?= $stats['pending'] ?></div>
            <div class="dash-stat-label">Pending Approval</div>
        </div>
    </div>
    <div class="dash-stat-card">
        <div class="dash-stat-icon green"><i class="ph ph-users"></i></div>
        <div>
            <div class="dash-stat-value"><?= $stats['users'] ?></div>
            <div class="dash-stat-label">Registered Users</div>
        </div>
    </div>
    <div class="dash-stat-card">
        <div class="dash-stat-icon blue"><i class="ph ph-eye"></i></div>
        <div>
            <div class="dash-stat-value"><?= number_format($stats['views']) ?></div>
            <div class="dash-stat-label">Total Views</div>
        </div>
    </div>
</div>

<!-- Pending Listings -->
<?php if (!empty($pending['data'])): ?>
<div class="table-wrap mt-8">
    <div class="table-header">
        <h3>🔔 Pending Approval (<?= count($pending['data']) ?>)</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Title</th>
                    <th>User</th>
                    <th>Price</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending['data'] as $listing): ?>
                <tr>
                    <td>
                        <?php if (!empty($listing['main_image'])): ?>
                        <img src="<?= getImageUrl($listing['main_image'], 'thumb') ?>" class="thumb" alt="">
                        <?php endif; ?>
                    </td>
                    <td><strong><?= e($listing['title'] ?? 'Listing #' . $listing['id']) ?></strong></td>
                    <td><?= e($listing['user_name'] ?? '-') ?></td>
                    <td><?= formatPrice($listing['price'], $listing['currency']) ?></td>
                    <td class="text-sm text-muted"><?= date('d.m.Y', strtotime($listing['created_at'])) ?></td>
                    <td>
                        <div class="table-actions">
                            <form method="POST" action="<?= ADMIN_URL ?>/listings/<?= $listing['id'] ?>/approve" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                    <i class="ph ph-check"></i>
                                </button>
                            </form>
                            <form method="POST" action="<?= ADMIN_URL ?>/listings/<?= $listing['id'] ?>/reject" style="display:inline;"
                                  onsubmit="var note = prompt('Reason for rejection (optional):'); if(note !== null) { this.querySelector('[name=admin_note]').value = note; return true; } return false;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="admin_note" value="">
                                <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                    <i class="ph ph-x"></i>
                                </button>
                            </form>
                            <a href="<?= BASE_URL ?>/listings/<?= e($listing['slug']) ?>" target="_blank" class="btn btn-ghost btn-sm" title="View">
                                <i class="ph ph-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="table-wrap mt-8 p-6 text-center text-muted">
    <i class="ph ph-checks" style="font-size: 3rem; display: block; margin-bottom: var(--space-4); color: var(--success);"></i>
    <h3>All caught up!</h3>
    <p>No pending listings to review.</p>
</div>
<?php endif; ?>
