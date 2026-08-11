<?php
$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM categories WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$rows = $db->query("SELECT * FROM categories ORDER BY sort_order, id")->fetchAll();
?>
<h2 class="page-title">Categories</h2>
<div class="card">
    <h3><?= $edit ? 'Edit Category' : 'Add New Category' ?></h3>
    <p class="hint">Categories appear on the homepage grid and the Products page, and each gets its own page at /slug.</p>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_category">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <?php adm_text('Name *', 'name', $edit['name'] ?? '', 'text', 'required'); ?>
            <?php adm_text('Sort Order', 'sort_order', $edit['sort_order'] ?? 0, 'number'); ?>
        </div>
        <?php adm_textarea('Description', 'description', $edit['description'] ?? ''); ?>
        <input type="hidden" name="current_image" value="<?= e($edit['image'] ?? '') ?>">
        <?php adm_image_field('Category Image', 'image_file', 'image_url', $edit['image'] ?? ''); ?>
        <input type="hidden" name="is_active" value="0">
        <div class="form-group"><label class="chk"><input type="checkbox" name="is_active" value="1" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>> Active</label></div>
        <button class="btn btn-blue"><?= $edit ? 'Update' : 'Add Category' ?></button>
        <?php if ($edit): ?><a href="<?= e(adm('categories')) ?>" class="btn btn-sm btn-gray" style="margin-left:8px;">Cancel</a><?php endif; ?>
    </form>
</div>
<div class="card">
    <h3>All Categories</h3>
    <table>
        <tr><th>Image</th><th>Name</th><th>URL</th><th>Order</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($rows as $c): ?>
        <tr>
            <td><?php if ($c['image']): ?><img class="thumb" src="<?= e(asset_url($c['image'])) ?>"><?php else: ?>—<?php endif; ?></td>
            <td><strong><?= e($c['name']) ?></strong></td>
            <td><a href="/<?= e($c['slug']) ?>" target="_blank">/<?= e($c['slug']) ?></a></td>
            <td><?= (int)$c['sort_order'] ?></td>
            <td><span class="badge-sm <?= $c['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $c['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('categories', 'categories', $c); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
