<?php
// Theme / typography settings (colors, font sizes, font families).

function cms_theme_default_settings() {
    return [
        // Site-wide colors
        'theme_color_primary'       => 'rgb(61,99,204)',
        'theme_color_primary_light' => 'rgb(14,132,235)',
        'theme_color_primary_dark'  => '#0d141a',
        'theme_color_text'          => '#0d141a',
        'theme_color_text_light'    => '#4a4a4a',
        'theme_color_heading'       => '#0d141a',
        'theme_color_body_bg'       => '#ffffff',
        'theme_color_btn_text'      => '#ffffff',

        // Site-wide typography (px unless noted)
        'theme_font_body'           => 'poppins',
        'theme_font_heading'        => 'playfair',
        'theme_body_font_size'      => '16',
        'theme_nav_link_size'       => '15',
        'theme_btn_font_size'       => '15',

        // Homepage — shared section titles (products, USPS, reviews)
        'theme_home_section_h2_size'   => '38',
        'theme_home_section_h2_color'  => '#0d141a',
        'theme_home_overline_size'     => '13',
        'theme_home_overline_color'    => 'rgb(61,99,204)',
        'theme_home_section_p_size'    => '16',
        'theme_home_section_p_color'   => '#4a4a4a',

        // Homepage — hero block
        'theme_home_hero_h1_size'      => '56',
        'theme_home_hero_h1_color'     => '#ffffff',
        'theme_home_hero_p_size'       => '18',
        'theme_home_hero_p_color'      => '#ffffff',

        // Homepage — top/mid sliders (slide content)
        'theme_home_slider_h2_size'    => '52',
        'theme_home_slider_h2_color'   => '#ffffff',
        'theme_home_slider_p_size'     => '18',
        'theme_home_slider_p_color'    => '#ffffff',

        // Homepage — clean energy strip
        'theme_home_clean_bg'          => '#0d141a',
        'theme_home_clean_h1_size'     => '42',
        'theme_home_clean_h1_color'    => '#ffffff',
        'theme_home_feature_h4_size'   => '16',
        'theme_home_feature_h4_color'  => '#ffffff',
        'theme_home_feature_p_size'    => '14',
        'theme_home_feature_p_color'   => '#ffffff',

        // Homepage — about-on-home
        'theme_home_abouthome_h3_size' => '32',
        'theme_home_abouthome_h3_color'=> '#0d141a',
        'theme_home_abouthome_p_size'  => '15',
        'theme_home_abouthome_p_color' => '#4a4a4a',

        // Homepage — USPS cards
        'theme_home_usp_h4_size'       => '16',
        'theme_home_usp_h4_color'      => '#0d141a',
        'theme_home_usp_p_size'        => '14',
        'theme_home_usp_p_color'       => '#4a4a4a',

        // Homepage — reviews
        'theme_home_reviews_h3_size'   => '30',
        'theme_home_reviews_h3_color'  => '#0d141a',
        'theme_home_reviews_p_size'    => '15',
        'theme_home_reviews_p_color'   => '#4a4a4a',

        // About Us page
        'theme_about_hero_h1_size'     => '48',
        'theme_about_hero_h1_color'    => '#ffffff',
        'theme_about_intro_size'       => '16',
        'theme_about_intro_color'      => '#4a4a4a',
        'theme_about_h3_size'          => '28',
        'theme_about_h3_color'         => '#0d141a',
        'theme_about_grid_p_size'      => '15',
        'theme_about_grid_p_color'     => '#4a4a4a',
        'theme_about_h5_size'          => '20',
        'theme_about_h5_color'         => '#0d141a',
        'theme_about_h6_size'          => '16',
        'theme_about_h6_color'         => 'rgb(61,99,204)',
        'theme_about_detail_p_size'    => '15',
        'theme_about_detail_p_color'   => '#4a4a4a',
    ];
}

function cms_theme_color_keys() {
    $keys = [];
    foreach (cms_theme_default_settings() as $k => $v) {
        if (strpos($k, '_color') !== false || substr($k, -3) === '_bg' || str_contains($k, '_bg')) {
            $keys[$k] = true;
        }
    }
    return array_keys($keys);
}

