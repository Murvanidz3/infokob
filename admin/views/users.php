<div class="admin-header">
    <h1>Users</h1>
</div>

<div class="table-wrap">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Listings</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= e($u['name']) ?></strong></td>
                    <td class="text-sm"><?= e($u['email']) ?></td>
                    <td class="text-sm"><?= e($u['phone']) ?></td>
                    <td>
                        <span class="badge <?= $u['role'] === 'admin' ? 'badge-featured' : 'badge-active' ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td><?= (int)($u['listings_count'] ?? 0) ?></td>
                    <td>
                        <span class="badge <?= $u['is_active'] ? 'badge-active' : 'badge-rejected' ?>">
                            <?= $u['is_active'] ? 'Active' : 'Blocked' ?>
                        </span>
                    </td>
                    <td class="text-sm text-muted"><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <?php if ($u['role'] !== 'admin'): ?>
                        <form method="POST" action="<?= ADMIN_URL ?>/users/<?= $u['id'] ?>/toggle" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm <?= $u['is_active'] ? 'btn-danger' : 'btn-success' ?>">
                                <?= $u['is_active'] ? 'Block' : 'Activate' ?>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
