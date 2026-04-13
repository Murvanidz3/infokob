<div class="admin-header">
    <h1>Listings</h1>
    <div class="flex gap-3">
        <a href="<?= ADMIN_URL ?>/listings" class="btn btn-ghost btn-sm <?= empty($_GET['status']) ? 'btn-primary' : '' ?>">All</a>
        <a href="<?= ADMIN_URL ?>/listings?status=pending" class="btn btn-ghost btn-sm <?= ($_GET['status'] ?? '') === 'pending' ? 'btn-primary' : '' ?>">Pending</a>
        <a href="<?= ADMIN_URL ?>/listings?status=active" class="btn btn-ghost btn-sm <?= ($_GET['status'] ?? '') === 'active' ? 'btn-primary' : '' ?>">Active</a>
        <a href="<?= ADMIN_URL ?>/listings?status=rejected" class="btn btn-ghost btn-sm <?= ($_GET['status'] ?? '') === 'rejected' ? 'btn-primary' : '' ?>">Rejected</a>
    </div>
</div>

<div class="table-wrap">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Title</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th>Views</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listings as $listing): ?>
                <tr>
                    <td>
                        <?php if (!empty($listing['main_image'])): ?>
                        <img src="<?= getImageUrl($listing['main_image'], 'thumb') ?>" class="thumb" alt="">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= BASE_URL ?>/listings/<?= e($listing['slug']) ?>" target="_blank" style="font-weight: 600;">
                            <?= e(truncate($listing['title'] ?? '#' . $listing['id'], 40)) ?>
                        </a>
                    </td>
                    <td class="text-sm"><?= e($listing['user_name'] ?? '-') ?></td>
                    <td class="text-sm"><?= getTypeLabel($listing['type']) ?></td>
                    <td><?= formatPrice($listing['price'], $listing['currency']) ?></td>
                    <td>
                        <?php $sc = match($listing['status']) { 'active' => 'badge-active', 'pending' => 'badge-pending', 'rejected' => 'badge-rejected', 'sold' => 'badge-sold', default => 'badge-pending' }; ?>
                        <span class="badge <?= $sc ?>"><?= ucfirst($listing['status']) ?></span>
                    </td>
                    <td>
                        <?php if ($listing['is_featured']): ?>
                        <span class="badge badge-featured">⭐</span>
                        <?php else: ?>
                        <span class="text-xs text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($listing['views']) ?></td>
                    <td>
                        <div class="table-actions" x-data="{ showMenu: false }" style="position: relative;">
                            <!-- Quick actions -->
                            <?php if ($listing['status'] === 'pending'): ?>
                            <form method="POST" action="<?= ADMIN_URL ?>/listings/<?= $listing['id'] ?>/approve" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-success btn-sm"><i class="ph ph-check"></i></button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($listing['status'] !== 'rejected'): ?>
                            <form method="POST" action="<?= ADMIN_URL ?>/listings/<?= $listing['id'] ?>/reject" style="display:inline;"
                                  onsubmit="var n = prompt('Reason?'); if(n !== null) { this.querySelector('[name=admin_note]').value = n; return true; } return false;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="admin_note" value="">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);"><i class="ph ph-x"></i></button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if (!$listing['is_featured']): ?>
                            <form method="POST" action="<?= ADMIN_URL ?>/listings/<?= $listing['id'] ?>/feature" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="days" value="30">
                                <button type="submit" class="btn btn-ghost btn-sm" title="Feature 30d"><i class="ph ph-star"></i></button>
                            </form>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?= ADMIN_URL ?>/listings/<?= $listing['id'] ?>/delete" style="display:inline;"
                                  onsubmit="return confirm('Delete this listing?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--danger);"><i class="ph ph-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($listings)): ?>
                <tr>
                    <td colspan="9" class="text-center text-muted p-6">No listings found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
<div style="margin-top: var(--space-6);">
    <?php $baseUrl = ADMIN_URL . '/listings' . (empty($_GET['status']) ? '' : '?status=' . e($_GET['status'])); ?>
    <?php require VIEW_PATH . '/partials/pagination.php'; ?>
</div>
<?php endif; ?>
