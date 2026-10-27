<?php
// ============================================
// GRIT FIT NUTRI - CMS helper layer
// ============================================
// Central access point for all database-backed, admin-managed content.
// Frontend and admin both use these helpers. Every value falls back to a
// sensible default (the original hard-coded value) when not yet set in the DB,
// so the site keeps working before/without admin configuration.

// Tables that generic reorder/toggle/delete actions are allowed to touch.
function cms_allowed_tables() {
    return [
        'categories', 'products', 'menu_items', 'banners', 'icons',
        'posters', 'usps', 'reviews', 'product_features',
        'product_variations', 'product_images', 'enquiries',
    ];
}

// Default values for singleton settings. Uses the original constants where
// available so the site is visually identical before any admin edits.
function cms_defaults() {
    static $d = null;
    if ($d !== null) return $d;
    $c = function ($name, $fallback = '') {
        return defined($name) ? constant($name) : $fallback;
    };
    $d = [
        'site_name'            => $c('SITE_NAME', 'Grit Fit Nutri'),
        'site_tagline'         => $c('SITE_TAGLINE', 'Superfoods for Super You.'),
        'site_logo'            => 'uploads/logo.png',

        // Advertisement / announcement bar
        'adbar_enabled'        => '1',
        'adbar_text'           => "Fueling the World with India's Finest Energy Bars.",
        'adbar_link'           => '',

        // Slider / carousel (homepage top)
        'slider_enabled'       => '1',

        // Hero / main banner (off by default: the hero video now lives as the
        // first slide of the slider; enable this for a separate static hero).
        'hero_enabled'         => '0',
        'hero_type'            => 'video',
        'hero_video'           => 'https://videos.pexels.com/video-files/8844271/8844271-uhd_4096_2160_24fps.mp4',
        'hero_image'           => '',
        'hero_title'           => 'Superfood Bars',
        'hero_subtitle'        => 'Fuel Your Day with Natural Energy and Health',
        'hero_btn1_text'       => 'About Us',
        'hero_btn1_link'       => '/about-us',
        'hero_btn2_text'       => 'Products',
        'hero_btn2_link'       => '/products',

        // Home "products" (categories) section
        'homeproducts_enabled' => '1',
        'homeproducts_overline' => 'Discover Greatness',
        'homeproducts_title'   => 'Explore our products',
        'homeproducts_subtitle' => 'Fuel Your Day, Naturally',

        // Mid banner section wrapper
        'midbanner_enabled'    => '1',

        // Clean energy / feature icons section
        'cleanenergy_enabled'  => '1',
        'cleanenergy_title'    => 'Clean Energy Great Taste',

        // About-on-home section
        'abouthome_enabled'    => '1',
        'abouthome_title'      => 'About Grit Fit Nutri',
        'abouthome_text'       => 'At Grit Fit Nutri, we create premium superfood energy bars that combine natural ingredients with health benefits for a modern lifestyle.',
        'abouthome_image'      => 'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,h=583,fit=crop/silemQfqUS99dRJ2/strawberry-about-mini-banner2-A3QOVoO0VXfMgyEo.jpg',
        'abouthome_btn_text'   => 'Learn More',
        'abouthome_btn_link'   => '/about-us',

        // About Us page (/about-us)
        'about_hero_image'     => 'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=1920,fit=crop/silemQfqUS99dRJ2/collective-combination-1024x402-dJo5gEZMqxU6QJ28.jpg',
        'about_hero_title'     => 'About Us',
        'about_intro'          => 'At Grit Fit Nutri, our mission is to revolutionize healthy living by providing high-quality, preservative-free superfood energy bars that fuel an active lifestyle. Committed to excellence, we ensure every product is lab-tested and made with the finest natural ingredients. Expanding globally, we aim to bring our nutritious offerings to international markets through strategic exports.',
        'about_who_title'      => 'Who Are We?',
        'about_who_text'       => 'We are people who have witnessed the imbalance in the life of working professionals as they turned to poor eating habits and stress eating. Therefore, we came to the conclusion to change it by taking the right action. At Gritfit, we focus on developing goods that can solve a number of health issues while being incredibly tasty.',
        'about_vision_title'   => 'Our Vision',
        'about_vision_text'    => 'At Grit Fit Nutri, our mission is to promote a healthier lifestyle by delivering premium, preservative-free superfood energy bars packed with natural nutrition. We are committed to quality, innovation, and sustainability, ensuring that our products meet global standards.',
        'about_banner_image'   => 'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=1920,fit=crop/silemQfqUS99dRJ2/collective-combination-1024x402-Yleqa574RZUoMZPw.jpg',
        'about_banner_alt'     => 'Products',
        'about_block1_title'   => 'GritFit Nutri – Your Trusted Partner for Global Nutrition Exports',
        'about_block1_text'    => 'GritFit Nutri is a leading manufacturer of premium nutritional products, specializing in white-label and private-label solutions. With world-class production facilities, stringent quality standards, and a commitment to international markets, we empower businesses to scale globally with confidence.',
        'about_block2_title'   => 'Scalable Manufacturing & Quality Assurance',
        'about_block2_text'    => 'Our state-of-the-art facilities are designed to meet high-volume demands while maintaining exceptional quality.',
        'about_block2_sub1_title' => 'Global Compliance & Certifications',
        'about_block2_sub1_text'  => 'FDA-approved, GMP-certified, and compliant with ISO, HACCP, Kosher, and Halal standards.',
        'about_block2_sub2_title' => 'Custom Manufacturing Solutions',
        'about_block2_sub2_text'  => 'Tailored formulations and flexible production capabilities to meet unique global market requirements.',
        'about_block3_title'   => 'White-Label & Private-Label Solutions for Global Expansion',
        'about_block3_text'    => 'We provide comprehensive private-label and white-label solutions to businesses worldwide.',
        'about_block3_sub1_title' => 'Tailored Product Development',
        'about_block3_sub1_text'  => 'Custom formulations aligned with international market preferences.',
        'about_block3_sub2_title' => 'Institutional & Retail Partnerships',
        'about_block3_sub2_text'  => 'Open to direct tie-ups with supermarket and convenience store chains, aviation sector, HoReCa, and institutional sales.',

        // USPS section
        'usps_enabled'         => '1',
        'usps_title'           => 'Why Choose Us',
        'usps_subtitle'        => '',

        // Posters / certificates strip
        'posters_enabled'      => '1',
        'posters_title'        => 'Our Certificates',

        // Reviews section
        'reviews_enabled'      => '1',
        'reviews_title'        => 'Customer Reviews',
        'reviews_subtitle'     => "See what our customers say about Grit Fit Nutri's energy bars.",

        // Bottom banner (call to action)
        'bottombanner_enabled' => '1',
        'bottombanner_image'   => '',
        'bottombanner_title'   => 'Ready to Fuel Your Day?',
        'bottombanner_text'    => 'Explore our range of premium superfood energy bars made with clean, natural ingredients.',
        'bottombanner_btn_text' => 'Shop Now',
        'bottombanner_btn_link' => '/products',

        // Footer
        'footer_enabled'       => '1',
        'footer_newsletter_enabled' => '1',
        'footer_copy'          => '© ' . date('Y') . '. All rights reserved.',

        // Contact / social (shared)
        'contact_email'        => $c('SITE_EMAIL', 'info@gritfitnutri.com'),
        'contact_phone'        => $c('SITE_PHONE', '+91 9811242068'),
        'contact_whatsapp'     => $c('WHATSAPP_NUMBER', '919811242068'),
        'contact_address'      => $c('SITE_ADDRESS', 'B-161, 3rd Floor, Lok Vihar, Pitampura, New Delhi, 110034'),
        'contact_hours'        => "Monday - Friday: 9:00 AM - 6:00 PM\nSaturday: 9:00 AM - 4:00 PM\nSunday: Closed",
        'contact_map'          => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3499.6775!2d77.1456!3d28.7004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d03e5f0000001%3A0x0!2sLok%20Vihar%2C%20Pitampura%2C%20New%20Delhi%2C%20110034!5e0!3m2!1sen!2sin!4v1700000000000',
        'social_facebook'      => $c('SOCIAL_FACEBOOK', 'https://www.facebook.com/gritfitnutri'),
        'social_instagram'     => $c('SOCIAL_INSTAGRAM', 'https://www.instagram.com/gritfitnutri'),
        'social_twitter'       => $c('SOCIAL_TWITTER', 'https://x.com/gritfitnutri'),

        // Homepage section order
        'home_order'           => 'slider,hero,products,midbanner,cleanenergy,abouthome,usps,posters,other',

        // Display sizes (pixels) — admin-adjustable from the dashboard.
        'logo_size'            => '75',  // header logo height
        'icon_size'            => '56',  // Clean Energy / feature icon height
        'slider_size'          => '460', // homepage top carousel (position: slider)
        'banner_size'          => '380', // mid/bottom/other banner slider height
        'poster_size'          => '80',  // certificate / poster strip image height
        'usp_size'             => '38',  // USPS ("Why Choose Us") icon size
    ];
    if (function_exists('cms_theme_default_settings')) {
        $d = array_merge($d, cms_theme_default_settings());
    }
    return $d;
}

