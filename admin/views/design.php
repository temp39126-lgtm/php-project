<?php
function design_color($key, $label) {
    $val = cms_theme_setting_color($key);
    echo '<div class="form-group"><label>' . e($label) . '</label>';
    echo '<input type="text" name="s[' . e($key) . ']" value="' . e($val) . '" placeholder="#0d141a or rgb(61,99,204)"></div>';
}
function design_px($key, $label) {
    $specs = cms_theme_px_specs();
    [$min, $max] = $specs[$key] ?? [10, 100];
    echo '<div class="form-group"><label>' . e($label) . ' (px)</label>';
    echo '<input type="number" name="s[' . e($key) . ']" value="' . (int) cms_theme_setting_px($key) . '" min="' . (int) $min . '" max="' . (int) $max . '"></div>';
}
$fontStacks = cms_theme_font_stacks();
$fontBody = setting('theme_font_body') ?: 'poppins';
$fontHeading = setting('theme_font_heading') ?: 'playfair';
?>
<h2 class="page-title">Design — Fonts &amp; Colors</h2>
<p class="hint" style="margin-bottom:16px;">Controls site-wide colors and typography for the <strong>homepage</strong> and <strong>About Us</strong> page. Save here, then hard-refresh the public site. Content text is still edited under <a href="<?= e(adm('homepage')) ?>">Homepage</a> and <a href="<?= e(adm('about')) ?>">About Us Page</a>.</p>

<form method="POST">
    <input type="hidden" name="action" value="save_settings">
    <input type="hidden" name="return" value="design">

    <div class="card">
        <h3>Site-wide</h3>
        <p class="hint">Brand colors and base text sizes (navigation, buttons, body text).</p>
        <div class="form-row">
            <?php design_color('theme_color_primary', 'Primary brand color'); ?>
            <?php design_color('theme_color_primary_light', 'Primary light (hovers)'); ?>
            <?php design_color('theme_color_primary_dark', 'Primary dark (dark sections)'); ?>
        </div>
        <div class="form-row">
            <?php design_color('theme_color_text', 'Body text color'); ?>
            <?php design_color('theme_color_text_light', 'Muted text color'); ?>
            <?php design_color('theme_color_heading', 'Heading color (default)'); ?>
        </div>
        <div class="form-row">
            <?php design_color('theme_color_body_bg', 'Page background'); ?>
            <?php design_color('theme_color_btn_text', 'Primary button text'); ?>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Body font</label>
                <select name="s[theme_font_body]">
                    <?php foreach ($fontStacks as $slug => $_): ?>
                    <option value="<?= e($slug) ?>" <?= $fontBody === $slug ? 'selected' : '' ?>><?= e(ucfirst($slug)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Heading font</label>
                <select name="s[theme_font_heading]">
                    <?php foreach ($fontStacks as $slug => $_): ?>
                    <option value="<?= e($slug) ?>" <?= $fontHeading === $slug ? 'selected' : '' ?>><?= e(ucfirst($slug)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <?php design_px('theme_body_font_size', 'Body font size'); ?>
            <?php design_px('theme_nav_link_size', 'Menu link size'); ?>
            <?php design_px('theme_btn_font_size', 'Button font size'); ?>
        </div>
    </div>

    <div class="card">
        <h3>Homepage — section titles</h3>
        <p class="hint">Products, USPS, reviews, and similar blocks that use the centered section title layout.</p>
        <div class="form-row">
            <?php design_px('theme_home_section_h2_size', 'Section title size'); ?>
            <?php design_color('theme_home_section_h2_color', 'Section title color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_overline_size', 'Overline size'); ?>
            <?php design_color('theme_home_overline_color', 'Overline color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_section_p_size', 'Section subtitle size'); ?>
            <?php design_color('theme_home_section_p_color', 'Section subtitle color'); ?>
        </div>
    </div>

    <div class="card">
        <h3>Homepage — hero &amp; sliders</h3>
        <div class="form-row">
            <?php design_px('theme_home_hero_h1_size', 'Hero heading size'); ?>
            <?php design_color('theme_home_hero_h1_color', 'Hero heading color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_hero_p_size', 'Hero subtext size'); ?>
            <?php design_color('theme_home_hero_p_color', 'Hero subtext color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_slider_h2_size', 'Slide heading size'); ?>
            <?php design_color('theme_home_slider_h2_color', 'Slide heading color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_slider_p_size', 'Slide text size'); ?>
            <?php design_color('theme_home_slider_p_color', 'Slide text color'); ?>
        </div>
    </div>

    <div class="card">
        <h3>Homepage — Clean Energy, About block, USPS, Reviews</h3>
        <div class="form-row">
            <?php design_color('theme_home_clean_bg', 'Clean Energy background'); ?>
            <?php design_px('theme_home_clean_h1_size', 'Clean Energy title size'); ?>
            <?php design_color('theme_home_clean_h1_color', 'Clean Energy title color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_feature_h4_size', 'Feature icon title size'); ?>
            <?php design_color('theme_home_feature_h4_color', 'Feature icon title color'); ?>
            <?php design_px('theme_home_feature_p_size', 'Feature icon text size'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_abouthome_h3_size', 'About-on-home title size'); ?>
            <?php design_color('theme_home_abouthome_h3_color', 'About-on-home title color'); ?>
            <?php design_px('theme_home_abouthome_p_size', 'About-on-home text size'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_usp_h4_size', 'USP card title size'); ?>
            <?php design_color('theme_home_usp_h4_color', 'USP card title color'); ?>
            <?php design_px('theme_home_usp_p_size', 'USP card text size'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_home_reviews_h3_size', 'Reviews title size'); ?>
            <?php design_color('theme_home_reviews_h3_color', 'Reviews title color'); ?>
            <?php design_px('theme_home_reviews_p_size', 'Reviews subtitle size'); ?>
        </div>
    </div>

    <div class="card">
        <h3>About Us page (/about-us)</h3>
        <div class="form-row">
            <?php design_px('theme_about_hero_h1_size', 'Hero title size'); ?>
            <?php design_color('theme_about_hero_h1_color', 'Hero title color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_about_intro_size', 'Intro paragraph size'); ?>
            <?php design_color('theme_about_intro_color', 'Intro paragraph color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_about_h3_size', 'Who / Vision heading size'); ?>
            <?php design_color('theme_about_h3_color', 'Who / Vision heading color'); ?>
            <?php design_px('theme_about_grid_p_size', 'Who / Vision body size'); ?>
        </div>
        <div class="form-row">
            <?php design_color('theme_about_grid_p_color', 'Who / Vision body color'); ?>
            <?php design_px('theme_about_h5_size', 'Detail block heading size'); ?>
            <?php design_color('theme_about_h5_color', 'Detail block heading color'); ?>
        </div>
        <div class="form-row">
            <?php design_px('theme_about_h6_size', 'Detail subheading size'); ?>
            <?php design_color('theme_about_h6_color', 'Detail subheading color'); ?>
            <?php design_px('theme_about_detail_p_size', 'Detail body size'); ?>
        </div>
        <div class="form-row">
            <?php design_color('theme_about_detail_p_color', 'Detail body color'); ?>
        </div>
    </div>

    <button class="btn btn-blue">Save Design Settings</button>
</form>
