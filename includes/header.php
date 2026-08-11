<?php
$menuItems = cms_rows('menu_items');
$curPath = '/' . trim($_GET['route'] ?? '', '/');
$socials = [
    'facebook' => setting('social_facebook'),
    'instagram' => setting('social_instagram'),
    'twitter' => setting('social_twitter'),
];
$socialSvg = [
    'facebook' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
    'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>',
    'twitter' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
];
$logo = setting('site_logo');
$siteName = setting('site_name');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?><?= e($siteName) ?></title>
    <meta name="description" content="<?= isset($pageDesc) ? e($pageDesc) : 'Fueling the World with India\'s Finest Energy Bars. Premium superfood energy bars.' ?>">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" href="<?= e(asset_url($logo)) ?>" type="image/png">
</head>
<body>
    <?php if (cms_flag('adbar_enabled') && setting('adbar_text') !== ''): ?>
    <div class="announcement-bar">
        <?php if (setting('adbar_link')): ?><a href="<?= e(link_url(setting('adbar_link'))) ?>"><?= e(setting('adbar_text')) ?></a>
        <?php else: ?><?= e(setting('adbar_text')) ?><?php endif; ?>
    </div>
    <?php endif; ?>

    <header class="header">
        <nav class="navbar">
            <a href="/" class="logo">
                <img src="<?= e(asset_url($logo)) ?>" alt="<?= e($siteName) ?> logo" onerror="this.src='https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=375,fit=crop/silemQfqUS99dRJ2/site-logo-new-d957G620KEuxvkO5.png'">
            </a>
            <ul class="nav-links">
                <?php foreach ($menuItems as $mi):
                    $url = $mi['url'];
                    $isProducts = rtrim($url, '/') === '/products';
                    $active = false;
                    if ($url === '/' || rtrim($url, '/') === '') $active = ($page === 'home');
                    elseif ($isProducts) $active = in_array($page, ['products', 'category', 'product-detail']);
                    elseif (rtrim($url, '/') === '/about-us') $active = ($page === 'about');
                    elseif (rtrim($url, '/') === '/contact') $active = ($page === 'contact');
                    else $active = (rtrim($curPath, '/') === rtrim($url, '/'));
                    $target = $mi['new_tab'] ? ' target="_blank" rel="noopener"' : '';
                ?>
                <li class="<?= $isProducts && !empty($navCategories) ? 'has-dropdown' : '' ?>">
                    <a href="<?= e(link_url($url)) ?>"<?= $target ?> <?= $active ? 'class="active"' : '' ?>><?= e($mi['label']) ?></a>
                    <?php if ($isProducts && !empty($navCategories)): ?>
                    <ul class="dropdown">
                        <?php foreach ($navCategories as $nc): ?>
                        <li><a href="/<?= e($nc['slug']) ?>"><?= e($nc['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
                <li class="nav-social">
                    <?php foreach ($socials as $key => $url): if (!$url) continue; ?>
                    <a href="<?= e($url) ?>" target="_blank" rel="noopener" title="<?= e(ucfirst($key)) ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><?= $socialSvg[$key] ?></svg>
                    </a>
                    <?php endforeach; ?>
                </li>
            </ul>
            <div class="menu-toggle"><span></span><span></span><span></span></div>
        </nav>
        <div class="nav-overlay"></div>
    </header>
