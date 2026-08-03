<?php
session_start();
require_once __DIR__ . '/config.php';

$route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$parts = explode('/', $route);

// Admin routes
if ($parts[0] === 'admin') {
    require __DIR__ . '/admin/index.php';
    exit;
}

$db = getDB();

switch ($route) {
    case '':
    case 'home':
        $page = 'home';
        break;
    case 'about-us':
        $page = 'about';
        break;
    case 'contact':
        $page = 'contact';
        break;
    case 'products':
        $page = 'products';
        break;
    default:
        // Check if it's a category slug
        $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
        $stmt->execute([$route]);
        $category = $stmt->fetch();
        if ($category) {
            $page = 'category';
            break;
        }
        
        // Check if it's a product slug
        $stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.is_active = 1");
        $stmt->execute([$route]);
        $product = $stmt->fetch();
        if ($product) {
            $page = 'product-detail';
            break;
        }
        
        $page = '404';
        break;
}

// Get categories for nav dropdown
$navCategories = $db->query("SELECT name, slug FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();

require __DIR__ . '/includes/header.php';
require __DIR__ . '/pages/' . $page . '.php';
require __DIR__ . '/includes/footer.php';
