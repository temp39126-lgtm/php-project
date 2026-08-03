<?php
session_start();
require_once __DIR__ . '/../config.php';
$db = getDB();

// Auth
$adminPage = isset($_GET['route']) ? str_replace('admin/', '', trim($_GET['route'], '/')) : 'admin';
$adminPage = str_replace('admin', '', $adminPage);
$adminPage = trim($adminPage, '/') ?: 'dashboard';

// Login/Logout
if ($adminPage === 'logout') {
    session_destroy();
    header('Location: ' . SITE_URL . '/admin/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$u]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($p, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_user'] = $admin['username'];
    }
}

$loggedIn = isset($_SESSION['admin_id']);

// Handle POST actions
$msg = '';
if ($loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Add Category
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['cat_name']);
        $slug = slugify($name);
        $desc = trim($_POST['cat_desc'] ?? '');
        $order = intval($_POST['cat_order'] ?? 0);
        $img = '';
        if (!empty($_FILES['cat_image']['name'])) {
            $img = uploadImage($_FILES['cat_image'], 'categories');
        }
        $stmt = $db->prepare("INSERT INTO categories (name, slug, image, description, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $img, $desc, $order]);
        $msg = 'Category added! Page auto-created at /' . $slug;
    }
    
    // Edit Category
    if (isset($_POST['edit_category'])) {
        $id = intval($_POST['cat_id']);
        $name = trim($_POST['cat_name']);
        $slug = slugify($name);
        $desc = trim($_POST['cat_desc'] ?? '');
        $order = intval($_POST['cat_order'] ?? 0);
        $active = isset($_POST['cat_active']) ? 1 : 0;
        
        $sql = "UPDATE categories SET name=?, slug=?, description=?, sort_order=?, is_active=?";
        $params = [$name, $slug, $desc, $order, $active];
        
        if (!empty($_FILES['cat_image']['name'])) {
            $img = uploadImage($_FILES['cat_image'], 'categories');
            if ($img) { $sql .= ", image=?"; $params[] = $img; }
        }
        $sql .= " WHERE id=?";
        $params[] = $id;
        $db->prepare($sql)->execute($params);
        $msg = 'Category updated!';
    }
    
    // Delete Category
    if (isset($_POST['delete_category'])) {
        $id = intval($_POST['cat_id']);
        $db->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
        $msg = 'Category deleted!';
    }
    
    // Add Product
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['prod_name']);
        $slug = slugify($name);
        // Ensure unique slug
        $check = $db->prepare("SELECT COUNT(*) FROM products WHERE slug=?");
        $check->execute([$slug]);
        if ($check->fetchColumn() > 0) $slug .= '-' . time();
        
        $catId = intval($_POST['prod_category']);
        $desc = trim($_POST['prod_desc'] ?? '');
        $short = trim($_POST['prod_short'] ?? '');
        $price = !empty($_POST['prod_price']) ? floatval($_POST['prod_price']) : null;
        $badge = $_POST['prod_badge'] ?? null;
        $order = intval($_POST['prod_order'] ?? 0);
        
        $imgs = ['','','',''];
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($_FILES["prod_image$i"]['name'])) {
                $uploaded = uploadImage($_FILES["prod_image$i"], 'products');
                if ($uploaded) $imgs[$i-1] = $uploaded;
            }
        }
        
        $stmt = $db->prepare("INSERT INTO products (name, slug, category_id, description, short_desc, price, badge, sort_order, image1, image2, image3, image4) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$name, $slug, $catId, $desc, $short, $price, $badge ?: null, $order, $imgs[0], $imgs[1], $imgs[2], $imgs[3]]);
        $msg = 'Product added! View at /' . $slug;
    }
    
    // Edit Product
    if (isset($_POST['edit_product'])) {
        $id = intval($_POST['prod_id']);
        $name = trim($_POST['prod_name']);
        $slug = slugify($name);
        $catId = intval($_POST['prod_category']);
        $desc = trim($_POST['prod_desc'] ?? '');
        $short = trim($_POST['prod_short'] ?? '');
        $price = !empty($_POST['prod_price']) ? floatval($_POST['prod_price']) : null;
        $badge = $_POST['prod_badge'] ?? null;
        $order = intval($_POST['prod_order'] ?? 0);
        $active = isset($_POST['prod_active']) ? 1 : 0;
        
        $sql = "UPDATE products SET name=?, slug=?, category_id=?, description=?, short_desc=?, price=?, badge=?, sort_order=?, is_active=?";
        $params = [$name, $slug, $catId, $desc, $short, $price, $badge ?: null, $order, $active];
        
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($_FILES["prod_image$i"]['name'])) {
                $uploaded = uploadImage($_FILES["prod_image$i"], 'products');
                if ($uploaded) { $sql .= ", image$i=?"; $params[] = $uploaded; }
            }
        }
        $sql .= " WHERE id=?";
        $params[] = $id;
        $db->prepare($sql)->execute($params);
        $msg = 'Product updated!';
    }
    
    // Delete Product
    if (isset($_POST['delete_product'])) {
        $id = intval($_POST['prod_id']);
        $db->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
        $msg = 'Product deleted!';
    }
    
    // Change Password
    if (isset($_POST['change_password'])) {
        $newPass = $_POST['new_password'] ?? '';
        if (strlen($newPass) >= 6) {
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $db->prepare("UPDATE admin_users SET password=? WHERE id=?")->execute([$hash, $_SESSION['admin_id']]);
            $msg = 'Password changed!';
        } else {
            $msg = 'Password must be at least 6 characters';
        }
    }
}