// Bump when settings change so cms_all_settings() reloads in the same request.
$GLOBALS['_cms_settings_gen'] = 0;

function cms_fetch_settings_from_db() {
    $cache = [];
    try {
        $rows = getDB()->query("SELECT skey, svalue FROM settings")->fetchAll();
        foreach ($rows as $r) {
            $cache[$r['skey']] = $r['svalue'];
        }
    } catch (Exception $e) {
        $cache = [];
    }
    return $cache;
}

function cms_bump_settings_cache() {
    $GLOBALS['_cms_settings_gen'] = ($GLOBALS['_cms_settings_gen'] ?? 0) + 1;
}

// Load all settings from DB once per request (refreshed after cms_bump_settings_cache()).
function cms_all_settings() {
    static $cache = null;
    static $loadedGen = -1;
    $gen = $GLOBALS['_cms_settings_gen'] ?? 0;
    if ($cache !== null && $loadedGen === $gen) {
        return $cache;
    }
    $cache = cms_fetch_settings_from_db();
    $loadedGen = $gen;
    return $cache;
}

// Get a single setting value (DB overrides default).
function setting($key, $default = null) {
    $all = cms_all_settings();
    if (array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '') {
        return $all[$key];
    }
    $defaults = cms_defaults();
    if ($default !== null) return $default;
    return $defaults[$key] ?? '';
}

