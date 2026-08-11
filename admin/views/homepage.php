<?php
function hp_chk($key, $label) {
    echo '<div class="form-group"><label class="chk"><input type="hidden" name="s[' . e($key) . ']" value="0">'
        . '<input type="checkbox" name="s[' . e($key) . ']" value="1" ' . (cms_flag($key) ? 'checked' : '') . '> ' . e($label) . '</label></div>';
}
function hp_text($key, $label, $ph = '') {
    echo '<div class="form-group"><label>' . e($label) . '</label><input type="text" name="s[' . e($key) . ']" value="' . e(setting($key)) . '" placeholder="' . e($ph) . '"></div>';
}
function hp_area($key, $label) {
    echo '<div class="form-group"><label>' . e($label) . '</label><textarea name="s[' . e($key) . ']">' . e(setting($key)) . '</textarea></div>';
}
?>
<h2 class="page-title">Homepage</h2>
<p class="hint" style="margin-bottom:16px;">Manage every homepage section here. Repeatable content (banners, icons, posters, USPS, reviews) has its own section in the sidebar.</p>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save_settings">
    <input type="hidden" name="return" value="homepage">

    <div class="card">
        <h3>Hero / Main Banner</h3>
        <?php hp_chk('hero_enabled', 'Show hero section'); ?>
        <div class="form-group"><label>Background Type</label>
            <select name="s[hero_type]">
                <option value="video" <?= setting('hero_type') === 'video' ? 'selected' : '' ?>>Video</option>
                <option value="image" <?= setting('hero_type') === 'image' ? 'selected' : '' ?>>Image</option>
            </select>
        </div>
        <?php hp_text('hero_video', 'Background Video URL (mp4)'); ?>
        <div class="form-group"><label>Background Image</label>
            <?php if (setting('hero_image')): ?><div class="img-preview"><img src="<?= e(asset_url(setting('hero_image'))) ?>"></div><?php endif; ?>
            <input type="file" name="file_hero_image" accept="image/*" onchange="previewImg(this)">
            <input type="text" name="s[hero_image]" value="<?= e(setting('hero_image')) ?>" class="mt8" placeholder="or image path/URL">
        </div>
        <?php hp_text('hero_title', 'Heading'); ?>
        <?php hp_text('hero_subtitle', 'Subheading'); ?>
        <div class="form-row">
            <?php hp_text('hero_btn1_text', 'Button 1 Text'); hp_text('hero_btn1_link', 'Button 1 Link'); ?>
        </div>
        <div class="form-row">
            <?php hp_text('hero_btn2_text', 'Button 2 Text'); hp_text('hero_btn2_link', 'Button 2 Link'); ?>
        </div>
    </div>

    <div class="card">
        <h3>Products / Categories Section</h3>
        <?php hp_chk('homeproducts_enabled', 'Show categories grid'); ?>
        <div class="form-row-3">
            <?php hp_text('homeproducts_overline', 'Overline'); hp_text('homeproducts_title', 'Title'); hp_text('homeproducts_subtitle', 'Subtitle'); ?>
        </div>
    </div>

    <div class="card">
        <h3>Mid Banner Section</h3>
        <?php hp_chk('midbanner_enabled', 'Show mid banner section'); ?>
        <p class="hint">Manage the banner images under <a href="<?= e(adm('banners')) ?>">Banners</a> (position: mid).</p>
    </div>

    <div class="card">
        <h3>Clean Energy / Feature Icons Section</h3>
        <?php hp_chk('cleanenergy_enabled', 'Show feature icons section'); ?>
        <?php hp_text('cleanenergy_title', 'Section Title'); ?>
        <p class="hint">Manage the feature items under <a href="<?= e(adm('icons')) ?>">Icons / Features</a>.</p>
    </div>

    <div class="card">
        <h3>About-on-Home Section</h3>
        <?php hp_chk('abouthome_enabled', 'Show about section'); ?>
        <?php hp_text('abouthome_title', 'Title'); ?>
        <?php hp_area('abouthome_text', 'Text'); ?>
        <div class="form-group"><label>Image</label>
            <?php if (setting('abouthome_image')): ?><div class="img-preview"><img src="<?= e(asset_url(setting('abouthome_image'))) ?>"></div><?php endif; ?>
            <input type="file" name="file_abouthome_image" accept="image/*" onchange="previewImg(this)">
            <input type="text" name="s[abouthome_image]" value="<?= e(setting('abouthome_image')) ?>" class="mt8" placeholder="or image path/URL">
        </div>
        <div class="form-row">
            <?php hp_text('abouthome_btn_text', 'Button Text'); hp_text('abouthome_btn_link', 'Button Link'); ?>
        </div>
    </div>

    <div class="card">
        <h3>USPS Section</h3>
        <?php hp_chk('usps_enabled', 'Show USPS section'); ?>
        <div class="form-row"><?php hp_text('usps_title', 'Title'); hp_text('usps_subtitle', 'Subtitle'); ?></div>
        <p class="hint">Manage the USP items under <a href="<?= e(adm('usps')) ?>">USPS</a>.</p>
    </div>

    <div class="card">
        <h3>Posters / Certificates Section</h3>
        <?php hp_chk('posters_enabled', 'Show posters section'); ?>
        <?php hp_text('posters_title', 'Section Title'); ?>
        <p class="hint">Manage the poster images under <a href="<?= e(adm('posters')) ?>">Posters</a>.</p>
    </div>

    <div class="card">
        <h3>Reviews Section</h3>
        <?php hp_chk('reviews_enabled', 'Show reviews section'); ?>
        <div class="form-row"><?php hp_text('reviews_title', 'Title'); hp_text('reviews_subtitle', 'Subtitle'); ?></div>
        <p class="hint">Manage individual reviews under <a href="<?= e(adm('reviews')) ?>">Reviews</a>.</p>
    </div>

    <div class="card">
        <h3>Section Order</h3>
        <p class="hint">Comma-separated order of homepage sections. Available keys: hero, products, midbanner, cleanenergy, abouthome, usps, posters.</p>
        <?php hp_text('home_order', 'Order'); ?>
    </div>

    <button class="btn btn-blue">Save Homepage</button>
</form>
