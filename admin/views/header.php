<?php
$editMenu = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM menu_items WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $editMenu = $s->fetch();
}
$menu = $db->query("SELECT * FROM menu_items ORDER BY sort_order, id")->fetchAll();
?>
<h2 class="page-title">Header / Menu / Advertisement Bar</h2>

<div class="card">
    <h3>Logo &amp; Site Identity</h3>
    <p class="hint">The logo appears in the site header. Site name is used in the footer, page titles and admin.</p>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_settings">
        <input type="hidden" name="return" value="header">
        <div class="form-row">
            <div class="form-group"><label>Site Name</label><input type="text" name="s[site_name]" value="<?= e(setting('site_name')) ?>"></div>
            <div class="form-group"><label>Tagline</label><input type="text" name="s[site_tagline]" value="<?= e(setting('site_tagline')) ?>"></div>
        </div>
        <div class="form-group">
            <label>Logo</label>
            <?php if (setting('site_logo')): ?><div class="img-preview"><img src="<?= e(asset_url(setting('site_logo'))) ?>"></div><?php endif; ?>
            <input type="file" name="file_site_logo" accept="image/*" onchange="previewImg(this)">
            <input type="text" name="s[site_logo]" value="<?= e(setting('site_logo')) ?>" class="mt8" placeholder="or image path/URL">
        </div>
        <button class="btn btn-blue">Save Logo &amp; Identity</button>
    </form>
</div>

<div class="card">
    <h3>Advertisement Bar</h3>
    <p class="hint">The thin bar at the very top of every page.</p>
    <form method="POST">
        <input type="hidden" name="action" value="save_settings">
        <input type="hidden" name="return" value="header">
        <div class="form-group"><label class="chk"><input type="hidden" name="s[adbar_enabled]" value="0"><input type="checkbox" name="s[adbar_enabled]" value="1" <?= cms_flag('adbar_enabled') ? 'checked' : '' ?>> Show advertisement bar</label></div>
        <div class="form-group"><label>Advertisement Text</label><input type="text" name="s[adbar_text]" value="<?= e(setting('adbar_text')) ?>"></div>
        <div class="form-group"><label>Link (optional — makes the bar clickable)</label><input type="text" name="s[adbar_link]" value="<?= e(setting('adbar_link')) ?>" placeholder="/products or https://..."></div>
        <button class="btn btn-blue">Save Advertisement Bar</button>
    </form>
</div>

<div class="card">
    <h3><?= $editMenu ? 'Edit Menu Item' : 'Add Menu Item' ?></h3>
    <p class="hint">Main navigation links. The Products link automatically shows a dropdown of active categories.</p>
    <form method="POST">
        <input type="hidden" name="action" value="save_menu">
        <?php if ($editMenu): ?><input type="hidden" name="id" value="<?= (int)$editMenu['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <?php adm_text('Label *', 'label', $editMenu['label'] ?? '', 'text', 'required'); ?>
            <?php adm_text('Link (URL) *', 'url', $editMenu['url'] ?? '/', 'text', 'required'); ?>
        </div>
        <div class="form-group"><label class="chk"><input type="hidden" name="new_tab" value="0"><input type="checkbox" name="new_tab" value="1" <?= (!empty($editMenu['new_tab'])) ? 'checked' : '' ?>> Open in new tab</label></div>
        <input type="hidden" name="is_active" value="0">
        <div class="form-group"><label class="chk"><input type="checkbox" name="is_active" value="1" <?= (!$editMenu || $editMenu['is_active']) ? 'checked' : '' ?>> Active</label></div>
        <button class="btn btn-blue"><?= $editMenu ? 'Update' : 'Add Menu Item' ?></button>
        <?php if ($editMenu): ?><a href="<?= e(adm('header')) ?>" class="btn btn-sm btn-gray" style="margin-left:8px;">Cancel</a><?php endif; ?>
    </form>
    <table style="margin-top:18px;">
        <tr><th>Label</th><th>Link</th><th>Order</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($menu as $m): ?>
        <tr>
            <td><strong><?= e($m['label']) ?></strong></td>
            <td><?= e($m['url']) ?><?= $m['new_tab'] ? ' ↗' : '' ?></td>
            <td><?= (int)$m['sort_order'] ?></td>
            <td><span class="badge-sm <?= $m['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $m['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('header', 'menu_items', $m); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
