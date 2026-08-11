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

        // Hero / main banner
        'hero_enabled'         => '1',
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
        'contact_email'        => $c('SITE_EMAIL', 'contact@gritfitnutri.in'),
        'contact_phone'        => $c('SITE_PHONE', '+91 9811242068'),
        'contact_whatsapp'     => $c('WHATSAPP_NUMBER', '919811242068'),
        'contact_address'      => $c('SITE_ADDRESS', 'B-161, 3rd Floor, Lok Vihar, Pitampura, New Delhi, 110034'),
        'contact_hours'        => "Monday - Friday: 9:00 AM - 6:00 PM\nSaturday: 9:00 AM - 4:00 PM\nSunday: Closed",
        'contact_map'          => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3499.6775!2d77.1456!3d28.7004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d03e5f0000001%3A0x0!2sLok%20Vihar%2C%20Pitampura%2C%20New%20Delhi%2C%20110034!5e0!3m2!1sen!2sin!4v1700000000000',
        'social_facebook'      => $c('SOCIAL_FACEBOOK', 'https://www.facebook.com/gritfitnutri'),
        'social_instagram'     => $c('SOCIAL_INSTAGRAM', 'https://www.instagram.com/gritfitnutri'),
        'social_twitter'       => $c('SOCIAL_TWITTER', 'https://x.com/gritfitnutri'),

        // Homepage section order
        'home_order'           => 'slider,hero,products,midbanner,cleanenergy,abouthome,usps,posters',
    ];
    return $d;
}

// Load all settings from DB once per request.
function cms_all_settings() {
    static $cache = null;
    if ($cache !== null) return $cache;
    $cache = [];
    try {
        $rows = getDB()->query("SELECT skey, svalue FROM settings")->fetchAll();
        foreach ($rows as $r) $cache[$r['skey']] = $r['svalue'];
    } catch (Exception $e) {
        $cache = [];
    }
    return $cache;
}

// Get a single setting value (DB overrides default).
function setting($key, $default = null) {
    $all = cms_all_settings();
    if (array_key_exists($key, $all) && $all[$key] !== null) {
        return $all[$key];
    }
    $defaults = cms_defaults();
    if ($default !== null) return $default;
    return $defaults[$key] ?? '';
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
