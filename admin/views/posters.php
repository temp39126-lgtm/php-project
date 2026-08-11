<?php
$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM posters WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$rows = $db->query("SELECT * FROM posters ORDER BY sort_order, id")->fetchAll();
?>
<h2 class="page-title">Posters</h2>
<div class="card">
    <h3><?= $edit ? 'Edit Poster' : 'Add Poster' ?></h3>
    <p class="hint">Image strip on the homepage (used for certificates / promotional posters).</p>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_poster">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        <input type="hidden" name="current_image" value="<?= e($edit['image'] ?? '') ?>">
        <?php adm_image_field('Poster Image *', 'image_file', 'image_url', $edit['image'] ?? ''); ?>
        <?php adm_text('Title / Alt Text', 'title', $edit['title'] ?? ''); ?>
        <?php adm_text('Link (optional)', 'link', $edit['link'] ?? ''); ?>
        <input type="hidden" name="is_active" value="0">
        <div class="form-group"><label class="chk"><input type="checkbox" name="is_active" value="1" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>> Active</label></div>
        <button class="btn btn-blue"><?= $edit ? 'Update' : 'Add Poster' ?></button>
        <?php if ($edit): ?><a href="<?= e(adm('posters')) ?>" class="btn btn-sm btn-gray" style="margin-left:8px;">Cancel</a><?php endif; ?>
    </form>
</div>
<div class="card">
    <h3>All Posters</h3>
    <table>
        <tr><th>Image</th><th>Title</th><th>Order</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($rows as $p): ?>
        <tr>
            <td><?php if ($p['image']): ?><img class="thumb" src="<?= e(asset_url($p['image'])) ?>"><?php else: ?>—<?php endif; ?></td>
            <td><?= e($p['title']) ?></td>
            <td><?= (int)$p['sort_order'] ?></td>
            <td><span class="badge-sm <?= $p['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $p['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('posters', 'posters', $p); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
