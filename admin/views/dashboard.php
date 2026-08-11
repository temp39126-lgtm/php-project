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
