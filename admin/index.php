<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/lib.php';
$db = getDB();

// Resolve the admin sub-page from the route (e.g. admin/products -> products).
$adminPage = isset($_GET['route']) ? trim($_GET['route'], '/') : 'admin';
$adminPage = preg_replace('~^admin/?~', '', $adminPage);
$adminPage = trim($adminPage, '/');
$adminPage = $adminPage !== '' ? $adminPage : 'dashboard';

if ($adminPage === 'logout') {
    session_destroy();
    header('Location: ' . adm(''));
    exit;
}

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$_POST['username'] ?? '']);
    $admin = $stmt->fetch();
    if ($admin && password_verify($_POST['password'] ?? '', $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_user'] = $admin['username'];
        header('Location: ' . adm(''));
        exit;
    }
    $loginError = 'Invalid username or password.';
}

$loggedIn = isset($_SESSION['admin_id']);

// Process POST actions (redirects on completion).
require __DIR__ . '/handlers.php';

$msg = admin_take_flash();

// Sidebar sections: [slug => [icon, label]]
$navSections = [
    'dashboard'    => ['📊', 'Dashboard'],
    'homepage'     => ['🏠', 'Homepage'],
    'header'       => ['🔝', 'Header / Menu / Ad Bar'],
    'categories'   => ['📁', 'Categories'],
    'products'     => ['📦', 'Products'],
    'banners'      => ['🖼️', 'Banners'],
    'icons'        => ['✨', 'Icons / Features'],
    'posters'      => ['🏆', 'Posters'],
    'usps'         => ['⭐', 'USPS'],
    'reviews'      => ['💬', 'Reviews'],
    'bottombanner' => ['📢', 'Bottom Banner'],
    'footer'       => ['📄', 'Footer / Contact'],
    'enquiries'    => ['📩', 'Enquiries'],
    'settings'     => ['⚙️', 'Account'],
];

// Views that exist; fall back to dashboard.
$viewFile = __DIR__ . '/views/' . preg_replace('~[^a-z]~', '', $adminPage) . '.php';
if (!is_file($viewFile)) { $adminPage = 'dashboard'; $viewFile = __DIR__ . '/views/dashboard.php'; }

