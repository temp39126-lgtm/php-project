<?php
// ============================================
// Admin CMS - POST action dispatcher
// Runs only when an admin is logged in.
// Uses the PRG pattern: every action ends in a redirect.
// ============================================

if (!($loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST')) return;

$action = $_POST['action'] ?? '';
$ret = $_POST['return'] ?? '';
$db = getDB();

switch ($action) {

    // ---------- Generic actions ----------
    case 'reorder':
        cms_reorder($_POST['table'] ?? '', $_POST['id'] ?? 0, $_POST['dir'] ?? 'up');
        admin_redirect($ret ?: 'dashboard');

    case 'toggle':
        cms_toggle($_POST['table'] ?? '', $_POST['id'] ?? 0);
        admin_redirect($ret ?: 'dashboard', 'Visibility updated.');

    case 'delete':
        cms_delete($_POST['table'] ?? '', $_POST['id'] ?? 0);
        admin_redirect($ret ?: 'dashboard', 'Item deleted.');

    // ---------- Settings groups (homepage, header, footer, etc.) ----------
    case 'save_settings':
        foreach (($_POST['s'] ?? []) as $k => $v) {
            set_setting((string)$k, is_array($v) ? implode(',', $v) : (string)$v);
        }
        // Image settings uploaded as file_<key>.
        foreach ($_FILES as $field => $info) {
            if (strpos($field, 'file_') === 0 && !empty($info['name'])) {
                $key = substr($field, 5);
                $path = uploadImage($info, 'cms');
                if ($path) set_setting($key, $path);
            }
        }
        admin_redirect($ret ?: 'homepage', 'Settings saved.');

    // ---------- Categories ----------
    case 'save_category': {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $name = trim($_POST['name'] ?? '');
        $fields = [
            'name' => $name,
            'slug' => slugify($name),
            'description' => trim($_POST['description'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => (int)($_POST['is_active'] ?? 0),
            'image' => cms_resolve_image('image_file', 'image_url', 'categories', $_POST['current_image'] ?? ''),
        ];
        if (!$id) $fields['is_active'] = 1;
        cms_upsert('categories', $fields, $id);
        admin_redirect('categories', $id ? 'Category updated!' : 'Category added! Page at /' . $fields['slug']);
    }

    // ---------- Menu items ----------
    case 'save_menu': {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fields = [
            'label' => trim($_POST['label'] ?? ''),
            'url' => trim($_POST['url'] ?? '/'),
            'new_tab' => (int)($_POST['new_tab'] ?? 0),
            'is_active' => (int)($_POST['is_active'] ?? 0),
        ];
        if (!$id) $fields['sort_order'] = cms_next_order('menu_items');
        cms_upsert('menu_items', $fields, $id);
        admin_redirect('header', $id ? 'Menu item updated!' : 'Menu item added!');
    }

    // ---------- Banners ----------
    case 'save_banner': {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fields = [
            'position' => trim($_POST['position'] ?? 'mid'),
            'title' => trim($_POST['title'] ?? ''),
            'subtitle' => trim($_POST['subtitle'] ?? ''),
            'button_text' => trim($_POST['button_text'] ?? ''),
            'button_link' => trim($_POST['button_link'] ?? ''),
            'button2_text' => trim($_POST['button2_text'] ?? ''),
            'button2_link' => trim($_POST['button2_link'] ?? ''),
            'video' => trim($_POST['video'] ?? ''),
            'is_active' => (int)($_POST['is_active'] ?? 0),
            'image' => cms_resolve_image('image_file', 'image_url', 'banners', $_POST['current_image'] ?? ''),
        ];
        if (!$id) $fields['sort_order'] = cms_next_order('banners');
        cms_upsert('banners', $fields, $id);
        admin_redirect('banners', $id ? 'Banner updated!' : 'Banner added!');
    }

    // ---------- Icons ----------
    case 'save_icon': {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fields = [
            'title' => trim($_POST['title'] ?? ''),
            'subtitle' => trim($_POST['subtitle'] ?? ''),
            'link' => trim($_POST['link'] ?? ''),
            'is_active' => (int)($_POST['is_active'] ?? 0),
            'image' => cms_resolve_image('image_file', 'image_url', 'icons', $_POST['current_image'] ?? ''),
        ];
        if (!$id) $fields['sort_order'] = cms_next_order('icons');
        cms_upsert('icons', $fields, $id);
        admin_redirect('icons', $id ? 'Icon updated!' : 'Icon added!');
    }

    // ---------- Posters ----------
    case 'save_poster': {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fields = [
            'title' => trim($_POST['title'] ?? ''),
            'link' => trim($_POST['link'] ?? ''),
            'is_active' => (int)($_POST['is_active'] ?? 0),
            'image' => cms_resolve_image('image_file', 'image_url', 'posters', $_POST['current_image'] ?? ''),
        ];
        if (!$id) $fields['sort_order'] = cms_next_order('posters');
        cms_upsert('posters', $fields, $id);
        admin_redirect('posters', $id ? 'Poster updated!' : 'Poster added!');
    }

    // ---------- USPS ----------
    case 'save_usp': {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fields = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'link' => null,
            'is_active' => (int)($_POST['is_active'] ?? 0),
            'icon' => cms_resolve_image('image_file', 'image_url', 'usps', $_POST['current_image'] ?? ''),
        ];
        unset($fields['link']);
        if (!$id) $fields['sort_order'] = cms_next_order('usps');
        cms_upsert('usps', $fields, $id);
        admin_redirect('usps', $id ? 'USP updated!' : 'USP added!');
    }

    // ---------- Reviews ----------
    case 'save_review': {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fields = [
            'name' => trim($_POST['name'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'rating' => max(1, min(5, (int)($_POST['rating'] ?? 5))),
            'review_text' => trim($_POST['review_text'] ?? ''),
            'answer' => trim($_POST['answer'] ?? ''),
            'is_active' => (int)($_POST['is_active'] ?? 0),
            'avatar' => cms_resolve_image('image_file', 'image_url', 'reviews', $_POST['current_image'] ?? ''),
        ];
        if (!$id) $fields['sort_order'] = cms_next_order('reviews');
        cms_upsert('reviews', $fields, $id);
        admin_redirect('reviews', $id ? 'Review updated!' : 'Review added!');
    }

    // ---------- Products ----------
    case 'save_product': {
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $name = trim($_POST['name'] ?? '');
        $slug = slugify($name);
        if (!$id) {
            $chk = $db->prepare("SELECT COUNT(*) FROM products WHERE slug=?");
            $chk->execute([$slug]);
            if ($chk->fetchColumn() > 0) $slug .= '-' . time();
        }
        $fields = [
            'name' => $name,
            'slug' => $slug,
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'short_desc' => trim($_POST['short_desc'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => $_POST['price'] !== '' ? (float)$_POST['price'] : null,
            'badge' => ($_POST['badge'] ?? '') !== '' ? trim($_POST['badge']) : null,
            'grams' => trim($_POST['grams'] ?? ''),
            'flavour' => trim($_POST['flavour'] ?? ''),
            'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
            'contact_number' => trim($_POST['contact_number'] ?? ''),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active' => (int)($_POST['is_active'] ?? 0),
        ];
        if (!$id) $fields['is_active'] = 1;
        $pid = cms_upsert('products', $fields, $id);
        admin_redirect('products?edit=' . $pid, $id ? 'Product updated!' : 'Product created! Now add images, features and pack sizes below.');
    }

    // ---------- Product features ----------
    case 'save_feature': {
        $pid = (int)($_POST['product_id'] ?? 0);
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fields = ['product_id' => $pid, 'feature' => trim($_POST['feature'] ?? '')];
        if (!$id) $fields['sort_order'] = cms_next_order('product_features', 'product_id', $pid);
        if ($fields['feature'] !== '') cms_upsert('product_features', $fields, $id);
        admin_redirect('products?edit=' . $pid, 'Feature saved.');
    }

    // ---------- Product variations ----------
    case 'save_variation': {
        $pid = (int)($_POST['product_id'] ?? 0);
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $fields = [
            'product_id' => $pid,
            'pack_size' => trim($_POST['pack_size'] ?? ''),
            'price' => $_POST['price'] !== '' ? (float)$_POST['price'] : null,
            'status' => trim($_POST['status'] ?? 'In Stock'),
            'is_active' => (int)($_POST['is_active'] ?? 1),
        ];
        if (!$id) $fields['sort_order'] = cms_next_order('product_variations', 'product_id', $pid);
        if ($fields['pack_size'] !== '') cms_upsert('product_variations', $fields, $id);
        admin_redirect('products?edit=' . $pid, 'Pack size saved.');
    }

    // ---------- Product images ----------
    case 'upload_image': {
        $pid = (int)($_POST['product_id'] ?? 0);
        $path = cms_resolve_image('image_file', 'image_url', 'products', '');
        if ($path !== '') {
            $isFirst = (int)$db->query("SELECT COUNT(*) FROM product_images WHERE product_id=" . $pid)->fetchColumn() === 0;
            cms_upsert('product_images', [
                'product_id' => $pid,
                'image' => $path,
                'sort_order' => cms_next_order('product_images', 'product_id', $pid),
                'is_primary' => $isFirst ? 1 : 0,
            ]);
        }
        admin_redirect('products?edit=' . $pid, 'Image added.');
    }

    case 'set_primary_image': {
        $pid = (int)($_POST['product_id'] ?? 0);
        $iid = (int)($_POST['id'] ?? 0);
        $db->prepare("UPDATE product_images SET is_primary=0 WHERE product_id=?")->execute([$pid]);
        $db->prepare("UPDATE product_images SET is_primary=1 WHERE id=? AND product_id=?")->execute([$iid, $pid]);
        admin_redirect('products?edit=' . $pid, 'Primary image updated.');
    }

    // ---------- Account ----------
    case 'change_password': {
        $newPass = $_POST['new_password'] ?? '';
        if (strlen($newPass) >= 6) {
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $db->prepare("UPDATE admin_users SET password=? WHERE id=?")->execute([$hash, $_SESSION['admin_id']]);
            admin_redirect('settings', 'Password changed!');
        }
        admin_redirect('settings', 'Password must be at least 6 characters.');
    }
}