// Fetch data
if ($loggedIn) {
    $categories = $db->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();
    $products = $db->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.sort_order, p.id DESC")->fetchAll();
    $enquiries = $db->query("SELECT * FROM enquiries ORDER BY id DESC LIMIT 50")->fetchAll();
    $stats = [
        'categories' => $db->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
        'products' => $db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
        'enquiries' => $db->query("SELECT COUNT(*) FROM enquiries WHERE is_read=0")->fetchColumn(),
    ];
    
    // Edit mode
    $editCat = null; $editProd = null;
    if (isset($_GET['edit_cat'])) {
        $stmt = $db->prepare("SELECT * FROM categories WHERE id=?");
        $stmt->execute([intval($_GET['edit_cat'])]);
        $editCat = $stmt->fetch();
    }
    if (isset($_GET['edit_prod'])) {
        $stmt = $db->prepare("SELECT * FROM products WHERE id=?");
        $stmt->execute([intval($_GET['edit_prod'])]);
        $editProd = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= SITE_NAME ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;background:#f0f2f5;color:#333;}
        
        /* Login */
        .login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
        .login-box{background:#fff;border-radius:12px;padding:40px;max-width:400px;width:100%;box-shadow:0 4px 20px rgba(0,0,0,0.08);}
        .login-box h2{color:rgb(61,99,204);margin-bottom:25px;text-align:center;}
        
        /* Layout */
        .admin-wrap{display:flex;min-height:100vh;}
        .sidebar{width:240px;background:#0d141a;color:#fff;padding:20px 0;position:fixed;height:100vh;overflow-y:auto;}
        .sidebar h3{padding:0 20px;margin-bottom:30px;font-size:16px;color:rgb(61,99,204);}
        .sidebar a{display:block;padding:12px 20px;color:rgba(255,255,255,0.7);font-size:14px;transition:0.2s;}
        .sidebar a:hover,.sidebar a.active{background:rgba(61,99,204,0.2);color:#fff;}
        .sidebar a span{margin-right:8px;}
        .main-content{margin-left:240px;flex:1;padding:30px;}
        
        /* Components */
        .card{background:#fff;border-radius:10px;padding:25px;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,0.04);}
        .card h3{margin-bottom:20px;font-size:18px;color:#0d141a;}
        .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:30px;}
        .stat-card{background:#fff;border-radius:10px;padding:25px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,0.04);}
        .stat-card h2{font-size:32px;color:rgb(61,99,204);}
        .stat-card p{font-size:13px;color:#999;margin-top:4px;}
        
        /* Forms */
        .form-group{margin-bottom:16px;}
        .form-group label{display:block;font-size:13px;font-weight:500;margin-bottom:5px;color:#555;}
        .form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-family:inherit;font-size:14px;outline:none;transition:0.2s;}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:rgb(61,99,204);box-shadow:0 0 0 3px rgba(61,99,204,0.1);}
        .form-group textarea{height:100px;resize:vertical;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        
        /* Buttons */
        .btn{display:inline-block;padding:10px 22px;border:none;border-radius:8px;font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;transition:0.2s;}
        .btn-blue{background:rgb(61,99,204);color:#fff;}
        .btn-blue:hover{background:rgb(14,132,235);}
        .btn-red{background:#dc3545;color:#fff;font-size:12px;padding:6px 14px;}
        .btn-red:hover{background:#c82333;}
        .btn-sm{font-size:12px;padding:6px 14px;}
        .btn-edit{background:#ffc107;color:#000;font-size:12px;padding:6px 14px;}
        
        /* Tables */
        table{width:100%;border-collapse:collapse;}
        th,td{text-align:left;padding:10px 14px;border-bottom:1px solid #eee;font-size:13px;}
        th{font-weight:600;color:#555;background:#f8f9fa;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;}
        td img{width:50px;height:50px;object-fit:cover;border-radius:6px;}
        .badge-sm{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:500;}
        .badge-green{background:#d4edda;color:#155724;}
        .badge-gray{background:#e2e3e5;color:#383d41;}
        
        /* Alert */
        .alert{padding:12px 18px;border-radius:8px;margin-bottom:20px;font-size:14px;background:#d4edda;color:#155724;}
        
        /* Responsive */
        @media(max-width:768px){
            .sidebar{width:100%;height:auto;position:static;}
            .main-content{margin-left:0;}
            .admin-wrap{flex-direction:column;}
            .stats-grid{grid-template-columns:1fr;}
            .form-row{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>

<?php if (!$loggedIn): ?>
<!-- LOGIN -->
<div class="login-wrap">
    <div class="login-box">
        <h2>🔐 Admin Login</h2>
        <form method="POST">
            <input type="hidden" name="admin_login" value="1">
            <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <button type="submit" class="btn btn-blue" style="width:100%;">Login</button>
        </form>
    </div>
</div>

<?php else: ?>
<!-- ADMIN PANEL -->
<div class="admin-wrap">
    <div class="sidebar">
        <h3>⚡ <?= SITE_NAME ?></h3>
        <a href="<?= SITE_URL ?>/admin/" class="<?= $adminPage==='dashboard'?'active':'' ?>"><span>📊</span> Dashboard</a>
        <a href="<?= SITE_URL ?>/admin/categories" class="<?= $adminPage==='categories'?'active':'' ?>"><span>📁</span> Categories</a>
        <a href="<?= SITE_URL ?>/admin/products" class="<?= $adminPage==='products'?'active':'' ?>"><span>📦</span> Products</a>
        <a href="<?= SITE_URL ?>/admin/enquiries" class="<?= $adminPage==='enquiries'?'active':'' ?>"><span>📩</span> Enquiries</a>
        <a href="<?= SITE_URL ?>/admin/settings" class="<?= $adminPage==='settings'?'active':'' ?>"><span>⚙️</span> Settings</a>
        <a href="<?= SITE_URL ?>/" target="_blank"><span>🌐</span> View Site</a>
        <a href="<?= SITE_URL ?>/admin/logout"><span>🚪</span> Logout</a>
    </div>
    
    <div class="main-content">
        <?php if ($msg): ?><div class="alert"><?= e($msg) ?></div><?php endif; ?>
        
        <?php if ($adminPage === 'dashboard'): ?>
        <!-- DASHBOARD -->
        <h2 style="margin-bottom:20px;">Dashboard</h2>
        <div class="stats-grid">
            <div class="stat-card"><h2><?= $stats['categories'] ?></h2><p>Categories</p></div>
            <div class="stat-card"><h2><?= $stats['products'] ?></h2><p>Products</p></div>
            <div class="stat-card"><h2><?= $stats['enquiries'] ?></h2><p>New Enquiries</p></div>
        </div>
        
        <?php elseif ($adminPage === 'categories'): ?>
        <!-- CATEGORIES -->
        <h2 style="margin-bottom:20px;">Categories</h2>
        
        <div class="card">
            <h3><?= $editCat ? 'Edit Category' : 'Add New Category' ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editCat): ?>
                <input type="hidden" name="edit_category" value="1">
                <input type="hidden" name="cat_id" value="<?= $editCat['id'] ?>">
                <?php else: ?>
                <input type="hidden" name="add_category" value="1">
                <?php endif; ?>
                <div class="form-row">
                    <div class="form-group"><label>Name *</label><input type="text" name="cat_name" value="<?= e($editCat['name'] ?? '') ?>" required></div>
                    <div class="form-group"><label>Sort Order</label><input type="number" name="cat_order" value="<?= $editCat['sort_order'] ?? 0 ?>"></div>
                </div>
                <div class="form-group"><label>Description</label><textarea name="cat_desc"><?= e($editCat['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Category Image</label><input type="file" name="cat_image" accept="image/*"></div>
                <?php if ($editCat): ?>
                <div class="form-group"><label><input type="checkbox" name="cat_active" <?= $editCat['is_active'] ? 'checked' : '' ?>> Active</label></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-blue"><?= $editCat ? 'Update' : 'Add Category' ?></button>
                <?php if ($editCat): ?><a href="<?= SITE_URL ?>/admin/categories" class="btn btn-sm" style="margin-left:10px;">Cancel</a><?php endif; ?>
            </form>
        </div>
        
        <div class="card">
            <h3>All Categories</h3>
            <table>
                <tr><th>Image</th><th>Name</th><th>Slug (URL)</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td><?php if($c['image']): ?><img src="<?= SITE_URL ?>/<?= e($c['image']) ?>"><?php else: ?>—<?php endif; ?></td>
                    <td><strong><?= e($c['name']) ?></strong></td>
                    <td><a href="<?= SITE_URL ?>/<?= e($c['slug']) ?>" target="_blank">/<?= e($c['slug']) ?></a></td>
                    <td><?= $c['sort_order'] ?></td>
                    <td><span class="badge-sm <?= $c['is_active']?'badge-green':'badge-gray' ?>"><?= $c['is_active']?'Active':'Inactive' ?></span></td>
                    <td>
                        <a href="<?= SITE_URL ?>/admin/categories?edit_cat=<?= $c['id'] ?>" class="btn btn-edit btn-sm">Edit</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this category and ALL its products?')">
                            <input type="hidden" name="delete_category" value="1">
                            <input type="hidden" name="cat_id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn btn-red btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <?php elseif ($adminPage === 'products'): ?>
        <!-- PRODUCTS -->
        <h2 style="margin-bottom:20px;">Products</h2>
        
        <div class="card">
            <h3><?= $editProd ? 'Edit Product' : 'Add New Product' ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editProd): ?>
                <input type="hidden" name="edit_product" value="1">
                <input type="hidden" name="prod_id" value="<?= $editProd['id'] ?>">
                <?php else: ?>
                <input type="hidden" name="add_product" value="1">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group"><label>Product Name *</label><input type="text" name="prod_name" value="<?= e($editProd['name'] ?? '') ?>" required></div>
                    <div class="form-group"><label>Category *</label>
                        <select name="prod_category" required>
                            <option value="">-- Select --</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($editProd && $editProd['category_id']==$c['id'])?'selected':'' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Price (₹) - optional</label><input type="number" step="0.01" name="prod_price" value="<?= $editProd['price'] ?? '' ?>"></div>
                    <div class="form-group"><label>Badge</label>
                        <select name="prod_badge">
                            <option value="">None</option>
                            <option value="New Arrival" <?= ($editProd && $editProd['badge']==='New Arrival')?'selected':'' ?>>New Arrival</option>
                            <option value="New Flavor" <?= ($editProd && $editProd['badge']==='New Flavor')?'selected':'' ?>>New Flavor</option>
                            <option value="Bestseller" <?= ($editProd && $editProd['badge']==='Bestseller')?'selected':'' ?>>Bestseller</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Short Description</label><input type="text" name="prod_short" value="<?= e($editProd['short_desc'] ?? '') ?>" maxlength="500"></div>
                <div class="form-group"><label>Full Description</label><textarea name="prod_desc"><?= e($editProd['description'] ?? '') ?></textarea></div>
                <div class="form-row">
                    <div class="form-group"><label>Image 1 (Main) *</label><input type="file" name="prod_image1" accept="image/*" <?= $editProd?'':'required' ?>></div>
                    <div class="form-group"><label>Image 2</label><input type="file" name="prod_image2" accept="image/*"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Image 3</label><input type="file" name="prod_image3" accept="image/*"></div>
                    <div class="form-group"><label>Image 4</label><input type="file" name="prod_image4" accept="image/*"></div>
                </div>
                <div class="form-group"><label>Sort Order</label><input type="number" name="prod_order" value="<?= $editProd['sort_order'] ?? 0 ?>"></div>
                <?php if ($editProd): ?>
                <div class="form-group"><label><input type="checkbox" name="prod_active" <?= $editProd['is_active']?'checked':'' ?>> Active</label></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-blue"><?= $editProd ? 'Update' : 'Add Product' ?></button>
                <?php if ($editProd): ?><a href="<?= SITE_URL ?>/admin/products" class="btn btn-sm" style="margin-left:10px;">Cancel</a><?php endif; ?>
            </form>
        </div>
        
        <div class="card">
            <h3>All Products (<?= count($products) ?>)</h3>
            <table>
                <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Badge</th><th>Status</th><th>Actions</th></tr>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php if($p['image1']): ?><img src="<?= SITE_URL ?>/<?= e($p['image1']) ?>"><?php else: ?>—<?php endif; ?></td>
                    <td><strong><?= e($p['name']) ?></strong></td>
                    <td><?= e($p['cat_name']) ?></td>
                    <td><?= $p['price'] ? '₹'.number_format($p['price'],2) : '—' ?></td>
                    <td><?= $p['badge'] ? '<span class="badge-sm badge-green">'.e($p['badge']).'</span>' : '—' ?></td>
                    <td><span class="badge-sm <?= $p['is_active']?'badge-green':'badge-gray' ?>"><?= $p['is_active']?'Active':'Off' ?></span></td>
                    <td>
                        <a href="<?= SITE_URL ?>/<?= e($p['slug']) ?>" target="_blank" class="btn btn-sm" style="background:#17a2b8;color:#fff;">View</a>
                        <a href="<?= SITE_URL ?>/admin/products?edit_prod=<?= $p['id'] ?>" class="btn btn-edit btn-sm">Edit</a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?')">
                            <input type="hidden" name="delete_product" value="1">
                            <input type="hidden" name="prod_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-red btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <?php elseif ($adminPage === 'enquiries'): ?>
        <!-- ENQUIRIES -->
        <h2 style="margin-bottom:20px;">Enquiries</h2>
        <div class="card">
            <table>
                <tr><th>Date</th><th>Name</th><th>Email</th><th>Message</th></tr>
                <?php foreach ($enquiries as $eq): ?>
                <tr>
                    <td><?= date('d M Y H:i', strtotime($eq['created_at'])) ?></td>
                    <td><strong><?= e($eq['name']) ?></strong></td>
                    <td><a href="mailto:<?= e($eq['email']) ?>"><?= e($eq['email']) ?></a></td>
                    <td><?= e(substr($eq['message'],0,100)) ?><?= strlen($eq['message'])>100?'...':'' ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($enquiries)): ?>
                <tr><td colspan="4" style="text-align:center;color:#999;">No enquiries yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>
        
        <?php elseif ($adminPage === 'settings'): ?>
        <!-- SETTINGS -->
        <h2 style="margin-bottom:20px;">Settings</h2>
        <div class="card">
            <h3>Change Password</h3>
            <form method="POST">
                <input type="hidden" name="change_password" value="1">
                <div class="form-group"><label>New Password (min 6 chars)</label><input type="password" name="new_password" minlength="6" required></div>
                <button type="submit" class="btn btn-blue">Change Password</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

</body>
</html>
