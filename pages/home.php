<?php
$pageTitle = "Superfood Energy Bars";
$categories = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
?>

<!-- Hero -->
<section class="hero">
    <video autoplay muted loop playsinline>
        <source src="https://videos.pexels.com/video-files/8844271/8844271-uhd_4096_2160_24fps.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Superfood Bars</h1>
        <p>Fuel Your Day with Natural Energy and Health</p>
        <div class="hero-buttons">
            <a href="<?= SITE_URL ?>/about-us" class="btn btn-primary">About Us</a>
            <a href="<?= SITE_URL ?>/products" class="btn btn-outline">Products</a>
        </div>
    </div>
</section>

<!-- Products -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <span class="overline">Discover Greatness</span>
            <h2>Explore our products</h2>
            <p>Fuel Your Day, Naturally</p>
        </div>
        <div class="products-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="<?= SITE_URL ?>/<?= e($cat['slug']) ?>" class="product-card">
                <div class="product-img-wrap">
                    <?php if ($cat['image']): ?>
                    <img src="<?= SITE_URL ?>/<?= e($cat['image']) ?>" alt="<?= e($cat['name']) ?>">
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

<!-- Banner -->
<div class="banner-section">
    <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=1920,fit=crop/silemQfqUS99dRJ2/collective-combination-1024x402-Yleqa574RZUoMZPw.jpg" alt="Products" class="banner-full">
</div>

<!-- Clean Energy -->
<section class="clean-energy">
    <div class="container">
        <h1>Clean Energy Great Taste</h1>
        <div class="features-row">
            <div class="feature-item">
                <h4>Zero Preservatives</h4>
                <p>All our products are free from any form of preservatives or additives</p>
            </div>
            <div class="feature-item">
                <h4>Best Quality Ingredients</h4>
                <p>We have used the best quality ingredients sourcing them sustainably.</p>
            </div>
            <div class="feature-item">
                <h4>Exceptional Quality</h4>
                <p>Our QC processes are very well defined and executed.</p>
            </div>
        </div>
    </div>
</section>

<!-- About -->
<section class="section">
    <div class="container">
        <div class="about-home">
            <div class="about-home-text">
                <h3>About Grit Fit Nutri</h3>
                <p>At Grit Fit Nutri, we create premium superfood energy bars that combine natural ingredients with health benefits for a modern lifestyle.</p>
                <a href="<?= SITE_URL ?>/about-us" class="btn btn-primary">Learn More</a>
            </div>
            <div class="about-home-img">
                <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,h=583,fit=crop/silemQfqUS99dRJ2/strawberry-about-mini-banner2-A3QOVoO0VXfMgyEo.jpg" alt="About">
            </div>
        </div>
    </div>
</section>

<!-- Certificates -->
<section class="certificates-strip">
    <h5>Our Certificates</h5>
    <div class="cert-logos">
        <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=346,fit=crop/silemQfqUS99dRJ2/1500px_-removebg-preview-YyvZW0MyxRt7BLW8.jpg" alt="Certificate">
        <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=324,fit=crop/silemQfqUS99dRJ2/5-removebg-preview-AE0a8r6yZ6F2yqvm.jpg" alt="Certificate">
        <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=324,fit=crop/silemQfqUS99dRJ2/6-removebg-preview-mxBM6XD0vLTyG6gb.jpg" alt="Certificate">
        <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=312,fit=crop/silemQfqUS99dRJ2/4-removebg-preview-YNqPa24LZpFnE7xL.jpg" alt="Certificate">
        <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=324,fit=crop/silemQfqUS99dRJ2/2-removebg-preview-AwvDN92G1Ocyxpr4.jpg" alt="Certificate">
        <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,h=329,fit=crop/silemQfqUS99dRJ2/1-removebg-preview-m5KL5n34BpSwVLZZ.jpg" alt="Certificate">
    </div>
</section>
