<?php
function about_text($key, $label) {
    echo '<div class="form-group"><label>' . e($label) . '</label><input type="text" name="s[' . e($key) . ']" value="' . e(setting($key)) . '"></div>';
}
function about_area($key, $label) {
    echo '<div class="form-group"><label>' . e($label) . '</label><textarea name="s[' . e($key) . ']" rows="4">' . e(setting($key)) . '</textarea></div>';
}
?>
<h2 class="page-title">About Us Page</h2>
<p class="hint" style="margin-bottom:16px;">Edits the public page at <a href="<?= e(link_url('/about-us')) ?>" target="_blank" rel="noopener">/about-us</a>. Leave a field empty to hide that piece on the site (except the hero title, which falls back to “About Us”). Font sizes and colors: <a href="<?= e(adm('design')) ?>">Design / Fonts &amp; Colors</a>.</p>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="save_settings">
    <input type="hidden" name="return" value="about">

    <div class="card">
        <h3>Top banner (hero)</h3>
        <div class="form-group">
            <label>Hero background image</label>
            <?php if (setting('about_hero_image')): ?><div class="img-preview"><img src="<?= e(asset_url(setting('about_hero_image'))) ?>"></div><?php endif; ?>
            <input type="file" name="file_about_hero_image" accept="image/*" onchange="previewImg(this)">
            <input type="text" name="s[about_hero_image]" value="<?= e(setting('about_hero_image')) ?>" class="mt8" placeholder="Upload or paste image path/URL">
        </div>
        <?php about_text('about_hero_title', 'Page heading (over image)'); ?>
    </div>

    <div class="card">
        <h3>Introduction</h3>
        <?php about_area('about_intro', 'Intro paragraph'); ?>
    </div>

    <div class="card">
        <h3>Who we are &amp; vision (two columns)</h3>
        <div class="form-row">
            <div>
                <?php about_text('about_who_title', 'Left column heading'); ?>
                <?php about_area('about_who_text', 'Left column text'); ?>
            </div>
            <div>
                <?php about_text('about_vision_title', 'Right column heading'); ?>
                <?php about_area('about_vision_text', 'Right column text'); ?>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Full-width banner image</h3>
        <div class="form-group">
            <label>Banner image</label>
            <?php if (setting('about_banner_image')): ?><div class="img-preview"><img src="<?= e(asset_url(setting('about_banner_image'))) ?>"></div><?php endif; ?>
            <input type="file" name="file_about_banner_image" accept="image/*" onchange="previewImg(this)">
            <input type="text" name="s[about_banner_image]" value="<?= e(setting('about_banner_image')) ?>" class="mt8" placeholder="Upload or paste image path/URL">
        </div>
        <?php about_text('about_banner_alt', 'Image alt text'); ?>
    </div>

    <div class="card">
        <h3>Content block 1</h3>
        <?php about_text('about_block1_title', 'Heading'); ?>
        <?php about_area('about_block1_text', 'Body text'); ?>
    </div>

    <div class="card">
        <h3>Content block 2</h3>
        <?php about_text('about_block2_title', 'Heading'); ?>
        <?php about_area('about_block2_text', 'Intro text'); ?>
        <div class="form-row">
            <div>
                <?php about_text('about_block2_sub1_title', 'Subheading A'); ?>
                <?php about_area('about_block2_sub1_text', 'Text A'); ?>
            </div>
            <div>
                <?php about_text('about_block2_sub2_title', 'Subheading B'); ?>
                <?php about_area('about_block2_sub2_text', 'Text B'); ?>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Content block 3</h3>
        <?php about_text('about_block3_title', 'Heading'); ?>
        <?php about_area('about_block3_text', 'Intro text'); ?>
        <div class="form-row">
            <div>
                <?php about_text('about_block3_sub1_title', 'Subheading A'); ?>
                <?php about_area('about_block3_sub1_text', 'Text A'); ?>
            </div>
            <div>
                <?php about_text('about_block3_sub2_title', 'Subheading B'); ?>
                <?php about_area('about_block3_sub2_text', 'Text B'); ?>
            </div>
        </div>
    </div>

    <button class="btn btn-blue">Save About Page</button>
</form>
