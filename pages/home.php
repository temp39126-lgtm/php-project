<?php
$pageTitle = "Superfood Energy Bars";

$order = array_filter(array_map('trim', explode(',', setting('home_order'))));
if (empty($order)) $order = ['hero', 'products', 'midbanner', 'cleanenergy', 'abouthome', 'usps', 'posters'];

foreach ($order as $section):
    switch ($section):

    // ---------------- HERO ----------------
    case 'hero':
        if (!cms_flag('hero_enabled')) break;
        $heroImg = setting('hero_image');
        ?>
        <section class="hero">
            <?php if (setting('hero_type') === 'image' && $heroImg): ?>
                <img src="<?= e(asset_url($heroImg)) ?>" alt="" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:0;">
            <?php elseif (setting('hero_video')): ?>
                <video autoplay muted loop playsinline><source src="<?= e(setting('hero_video')) ?>" type="video/mp4"></video>
            <?php endif; ?>
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1><?= e(setting('hero_title')) ?></h1>
                <?php if (setting('hero_subtitle')): ?><p><?= e(setting('hero_subtitle')) ?></p><?php endif; ?>
                <div class="hero-buttons">
                    <?php if (setting('hero_btn1_text')): ?><a href="<?= e(link_url(setting('hero_btn1_link'))) ?>" class="btn btn-primary"><?= e(setting('hero_btn1_text')) ?></a><?php endif; ?>
                    <?php if (setting('hero_btn2_text')): ?><a href="<?= e(link_url(setting('hero_btn2_link'))) ?>" class="btn btn-outline"><?= e(setting('hero_btn2_text')) ?></a><?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        break;

    // ---------------- CATEGORIES GRID ----------------
    case 'products':
        if (!cms_flag('homeproducts_enabled')) break;
        $categories = cms_rows('categories');
        ?>
        <section class="section">
            <div class="container">
                <div class="section-title">
                    <?php if (setting('homeproducts_overline')): ?><span class="overline"><?= e(setting('homeproducts_overline')) ?></span><?php endif; ?>
                    <h2><?= e(setting('homeproducts_title')) ?></h2>
                    <?php if (setting('homeproducts_subtitle')): ?><p><?= e(setting('homeproducts_subtitle')) ?></p><?php endif; ?>
                </div>
                <div class="products-grid">
                    <?php foreach ($categories as $cat): ?>
                    <a href="/<?= e($cat['slug']) ?>" class="product-card">
                        <div class="product-img-wrap">
                            <?php if ($cat['image']): ?>
                            <img src="<?= e(asset_url($cat['image'])) ?>" alt="<?= e($cat['name']) ?>">
                            <?php else: ?>
                            <div style="width:200px;height:200px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;border-radius:8px;color:#999;">No Image</div>
                            <?php endif; ?>
                        </div>
                        <span class="product-label label-default"><?= e($cat['name']) ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        break;

    // ---------------- MID BANNER(S) ----------------
    case 'midbanner':
        if (!cms_flag('midbanner_enabled')) break;
        $banners = cms_rows('banners', true, "position=?", ['mid']);
        foreach ($banners as $b):
            if (!$b['image'] && !$b['title']) continue; ?>
            <div class="banner-section">
                <?php if ($b['image']): ?><img src="<?= e(asset_url($b['image'])) ?>" alt="<?= e($b['title']) ?>" class="banner-full"><?php endif; ?>
                <?php if ($b['title'] || $b['button_text']): ?>
                <div class="banner-overlay">
                    <?php if ($b['title']): ?><h2><?= e($b['title']) ?></h2><?php endif; ?>
                    <?php if ($b['subtitle']): ?><p><?= e($b['subtitle']) ?></p><?php endif; ?>
                    <?php if ($b['button_text']): ?><a href="<?= e(link_url($b['button_link'])) ?>" class="btn btn-primary"><?= e($b['button_text']) ?></a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach;
        break;

    // ---------------- CLEAN ENERGY / ICONS ----------------
    case 'cleanenergy':
        if (!cms_flag('cleanenergy_enabled')) break;
        $icons = cms_rows('icons');
        if (empty($icons)) break; ?>
        <section class="clean-energy">
            <div class="container">
                <h1><?= e(setting('cleanenergy_title')) ?></h1>
                <div class="features-row">
                    <?php foreach ($icons as $ic): ?>
                    <div class="feature-item">
                        <?php if ($ic['image']): ?><div class="feature-icon"><img src="<?= e(asset_url($ic['image'])) ?>" alt="<?= e($ic['title']) ?>"></div><?php endif; ?>
                        <h4><?= e($ic['title']) ?></h4>
                        <?php if ($ic['subtitle']): ?><p><?= e($ic['subtitle']) ?></p><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        break;

    // ---------------- ABOUT ON HOME ----------------
    case 'abouthome':
        if (!cms_flag('abouthome_enabled')) break; ?>
        <section class="section">
            <div class="container">
                <div class="about-home">
                    <div class="about-home-text">
                        <h3><?= e(setting('abouthome_title')) ?></h3>
                        <p><?= e(setting('abouthome_text')) ?></p>
                        <?php if (setting('abouthome_btn_text')): ?><a href="<?= e(link_url(setting('abouthome_btn_link'))) ?>" class="btn btn-primary"><?= e(setting('abouthome_btn_text')) ?></a><?php endif; ?>
                    </div>
                    <?php if (setting('abouthome_image')): ?>
                    <div class="about-home-img"><img src="<?= e(asset_url(setting('abouthome_image'))) ?>" alt="About"></div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        break;

    // ---------------- USPS ----------------
    case 'usps':
        if (!cms_flag('usps_enabled')) break;
        $usps = cms_rows('usps');
        if (empty($usps)) break; ?>
        <section class="section usps-section">
            <div class="container">
                <div class="section-title">
                    <h2><?= e(setting('usps_title')) ?></h2>
                    <?php if (setting('usps_subtitle')): ?><p><?= e(setting('usps_subtitle')) ?></p><?php endif; ?>
                </div>
                <div class="usps-grid">
                    <?php foreach ($usps as $u): ?>
                    <div class="usp-item">
                        <div class="usp-icon">
                            <?php if ($u['icon']): ?><img src="<?= e(asset_url($u['icon'])) ?>" alt="<?= e($u['title']) ?>"><?php else: ?>✓<?php endif; ?>
                        </div>
                        <h4><?= e($u['title']) ?></h4>
                        <?php if ($u['description']): ?><p><?= e($u['description']) ?></p><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
        break;

    // ---------------- POSTERS / CERTIFICATES ----------------
    case 'posters':
        if (!cms_flag('posters_enabled')) break;
        $posters = cms_rows('posters');
        if (empty($posters)) break; ?>
        <section class="certificates-strip">
            <?php if (setting('posters_title')): ?><h5><?= e(setting('posters_title')) ?></h5><?php endif; ?>
            <div class="cert-logos">
                <?php foreach ($posters as $p):
                    $img = '<img src="' . e(asset_url($p['image'])) . '" alt="' . e($p['title']) . '">';
                    if ($p['link']) echo '<a href="' . e(link_url($p['link'])) . '" target="_blank" rel="noopener">' . $img . '</a>';
                    else echo $img;
                endforeach; ?>
            </div>
        </section>
        <?php
        break;

    endswitch;
endforeach;