// Pixel size from a setting key (empty/invalid DB values fall back to cms_defaults).
function cms_setting_px($key, $min = 1, $max = 2000) {
    $defaults = cms_defaults();
    $fallback = (int)($defaults[$key] ?? 0);
    $n = (int) setting($key);
    if ($n < $min) {
        $n = max($min, $fallback);
    }
    if ($n > $max) {
        $n = $max;
    }
    return $n;
}

// All admin-adjustable display sizes (setting key => [min, max]).
function cms_size_specs() {
    return [
        'logo_size'   => [20, 500],
        'icon_size'   => [20, 300],
        'slider_size' => [200, 900],
        'banner_size' => [120, 900],
        'poster_size' => [30, 400],
        'usp_size'    => [16, 200],
    ];
}

// Inline :root CSS variables consumed by css/style.css.
function cms_size_css_variables() {
    $map = [
        'logo_size'   => '--logo-h',
        'icon_size'   => '--icon-h',
        'slider_size' => '--slider-h',
        'banner_size' => '--banner-h',
        'poster_size' => '--poster-h',
        'usp_size'    => '--usp-h',
    ];
    $specs = cms_size_specs();
    $parts = [];
    foreach ($map as $key => $var) {
        if (!isset($specs[$key])) {
            continue;
        }
        [$min, $max] = $specs[$key];
        $parts[] = $var . ':' . cms_setting_px($key, $min, $max) . 'px';
    }
    return implode(';', $parts);
}

// Clamp a posted size value before saving (returns [value, was_clamped]).
function cms_clamp_size_setting($key, $value) {
    $specs = cms_size_specs();
    if (!isset($specs[$key])) {
        return [(string) $value, false];
    }
    [$min, $max] = $specs[$key];
    $n = (int) $value;
    $clamped = max($min, min($max, $n));
    return [(string) $clamped, $clamped !== $n];
}

// Boolean flag for enable/disable toggles.
function cms_flag($key) {
    $v = setting($key);
    return $v === '1' || $v === 1 || $v === true;
}

// Upsert a setting.
function set_setting($key, $value) {
    $stmt = getDB()->prepare(
        "INSERT INTO settings (skey, svalue) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)"
    );
    $stmt->execute([$key, $value]);
    cms_bump_settings_cache();
}

