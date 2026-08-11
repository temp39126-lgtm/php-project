<?php
$pageTitle = "Our Products";
$categories = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1 WHERE c.is_active = 1 GROUP BY c.id ORDER BY c.sort_order")->fetchAll();
?>

<section class="product-page-hero"><h1>Our Products</h1></section>

<section class="product-listing">
    <div class="container">
        <?php if (empty($categories)): ?>
        <p style="text-align:center;color:#999;padding:40px;">No categories yet. Add from admin panel.</p>
        <?php else: ?>
        <div class="product-listing-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="/<?= e($cat['slug']) ?>" class="product-item">
                <div class="product-item-img">
                    <?php if ($cat['image']): ?>
                    <img src="<?= e(asset_url($cat['image'])) ?>" alt="<?= e($cat['name']) ?>">
                    <?php else: ?>
                    <div class="no-img-placeholder"><?= e($cat['name'][0]) ?></div>
                    <?php endif; ?>
                </div>
                <p class="product-item-name"><?= e($cat['name']) ?></p>
                <span class="product-count"><?= $cat['product_count'] ?> products</span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
