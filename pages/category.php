<?php
$pageTitle = $category['name'];
$products = $db->prepare("SELECT * FROM products WHERE category_id = ? AND is_active = 1 ORDER BY sort_order, id DESC");
$products->execute([$category['id']]);
$products = $products->fetchAll();
?>

<section class="product-page-hero"><h1><?= e($category['name']) ?></h1></section>

<section class="product-listing">
    <div class="container">
        <?php if (empty($products)): ?>
        <p style="text-align:center;color:#999;padding:40px;">No products in this category yet.</p>
        <?php else: ?>
        <div class="product-listing-grid">
            <?php foreach ($products as $prod): ?>
            <a href="/<?= e($prod['slug']) ?>" class="product-item">
                <div class="product-item-img">
                    <?php if ($prod['badge']): ?>
                    <span class="badge <?= $prod['badge'] === 'New Arrival' ? 'badge-arrival' : 'badge-flavor' ?>"><?= e($prod['badge']) ?></span>
                    <?php endif; ?>
                    <?php $cImg = product_primary_image($prod); if ($cImg): ?>
                    <img src="<?= e(asset_url($cImg)) ?>" alt="<?= e($prod['name']) ?>">
                    <?php else: ?>
                    <div class="no-img-placeholder"><?= e($prod['name'][0]) ?></div>
                    <?php endif; ?>
                </div>
                <p class="product-item-name"><?= e($prod['name']) ?></p>
                <?php if ($prod['price']): ?>
                <p class="product-item-price">₹<?= number_format($prod['price'], 2) ?></p>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