// Fetch ordered rows from a CMS table.
function cms_rows($table, $activeOnly = true, $where = '', $params = []) {
    if (!in_array($table, cms_allowed_tables(), true)) return [];
    $sql = "SELECT * FROM `$table`";
    $clauses = [];
    if ($activeOnly) $clauses[] = "is_active = 1";
    if ($where !== '') $clauses[] = $where;
    if ($clauses) $sql .= " WHERE " . implode(' AND ', $clauses);
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Render the active banners for a position as a slider/carousel.
// Reused for every banner position (slider, hero, mid, bottom, other) so any
// banner an admin assigns to a position shows up on the site. Pass
// $extraClass='' for the full-height top slider; 'banner-slider' for shorter strips.
function render_banner_slider($position, $extraClass = 'banner-slider') {
    $banners = cms_rows('banners', true, "position=?", [$position]);
    $banners = array_values(array_filter(
        $banners,
        fn($b) => $b['image'] !== '' || !empty($b['video']) || $b['title'] !== ''
    ));
    if (empty($banners)) return;
    $multi = count($banners) > 1;
    $cls = trim('hero-slider ' . $extraClass);
    echo '<section class="' . e($cls) . '"><div class="slides">';
    foreach ($banners as $idx => $b) {
        echo '<div class="slide ' . ($idx === 0 ? 'active' : '') . '">';
        if (!empty($b['video'])) {
            echo '<video autoplay muted loop playsinline><source src="' . e($b['video']) . '" type="video/mp4"></video>';
        } elseif ($b['image']) {
            echo '<img src="' . e(asset_url($b['image'])) . '" alt="">';
        }
        if ($b['title'] || $b['subtitle'] || $b['button_text'] || !empty($b['button2_text'])) {
            echo '<div class="slide-overlay"></div><div class="slide-content">';
            if ($b['title']) echo '<h2>' . e($b['title']) . '</h2>';
            if ($b['subtitle']) echo '<p>' . e($b['subtitle']) . '</p>';
            if ($b['button_text'] || !empty($b['button2_text'])) {
                echo '<div class="slide-buttons">';
                if ($b['button_text']) echo '<a href="' . e(link_url($b['button_link'])) . '" class="btn btn-primary">' . e($b['button_text']) . '</a>';
                if (!empty($b['button2_text'])) echo '<a href="' . e(link_url($b['button2_link'])) . '" class="btn btn-outline">' . e($b['button2_text']) . '</a>';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
    if ($multi) {
        echo '<button class="slider-arrow prev" aria-label="Previous slide">&#10094;</button>';
        echo '<button class="slider-arrow next" aria-label="Next slide">&#10095;</button>';
        echo '<div class="slider-dots">';
        foreach ($banners as $idx => $b) {
            echo '<button class="' . ($idx === 0 ? 'active' : '') . '" aria-label="Go to slide ' . ($idx + 1) . '"></button>';
        }
        echo '</div>';
    }
    echo '</section>';
}

// Resolve a media path to a URL. Full URLs pass through; stored upload paths
// become root-relative so they resolve on whatever host serves the page.
function asset_url($path) {
    $path = trim((string)$path);
    if ($path === '') return '';
    if (preg_match('~^(https?:)?//~i', $path) || strpos($path, 'data:') === 0) {
        return $path;
    }
    return '/' . ltrim($path, '/');
}

// Ordered image gallery for a product (primary first). Falls back to the
// legacy image1-4 columns when no product_images rows exist.
function product_gallery($product) {
    $pid = is_array($product) ? (int)$product['id'] : (int)$product;
    $stmt = getDB()->prepare(
        "SELECT image FROM product_images WHERE product_id=? ORDER BY is_primary DESC, sort_order, id"
    );
    $stmt->execute([$pid]);
    $imgs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if ($imgs) return $imgs;
    if (is_array($product)) {
        $legacy = [];
        foreach (['image1', 'image2', 'image3', 'image4'] as $c) {
            if (!empty($product[$c])) $legacy[] = $product[$c];
        }
        return $legacy;
    }
    return [];
}

// The primary image path for a product row.
function product_primary_image($product) {
    $g = product_gallery($product);
    return $g[0] ?? '';
}

// Resolve a link/href. Full URLs and tel:/mailto:/# anchors pass through;
// everything else is treated as a root-relative site path.
function link_url($url) {
    $url = trim((string)$url);
    if ($url === '') return '#';
    if (preg_match('~^(https?:)?//~i', $url) || preg_match('~^(mailto:|tel:|#)~i', $url)) {
        return $url;
    }
    return '/' . ltrim($url, '/');
}

require_once __DIR__ . '/cms_theme.php';
