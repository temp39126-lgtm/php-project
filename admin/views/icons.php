<?php
$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM icons WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$rows = $db->query("SELECT * FROM icons ORDER BY sort_order, id")->fetchAll();
?>
<h2 class="page-title">Icons / Feature Items</h2>
<div class="card">
    <h3><?= $edit ? 'Edit Item' : 'Add Item' ?></h3>
    <p class="hint">Feature cards in the "Clean Energy" homepage section. The icon image is optional.</p>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_icon">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        <?php adm_text('Title *', 'title', $edit['title'] ?? '', 'text', 'required'); ?>
        <?php adm_textarea('Description', 'subtitle', $edit['subtitle'] ?? ''); ?>
        <input type="hidden" name="current_image" value="<?= e($edit['image'] ?? '') ?>">
        <?php adm_image_field('Icon Image (optional)', 'image_file', 'image_url', $edit['image'] ?? ''); ?>
        <?php adm_text('Link (optional)', 'link', $edit['link'] ?? ''); ?>
        <input type="hidden" name="is_active" value="0">
        <div class="form-group"><label class="chk"><input type="checkbox" name="is_active" value="1" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>> Active</label></div>
        <button class="btn btn-blue"><?= $edit ? 'Update' : 'Add Item' ?></button>
        <?php if ($edit): ?><a href="<?= e(adm('icons')) ?>" class="btn btn-sm btn-gray" style="margin-left:8px;">Cancel</a><?php endif; ?>
    </form>
</div>
<div class="card">
    <h3>All Items</h3>
    <table>
        <tr><th>Icon</th><th>Title</th><th>Description</th><th>Order</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($rows as $it): ?>
        <tr>
            <td><?php if ($it['image']): ?><img class="thumb" src="<?= e(asset_url($it['image'])) ?>"><?php else: ?>—<?php endif; ?></td>
            <td><strong><?= e($it['title']) ?></strong></td>
            <td><?= e($it['subtitle']) ?></td>
            <td><?= (int)$it['sort_order'] ?></td>
            <td><span class="badge-sm <?= $it['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $it['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('icons', 'icons', $it); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
