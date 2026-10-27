<?php
$pageTitle = setting('about_hero_title') ?: 'About Us';
$pageDesc = setting('about_intro');
?>

<section class="page-hero">
    <?php if (setting('about_hero_image')): ?>
    <img src="<?= e(asset_url(setting('about_hero_image'))) ?>" alt="<?= e($pageTitle) ?>">
    <?php endif; ?>
    <div class="page-hero-overlay"></div>
    <h1><?= e($pageTitle) ?></h1>
</section>

<?php if (trim(setting('about_intro')) !== ''): ?>
<section class="section">
    <div class="container">
        <div class="about-intro">
            <p><?= nl2br(e(setting('about_intro'))) ?></p>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (trim(setting('about_who_text')) !== '' || trim(setting('about_vision_text')) !== ''): ?>
<section class="section" style="padding-top:0">
    <div class="container">
        <div class="about-grid">
            <?php if (trim(setting('about_who_text')) !== ''): ?>
            <div>
                <?php if (setting('about_who_title')): ?><h3><?= e(setting('about_who_title')) ?></h3><?php endif; ?>
                <p><?= nl2br(e(setting('about_who_text'))) ?></p>
            </div>
            <?php endif; ?>
            <?php if (trim(setting('about_vision_text')) !== ''): ?>
            <div>
                <?php if (setting('about_vision_title')): ?><h3><?= e(setting('about_vision_title')) ?></h3><?php endif; ?>
                <p><?= nl2br(e(setting('about_vision_text'))) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (setting('about_banner_image')): ?>
<div class="banner-section">
    <img src="<?= e(asset_url(setting('about_banner_image'))) ?>" alt="<?= e(setting('about_banner_alt')) ?>" class="banner-full">
</div>
<?php endif; ?>

<?php
$aboutBlocks = [
    [
        'title' => setting('about_block1_title'),
        'text'  => setting('about_block1_text'),
        'subs'  => [],
    ],
    [
        'title' => setting('about_block2_title'),
        'text'  => setting('about_block2_text'),
        'subs'  => [
            ['title' => setting('about_block2_sub1_title'), 'text' => setting('about_block2_sub1_text')],
            ['title' => setting('about_block2_sub2_title'), 'text' => setting('about_block2_sub2_text')],
        ],
    ],
    [
        'title' => setting('about_block3_title'),
        'text'  => setting('about_block3_text'),
        'subs'  => [
            ['title' => setting('about_block3_sub1_title'), 'text' => setting('about_block3_sub1_text')],
            ['title' => setting('about_block3_sub2_title'), 'text' => setting('about_block3_sub2_text')],
        ],
    ],
];
foreach ($aboutBlocks as $block):
    $hasBody = trim($block['text']) !== '';
    foreach ($block['subs'] as $sub) {
        if (trim($sub['text']) !== '') {
            $hasBody = true;
            break;
        }
    }
    if (trim($block['title']) === '' && !$hasBody) {
        continue;
    }
?>
<div class="about-detail-section">
    <div class="container"><div class="about-detail-content">
        <?php if (trim($block['title']) !== ''): ?><h5><?= e($block['title']) ?></h5><?php endif; ?>
        <?php if (trim($block['text']) !== ''): ?><p><?= nl2br(e($block['text'])) ?></p><?php endif; ?>
        <?php foreach ($block['subs'] as $sub):
            if (trim($sub['text']) === '' && trim($sub['title']) === '') continue;
        ?>
            <?php if (trim($sub['title']) !== ''): ?><h6><?= e($sub['title']) ?></h6><?php endif; ?>
            <?php if (trim($sub['text']) !== ''): ?><p><?= nl2br(e($sub['text'])) ?></p><?php endif; ?>
        <?php endforeach; ?>
    </div></div>
</div>
<?php endforeach; ?>
