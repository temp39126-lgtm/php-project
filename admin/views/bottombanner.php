<?php ?>
<h2 class="page-title">Bottom Banner</h2>
<div class="card">
    <h3>Call-to-action banner shown near the bottom of pages</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_settings">
        <input type="hidden" name="return" value="bottombanner">
        <div class="form-group"><label class="chk"><input type="hidden" name="s[bottombanner_enabled]" value="0"><input type="checkbox" name="s[bottombanner_enabled]" value="1" <?= cms_flag('bottombanner_enabled') ? 'checked' : '' ?>> Show bottom banner</label></div>
        <div class="form-group"><label>Background Image (optional)</label>
            <?php if (setting('bottombanner_image')): ?><div class="img-preview"><img src="<?= e(asset_url(setting('bottombanner_image'))) ?>"></div><?php endif; ?>
            <input type="file" name="file_bottombanner_image" accept="image/*" onchange="previewImg(this)">
            <input type="text" name="s[bottombanner_image]" value="<?= e(setting('bottombanner_image')) ?>" class="mt8" placeholder="or image path/URL">
        </div>
        <div class="form-group"><label>Heading</label><input type="text" name="s[bottombanner_title]" value="<?= e(setting('bottombanner_title')) ?>"></div>
        <div class="form-group"><label>Text</label><textarea name="s[bottombanner_text]"><?= e(setting('bottombanner_text')) ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>Button Text</label><input type="text" name="s[bottombanner_btn_text]" value="<?= e(setting('bottombanner_btn_text')) ?>"></div>
            <div class="form-group"><label>Button Link</label><input type="text" name="s[bottombanner_btn_link]" value="<?= e(setting('bottombanner_btn_link')) ?>"></div>
        </div>
        <button class="btn btn-blue">Save Bottom Banner</button>
    </form>
</div>
