<?php
$pageTitle = $product['name'];
$pageDesc = $product['short_desc'] ?: $product['name'] . ' - Premium energy bar from ' . setting('site_name');

$related = $db->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 ORDER BY RAND() LIMIT 4");
$related->execute([$product['category_id'], $product['id']]);
$related = $related->fetchAll();

$images = product_gallery($product);
$features = cms_rows('product_features', false, "product_id=?", [$product['id']]);
$variations = cms_rows('product_variations', true, "product_id=?", [$product['id']]);

$waNumber = $product['whatsapp_number'] ?: setting('contact_whatsapp');
$phone = $product['contact_number'] ?: setting('contact_phone');
$whatsappMsg = urlencode("Hi, I'm interested in " . $product['name'] . ". Please share more details.");
?>

<section class="section product-detail-section">
    <div class="container">
        <div class="breadcrumb">
            <a href="/products">Products</a> &rsaquo;
            <a href="/<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a> &rsaquo;
            <span><?= e($product['name']) ?></span>
        </div>

        <div class="product-detail-grid">
            <div class="product-images">
                <div class="product-main-image">
                    <?php if ($product['badge']): ?>
                    <span class="badge <?= $product['badge'] === 'New Arrival' ? 'badge-arrival' : 'badge-flavor' ?>"><?= e($product['badge']) ?></span>
                    <?php endif; ?>
                    <img id="mainImage" src="<?= e(asset_url($images[0] ?? '')) ?>" alt="<?= e($product['name']) ?>" onerror="this.src='https://via.placeholder.com/600x600?text=No+Image'">
                </div>
                <?php if (count($images) > 1): ?>
                <div class="product-thumbs">
                    <?php foreach ($images as $i => $img): ?>
                    <div class="thumb <?= $i === 0 ? 'active' : '' ?>" onclick="changeImage(this, '<?= e(asset_url($img)) ?>')">
                        <img src="<?= e(asset_url($img)) ?>" alt="<?= e($product['name']) ?> image <?= $i + 1 ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <h1><?= e($product['name']) ?></h1>

                <?php if ($product['price']): ?>
                <div class="product-price">₹<?= number_format($product['price'], 2) ?></div>
                <?php endif; ?>

                <?php if (!empty($product['grams']) || !empty($product['flavour'])): ?>
                <div class="product-attrs">
                    <?php if (!empty($product['grams'])): ?><span class="attr-chip"><strong>Weight:</strong> <?= e($product['grams']) ?></span><?php endif; ?>
                    <?php if (!empty($product['flavour'])): ?><span class="attr-chip"><strong>Flavour:</strong> <?= e($product['flavour']) ?></span><?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($product['short_desc']): ?>
                <p class="product-short-desc"><?= e($product['short_desc']) ?></p>
                <?php endif; ?>

                <?php if (!empty($variations)): ?>
                <div class="product-variations">
                    <h4>Pack Sizes</h4>
                    <div class="variation-list">
                        <?php foreach ($variations as $v): ?>
                        <div class="variation-row">
                            <span class="v-size"><?= e($v['pack_size']) ?></span>
                            <?php if ($v['price'] !== null): ?><span class="v-price">₹<?= number_format($v['price'], 2) ?></span><?php endif; ?>
                            <span class="v-status <?= stripos($v['status'], 'out') !== false ? 'out' : 'in' ?>"><?= e($v['status']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($features)): ?>
                <div class="product-features">
                    <h4>Key Features</h4>
                    <ul>
                        <?php foreach ($features as $f): ?>
                        <li><?= e($f['feature']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($product['description']): ?>
                <div class="product-description">
                    <?= $product['description'] /* HTML allowed: admin-managed content */ ?>
                </div>
                <?php endif; ?>

                <div class="product-cta">
                    <?php if ($waNumber): ?>
                    <a href="https://wa.me/<?= e($waNumber) ?>?text=<?= $whatsappMsg ?>" class="btn btn-whatsapp" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#fff" style="vertical-align:middle;margin-right:8px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884"/></svg>
                        Enquire on WhatsApp
                    </a>
                    <?php endif; ?>
                    <?php if ($phone): ?>
                    <a href="tel:<?= e(str_replace(' ', '', $phone)) ?>" class="btn btn-primary">📞 <?= e($phone) ?></a>
                    <?php endif; ?>
                </div>

                <div class="product-meta">
                    <p><strong>Category:</strong> <a href="/<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a></p>
                </div>
            </div>
        </div>

        <?php if (!empty($related)): ?>
        <div class="related-products">
            <h3>Related Products</h3>
            <div class="product-listing-grid">
                <?php foreach ($related as $rp): $rpImg = product_primary_image($rp); ?>
                <a href="/<?= e($rp['slug']) ?>" class="product-item">
                    <div class="product-item-img">
                        <?php if ($rp['badge']): ?>
                        <span class="badge <?= $rp['badge'] === 'New Arrival' ? 'badge-arrival' : 'badge-flavor' ?>"><?= e($rp['badge']) ?></span>
                        <?php endif; ?>
                        <?php if ($rpImg): ?><img src="<?= e(asset_url($rpImg)) ?>" alt="<?= e($rp['name']) ?>"><?php endif; ?>
                    </div>
                    <p class="product-item-name"><?= e($rp['name']) ?></p>
                    <?php if ($rp['price']): ?><p class="product-item-price">₹<?= number_format($rp['price'], 2) ?></p><?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function changeImage(thumb, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}
</script>
