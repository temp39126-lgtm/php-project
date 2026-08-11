<?php
$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM banners WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$rows = $db->query("SELECT * FROM banners ORDER BY sort_order, id")->fetchAll();
$positions = ['mid' => 'Mid (homepage banner strip)', 'hero' => 'Hero area', 'bottom' => 'Bottom', 'other' => 'Other'];
?>
<h2 class="page-title">Banners</h2>
<div class="card">
    <h3><?= $edit ? 'Edit Banner' : 'Add Banner' ?></h3>
    <p class="hint">Homepage promotional banners. "Mid" banners appear in the banner strip on the homepage.</p>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_banner">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        <div class="form-group"><label>Position</label>
            <select name="position">
                <?php foreach ($positions as $k => $v): ?>
                <option value="<?= $k ?>" <?= (($edit['position'] ?? 'mid') === $k) ? 'selected' : '' ?>><?= e($v) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="current_image" value="<?= e($edit['image'] ?? '') ?>">
        <?php adm_image_field('Banner Image', 'image_file', 'image_url', $edit['image'] ?? ''); ?>
        <div class="form-row">
            <?php adm_text('Title (optional)', 'title', $edit['title'] ?? ''); ?>
            <?php adm_text('Subtitle (optional)', 'subtitle', $edit['subtitle'] ?? ''); ?>
        </div>
        <div class="form-row">
            <?php adm_text('Button Text (optional)', 'button_text', $edit['button_text'] ?? ''); ?>
            <?php adm_text('Button Link (optional)', 'button_link', $edit['button_link'] ?? ''); ?>
        </div>
        <input type="hidden" name="is_active" value="0">
        <div class="form-group"><label class="chk"><input type="checkbox" name="is_active" value="1" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>> Active</label></div>
        <button class="btn btn-blue"><?= $edit ? 'Update' : 'Add Banner' ?></button>
        <?php if ($edit): ?><a href="<?= e(adm('banners')) ?>" class="btn btn-sm btn-gray" style="margin-left:8px;">Cancel</a><?php endif; ?>
    </form>
</div>
<div class="card">
    <h3>All Banners</h3>
    <table>
        <tr><th>Image</th><th>Position</th><th>Title</th><th>Order</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($rows as $b): ?>
        <tr>
            <td><?php if ($b['image']): ?><img class="thumb" src="<?= e(asset_url($b['image'])) ?>"><?php else: ?>—<?php endif; ?></td>
            <td><?= e($b['position']) ?></td>
            <td><?= e($b['title']) ?></td>
            <td><?= (int)$b['sort_order'] ?></td>
            <td><span class="badge-sm <?= $b['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $b['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('banners', 'banners', $b); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