$newEnquiries = $loggedIn ? (int)$db->query("SELECT COUNT(*) FROM enquiries WHERE is_read=0")->fetchColumn() : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= e(setting('site_name')) ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;background:#f0f2f5;color:#333;}
        a{text-decoration:none;color:inherit;}
        .login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
        .login-box{background:#fff;border-radius:12px;padding:40px;max-width:400px;width:100%;box-shadow:0 4px 20px rgba(0,0,0,0.08);}
        .login-box h2{color:rgb(61,99,204);margin-bottom:25px;text-align:center;}
        .login-err{background:#f8d7da;color:#842029;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:14px;}
        .admin-wrap{display:flex;min-height:100vh;}
        .sidebar{width:250px;background:#0d141a;color:#fff;padding:20px 0;position:fixed;height:100vh;overflow-y:auto;}
        .sidebar h3{padding:0 20px;margin-bottom:24px;font-size:16px;color:rgb(61,99,204);}
        .sidebar a{display:flex;align-items:center;gap:10px;padding:11px 20px;color:rgba(255,255,255,0.7);font-size:14px;transition:0.2s;}
        .sidebar a:hover,.sidebar a.active{background:rgba(61,99,204,0.2);color:#fff;}
        .sidebar .pill{margin-left:auto;background:#dc3545;color:#fff;border-radius:10px;font-size:11px;padding:1px 7px;}
        .sidebar .divider{height:1px;background:rgba(255,255,255,0.08);margin:10px 20px;}
        .main-content{margin-left:250px;flex:1;padding:30px;max-width:1100px;}
        .card{background:#fff;border-radius:10px;padding:25px;margin-bottom:20px;box-shadow:0 2px 10px rgba(0,0,0,0.04);}
        .card h3{margin-bottom:18px;font-size:17px;color:#0d141a;}
        .card .hint{font-size:13px;color:#888;margin-bottom:16px;margin-top:-10px;}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:20px;margin-bottom:20px;}
        .stat-card{background:#fff;border-radius:10px;padding:22px;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,0.04);}
        .stat-card h2{font-size:30px;color:rgb(61,99,204);}
        .stat-card p{font-size:13px;color:#999;margin-top:4px;}
        h2.page-title{margin-bottom:20px;color:#0d141a;}
        .form-group{margin-bottom:16px;}
        .form-group label{display:block;font-size:13px;font-weight:500;margin-bottom:5px;color:#555;}
        .form-group label.chk{display:flex;align-items:center;gap:8px;cursor:pointer;}
        .form-group label.chk input{width:auto;}
        .form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-family:inherit;font-size:14px;outline:none;transition:0.2s;}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:rgb(61,99,204);box-shadow:0 0 0 3px rgba(61,99,204,0.1);}
        .form-group textarea{min-height:90px;resize:vertical;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
        .mt8{margin-top:8px;}
        .img-preview{margin-bottom:8px;}
        .img-preview img{max-height:90px;max-width:180px;border:1px solid #eee;border-radius:8px;object-fit:contain;background:#fafafa;padding:4px;}
        .btn{display:inline-block;padding:10px 22px;border:none;border-radius:8px;font-family:inherit;font-size:14px;font-weight:500;cursor:pointer;transition:0.2s;}
        .btn-blue{background:rgb(61,99,204);color:#fff;}
        .btn-blue:hover{background:rgb(14,132,235);}
        .btn-red{background:#dc3545;color:#fff;}
        .btn-red:hover{background:#c82333;}
        .btn-green{background:#198754;color:#fff;}
        .btn-gray{background:#adb5bd;color:#fff;}
        .btn-move{background:#e9ecef;color:#333;}
        .btn-edit{background:#ffc107;color:#000;}
        .btn-sm{font-size:12px;padding:6px 12px;}
        .inline{display:inline-block;}
        .row-actions{display:flex;gap:6px;align-items:center;flex-wrap:wrap;}
        table{width:100%;border-collapse:collapse;}
        th,td{text-align:left;padding:10px 12px;border-bottom:1px solid #eee;font-size:13px;vertical-align:middle;}
        th{font-weight:600;color:#555;background:#f8f9fa;font-size:12px;text-transform:uppercase;letter-spacing:0.5px;}
        td img.thumb{width:48px;height:48px;object-fit:cover;border-radius:6px;background:#f4f4f4;}
        .badge-sm{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:500;}
        .badge-green{background:#d4edda;color:#155724;}
        .badge-gray{background:#e2e3e5;color:#383d41;}
        .alert{padding:12px 18px;border-radius:8px;margin-bottom:20px;font-size:14px;background:#d4edda;color:#155724;}
        .tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px;}
        .tabs a{padding:8px 16px;border-radius:20px;background:#fff;font-size:13px;color:#555;box-shadow:0 1px 4px rgba(0,0,0,0.05);}
        .tabs a.active{background:rgb(61,99,204);color:#fff;}
        .back-link{display:inline-block;margin-bottom:16px;font-size:13px;color:rgb(61,99,204);}
        @media(max-width:900px){
            .sidebar{width:100%;height:auto;position:static;}
            .main-content{margin-left:0;}
            .admin-wrap{flex-direction:column;}
            .form-row,.form-row-3{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>
<?php if (!$loggedIn): ?>
<div class="login-wrap">
    <div class="login-box">
        <h2>🔐 Admin Login</h2>
        <?php if (!empty($loginError)): ?><div class="login-err"><?= e($loginError) ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="admin_login" value="1">
            <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <button type="submit" class="btn btn-blue" style="width:100%;">Login</button>
        </form>
    </div>
</div>
<?php else: ?>
<div class="admin-wrap">
    <div class="sidebar">
        <h3>⚡ <?= e(setting('site_name')) ?></h3>
        <?php foreach ($navSections as $slug => $info): ?>
            <a href="<?= e(adm($slug === 'dashboard' ? '' : $slug)) ?>" class="<?= $adminPage === $slug ? 'active' : '' ?>">
                <span><?= $info[0] ?></span> <?= e($info[1]) ?>
                <?php if ($slug === 'enquiries' && $newEnquiries > 0): ?><span class="pill"><?= $newEnquiries ?></span><?php endif; ?>
            </a>
        <?php endforeach; ?>
        <div class="divider"></div>
        <a href="/" target="_blank"><span>🌐</span> View Site</a>
        <a href="<?= e(adm('logout')) ?>"><span>🚪</span> Logout</a>
    </div>
    <div class="main-content">
        <?php if ($msg): ?><div class="alert"><?= e($msg) ?></div><?php endif; ?>
        <?php require $viewFile; ?>
    </div>
</div>
<script>
function previewImg(input){
    if(input.files && input.files[0]){
        var box = input.closest('.form-group').querySelector('.img-preview');
        if(!box){ box=document.createElement('div'); box.className='img-preview'; input.parentNode.insertBefore(box, input); box.innerHTML='<img>'; }
        box.querySelector('img').src = URL.createObjectURL(input.files[0]);
    }
}
</script>
<?php endif; ?>
</body>
</html>
