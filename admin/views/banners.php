<?php
$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM banners WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$rows = $db->query("SELECT * FROM banners ORDER BY sort_order, id")->fetchAll();
$positions = [
    'slider' => 'Slider (homepage top carousel)',
    'mid'    => 'Mid (homepage banner strip)',
    'hero'   => 'Hero area (homepage, below top slider)',
    'bottom' => 'Bottom (above footer, all pages)',
    'other'  => 'Other (end of homepage)',
];
?>
<h2 class="page-title">Banners</h2>
<div class="card">
    <h3>Display Size</h3>
    <p class="hint">These heights apply on the public site immediately after you save (hard-refresh the homepage if you still see the old size).</p>
    <form method="POST">
        <input type="hidden" name="action" value="save_settings">
        <input type="hidden" name="return" value="banners">
        <div class="form-row">
            <div class="form-group">
                <label>Top slider height (pixels)</label>
                <input type="number" name="s[slider_size]" value="<?= cms_setting_px('slider_size', 200, 900) ?>" min="200" max="900" step="10">
                <p class="hint">Homepage carousel (banner position: Slider). Default 460.</p>
            </div>
            <div class="form-group">
                <label>Mid / bottom banner height (pixels)</label>
                <input type="number" name="s[banner_size]" value="<?= cms_setting_px('banner_size', 120, 900) ?>" min="120" max="900" step="10">
                <p class="hint">Mid, bottom, and other strips. Default 380.</p>
            </div>
        </div>
        <div class="form-group">
            <label>Slide autoplay speed (seconds)</label>
            <input type="number" name="s[slider_speed_sec]" value="<?= (int) round(cms_slider_interval_ms() / 100) / 10 ?>" min="2" max="60" step="0.5">
            <p class="hint">How long each slide stays visible before advancing (2–60 seconds). Applies to the top slider and all banner carousels. Default 5.5.</p>
        </div>
        <button class="btn btn-blue">Save Banner Settings</button>
    </form>
</div>
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
        <?php adm_text('Background Video URL (mp4) — for slider video slides; overrides image when set', 'video', $edit['video'] ?? '', 'text', 'placeholder="https://.../video.mp4"'); ?>
        <div class="form-row">
            <?php adm_text('Title (optional)', 'title', $edit['title'] ?? ''); ?>
            <?php adm_text('Subtitle (optional)', 'subtitle', $edit['subtitle'] ?? ''); ?>
        </div>
        <div class="form-row">
            <?php adm_text('Button Text (optional)', 'button_text', $edit['button_text'] ?? ''); ?>
            <?php adm_text('Button Link (optional)', 'button_link', $edit['button_link'] ?? ''); ?>
        </div>
        <div class="form-row">
            <?php adm_text('Second Button Text (optional)', 'button2_text', $edit['button2_text'] ?? ''); ?>
            <?php adm_text('Second Button Link (optional)', 'button2_link', $edit['button2_link'] ?? ''); ?>
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
            <td><?php if (!empty($b['video'])): ?><span class="badge-sm badge-gray">▶ video</span><?php elseif ($b['image']): ?><img class="thumb" src="<?= e(asset_url($b['image'])) ?>"><?php else: ?>—<?php endif; ?></td>
            <td><?= e($b['position']) ?></td>
            <td><?= e($b['title']) ?></td>
            <td><?= (int)$b['sort_order'] ?></td>
            <td><span class="badge-sm <?= $b['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $b['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('banners', 'banners', $b); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
