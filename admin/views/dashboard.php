<?php
$stats = [
    'categories' => (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'products'   => (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'reviews'    => (int)$db->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
    'enquiries'  => (int)$db->query("SELECT COUNT(*) FROM enquiries WHERE is_read=0")->fetchColumn(),
];
?>
<h2 class="page-title">Dashboard</h2>
<div class="stats-grid">
    <div class="stat-card"><h2><?= $stats['categories'] ?></h2><p>Categories</p></div>
    <div class="stat-card"><h2><?= $stats['products'] ?></h2><p>Products</p></div>
    <div class="stat-card"><h2><?= $stats['reviews'] ?></h2><p>Reviews</p></div>
    <div class="stat-card"><h2><?= $stats['enquiries'] ?></h2><p>New Enquiries</p></div>
</div>
<div class="card">
    <h3>Display sizes (storefront)</h3>
    <p class="hint">Values below are what the public site uses right now (from saved settings). Edit each section in the sidebar, then hard-refresh the homepage.</p>
    <table>
        <tr><th>What</th><th>Pixels</th><th>Edit in</th></tr>
        <tr><td>Logo height</td><td><?= cms_setting_px('logo_size', 20, 300) ?></td><td><a href="<?= e(adm('header')) ?>">Header</a></td></tr>
        <tr><td>Top slider</td><td><?= cms_setting_px('slider_size', 200, 900) ?></td><td><a href="<?= e(adm('banners')) ?>">Banners</a></td></tr>
        <tr><td>Mid / bottom banners</td><td><?= cms_setting_px('banner_size', 120, 900) ?></td><td><a href="<?= e(adm('banners')) ?>">Banners</a></td></tr>
        <tr><td>Clean Energy icons</td><td><?= cms_setting_px('icon_size', 20, 300) ?></td><td><a href="<?= e(adm('icons')) ?>">Icons</a></td></tr>
        <tr><td>Posters / certificates</td><td><?= cms_setting_px('poster_size', 30, 400) ?></td><td><a href="<?= e(adm('posters')) ?>">Posters</a></td></tr>
        <tr><td>USP icons</td><td><?= cms_setting_px('usp_size', 16, 200) ?></td><td><a href="<?= e(adm('usps')) ?>">USPS</a></td></tr>
    </table>
</div>
<div class="card">
    <h3>Content Management</h3>
    <p class="hint">Everything on the website is editable from this dashboard. Use the sections on the left to manage each part of the site. Changes appear on the live site immediately.</p>
    <div class="tabs">
        <a href="<?= e(adm('homepage')) ?>">Homepage</a>
        <a href="<?= e(adm('header')) ?>">Header / Menu</a>
        <a href="<?= e(adm('products')) ?>">Products</a>
        <a href="<?= e(adm('banners')) ?>">Banners</a>
        <a href="<?= e(adm('reviews')) ?>">Reviews</a>
        <a href="<?= e(adm('footer')) ?>">Footer / Contact</a>
    </div>
</div>