function cms_theme_px_specs() {
    return [
        'theme_body_font_size'         => [12, 24],
        'theme_nav_link_size'          => [12, 22],
        'theme_btn_font_size'          => [12, 22],
        'theme_home_section_h2_size'   => [20, 72],
        'theme_home_overline_size'     => [10, 24],
        'theme_home_section_p_size'    => [12, 24],
        'theme_home_hero_h1_size'      => [28, 80],
        'theme_home_hero_p_size'       => [12, 28],
        'theme_home_slider_h2_size'    => [24, 72],
        'theme_home_slider_p_size'     => [12, 28],
        'theme_home_clean_h1_size'     => [22, 64],
        'theme_home_feature_h4_size'   => [12, 28],
        'theme_home_feature_p_size'    => [12, 22],
        'theme_home_abouthome_h3_size' => [20, 48],
        'theme_home_abouthome_p_size'  => [12, 22],
        'theme_home_usp_h4_size'       => [12, 28],
        'theme_home_usp_p_size'        => [12, 22],
        'theme_home_reviews_h3_size'   => [18, 48],
        'theme_home_reviews_p_size'    => [12, 22],
        'theme_about_hero_h1_size'     => [24, 72],
        'theme_about_intro_size'       => [12, 24],
        'theme_about_h3_size'          => [18, 48],
        'theme_about_grid_p_size'      => [12, 22],
        'theme_about_h5_size'          => [14, 32],
        'theme_about_h6_size'          => [12, 24],
        'theme_about_detail_p_size'    => [12, 22],
    ];
}

function cms_theme_color_css_map() {
    return [
        'theme_color_primary'       => '--primary',
        'theme_color_primary_light' => '--primary-light',
        'theme_color_primary_dark'  => '--primary-dark',
        'theme_color_text'          => '--text',
        'theme_color_text_light'    => '--text-light',
        'theme_color_heading'       => '--dark',
        'theme_color_body_bg'       => '--body-bg',
        'theme_color_btn_text'      => '--btn-primary-text',
        'theme_home_section_h2_color'  => '--home-section-h2-color',
        'theme_home_overline_color'    => '--home-overline-color',
        'theme_home_section_p_color'   => '--home-section-p-color',
        'theme_home_hero_h1_color'     => '--home-hero-h1-color',
        'theme_home_hero_p_color'      => '--home-hero-p-color',
        'theme_home_slider_h2_color'   => '--home-slider-h2-color',
        'theme_home_slider_p_color'    => '--home-slider-p-color',
        'theme_home_clean_bg'          => '--home-clean-bg',
        'theme_home_clean_h1_color'    => '--home-clean-h1-color',
        'theme_home_feature_h4_color'  => '--home-feature-h4-color',
        'theme_home_feature_p_color'   => '--home-feature-p-color',
        'theme_home_abouthome_h3_color'=> '--home-abouthome-h3-color',
        'theme_home_abouthome_p_color' => '--home-abouthome-p-color',
        'theme_home_usp_h4_color'      => '--home-usp-h4-color',
        'theme_home_usp_p_color'       => '--home-usp-p-color',
        'theme_home_reviews_h3_color'  => '--home-reviews-h3-color',
        'theme_home_reviews_p_color'   => '--home-reviews-p-color',
        'theme_about_hero_h1_color'    => '--about-hero-h1-color',
        'theme_about_intro_color'      => '--about-intro-color',
        'theme_about_h3_color'         => '--about-h3-color',
        'theme_about_grid_p_color'     => '--about-grid-p-color',
        'theme_about_h5_color'         => '--about-h5-color',
        'theme_about_h6_color'         => '--about-h6-color',
        'theme_about_detail_p_color'   => '--about-detail-p-color',
    ];
}

