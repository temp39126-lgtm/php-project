<?php
$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM usps WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$rows = $db->query("SELECT * FROM usps ORDER BY sort_order, id")->fetchAll();
?>
<h2 class="page-title">USPS — Unique Selling Points</h2>
<div class="card">
    <h3><?= $edit ? 'Edit USP' : 'Add USP' ?></h3>
    <p class="hint">Shown in the "Why Choose Us" section on the homepage.</p>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_usp">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        <?php adm_text('Title *', 'title', $edit['title'] ?? '', 'text', 'required'); ?>
        <?php adm_textarea('Description', 'description', $edit['description'] ?? ''); ?>
        <input type="hidden" name="current_image" value="<?= e($edit['icon'] ?? '') ?>">
        <?php adm_image_field('Icon (optional)', 'image_file', 'image_url', $edit['icon'] ?? ''); ?>
        <input type="hidden" name="is_active" value="0">
        <div class="form-group"><label class="chk"><input type="checkbox" name="is_active" value="1" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>> Active</label></div>
        <button class="btn btn-blue"><?= $edit ? 'Update' : 'Add USP' ?></button>
        <?php if ($edit): ?><a href="<?= e(adm('usps')) ?>" class="btn btn-sm btn-gray" style="margin-left:8px;">Cancel</a><?php endif; ?>
    </form>
</div>
<div class="card">
    <h3>All USPs</h3>
    <table>
        <tr><th>Icon</th><th>Title</th><th>Description</th><th>Order</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($rows as $u): ?>
        <tr>
            <td><?php if ($u['icon']): ?><img class="thumb" src="<?= e(asset_url($u['icon'])) ?>"><?php else: ?>—<?php endif; ?></td>
            <td><strong><?= e($u['title']) ?></strong></td>
            <td><?= e($u['description']) ?></td>
            <td><?= (int)$u['sort_order'] ?></td>
            <td><span class="badge-sm <?= $u['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $u['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('usps', 'usps', $u); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