function cms_theme_px_css_map() {
    return [
        'theme_body_font_size'         => '--body-font-size',
        'theme_nav_link_size'          => '--nav-link-size',
        'theme_btn_font_size'          => '--btn-font-size',
        'theme_home_section_h2_size'   => '--home-section-h2-size',
        'theme_home_overline_size'     => '--home-overline-size',
        'theme_home_section_p_size'    => '--home-section-p-size',
        'theme_home_hero_h1_size'      => '--home-hero-h1-size',
        'theme_home_hero_p_size'       => '--home-hero-p-size',
        'theme_home_slider_h2_size'    => '--home-slider-h2-size',
        'theme_home_slider_p_size'     => '--home-slider-p-size',
        'theme_home_clean_h1_size'     => '--home-clean-h1-size',
        'theme_home_feature_h4_size'   => '--home-feature-h4-size',
        'theme_home_feature_p_size'    => '--home-feature-p-size',
        'theme_home_abouthome_h3_size' => '--home-abouthome-h3-size',
        'theme_home_abouthome_p_size'  => '--home-abouthome-p-size',
        'theme_home_usp_h4_size'       => '--home-usp-h4-size',
        'theme_home_usp_p_size'        => '--home-usp-p-size',
        'theme_home_reviews_h3_size'   => '--home-reviews-h3-size',
        'theme_home_reviews_p_size'    => '--home-reviews-p-size',
        'theme_about_hero_h1_size'     => '--about-hero-h1-size',
        'theme_about_intro_size'       => '--about-intro-size',
        'theme_about_h3_size'          => '--about-h3-size',
        'theme_about_grid_p_size'      => '--about-grid-p-size',
        'theme_about_h5_size'          => '--about-h5-size',
        'theme_about_h6_size'          => '--about-h6-size',
        'theme_about_detail_p_size'    => '--about-detail-p-size',
    ];
}

function cms_sanitize_color($value) {
    $v = trim((string) $value);
    if ($v === '') {
        return '';
    }
    if (preg_match('~^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$~', $v)) {
        return $v;
    }
    if (preg_match('~^rgba?\(\s*[\d.,\s%]+\s*\)$~', $v)) {
        return $v;
    }
    return '';
}

function cms_theme_setting_color($key) {
    $defaults = cms_theme_default_settings();
    $fallback = $defaults[$key] ?? '#0d141a';
    $raw = trim((string) setting($key));
    if ($raw === '') {
        return cms_sanitize_color($fallback) ?: $fallback;
    }
    $clean = cms_sanitize_color($raw);
    if ($clean !== '') {
        return $clean;
    }
    return cms_sanitize_color($fallback) ?: $fallback;
}

function cms_theme_setting_px($key) {
    $specs = cms_theme_px_specs();
    if (!isset($specs[$key])) {
        return (int) setting($key);
    }
    [$min, $max] = $specs[$key];
    $defaults = cms_theme_default_settings();
    $fallback = (int) ($defaults[$key] ?? $min);
    $n = (int) setting($key);
    if ($n < $min) {
        $n = max($min, $fallback);
    }
    if ($n > $max) {
        $n = $max;
    }
    return $n;
}

function cms_clamp_theme_px($key, $value) {
    $specs = cms_theme_px_specs();
    if (!isset($specs[$key])) {
        return [(string) $value, false];
    }
    [$min, $max] = $specs[$key];
    $n = (int) $value;
    $clamped = max($min, min($max, $n));
    return [(string) $clamped, $clamped !== $n];
}

function cms_clamp_theme_color($key, $value) {
    $defaults = cms_theme_default_settings();
    $fallback = $defaults[$key] ?? '#000000';
    $clean = cms_sanitize_color($value);
    if ($clean === '') {
        $clean = cms_sanitize_color($fallback) ?: $fallback;
        return [$clean, trim((string) $value) !== ''];
    }
    return [$clean, false];
}

function cms_theme_font_stacks() {
    return [
        'poppins'  => "'Poppins',sans-serif",
        'playfair' => "'Playfair Display',serif",
        'system'   => 'system-ui,-apple-system,sans-serif',
    ];
}

function cms_theme_font_stack($role) {
    $map = cms_theme_font_stacks();
    $key = 'theme_font_' . $role;
    $choice = setting($key);
    $defaults = cms_theme_default_settings();
    if ($choice === '' || !isset($map[$choice])) {
        $choice = $defaults[$key] ?? 'poppins';
    }
    return $map[$choice] ?? $map['poppins'];
}

function cms_theme_css_variables() {
    $parts = [];
    foreach (cms_theme_color_css_map() as $settingKey => $var) {
        $parts[] = $var . ':' . cms_theme_setting_color($settingKey);
    }
    foreach (cms_theme_px_css_map() as $settingKey => $var) {
        $parts[] = $var . ':' . cms_theme_setting_px($settingKey) . 'px';
    }
    $parts[] = '--font-body:' . cms_theme_font_stack('body');
    $parts[] = '--font-heading:' . cms_theme_font_stack('heading');
    return implode(';', $parts);
}

function cms_storefront_css_variables() {
    return cms_size_css_variables() . ';' . cms_theme_css_variables();
}
