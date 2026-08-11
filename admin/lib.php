<?php
// ============================================
// Admin CMS - shared helpers & generic actions
// ============================================

// Admin-internal link (root-relative so it works locally and in production).
function adm($path = '') {
    return '/admin/' . ltrim($path, '/');
}

// Flash message via session (PRG pattern).
function admin_flash($msg) { $_SESSION['admin_flash'] = $msg; }
function admin_take_flash() {
    $m = $_SESSION['admin_flash'] ?? '';
    unset($_SESSION['admin_flash']);
    return $m;
}
function admin_redirect($section, $msg = '') {
    if ($msg !== '') admin_flash($msg);
    header('Location: ' . adm($section));
    exit;
}

// Resolve an image for a form submission: uploaded file wins, then a pasted
// URL, otherwise keep the current value.
function cms_resolve_image($fileKey, $urlKey, $subfolder, $current = '') {
    if (!empty($_FILES[$fileKey]['name'])) {
        $up = uploadImage($_FILES[$fileKey], $subfolder);
        if ($up) return $up;
    }
    if (isset($_POST[$urlKey])) {
        $u = trim($_POST[$urlKey]);
        if ($u !== '') return $u;
    }
    return $current;
}

// Generic insert/update for a CMS table. Returns the row id.
function cms_upsert($table, array $fields, $id = null) {
    if (!in_array($table, cms_allowed_tables(), true)) return null;
    $db = getDB();
    if ($id) {
        $set = implode(', ', array_map(fn($c) => "`$c`=?", array_keys($fields)));
        $params = array_values($fields);
        $params[] = $id;
        $db->prepare("UPDATE `$table` SET $set WHERE id=?")->execute($params);
        return $id;
    }
    $cols = implode(', ', array_map(fn($c) => "`$c`", array_keys($fields)));
    $ph = implode(', ', array_fill(0, count($fields), '?'));
    $db->prepare("INSERT INTO `$table` ($cols) VALUES ($ph)")->execute(array_values($fields));
    return $db->lastInsertId();
}

// Next sort_order value for a table (optionally scoped to a product).
function cms_next_order($table, $scopeCol = null, $scopeVal = null) {
    $db = getDB();
    $sql = "SELECT COALESCE(MAX(sort_order),0)+1 FROM `$table`";
    $params = [];
    if ($scopeCol) { $sql .= " WHERE `$scopeCol`=?"; $params[] = $scopeVal; }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

// Toggle is_active for a row.
function cms_toggle($table, $id) {
    if (!in_array($table, cms_allowed_tables(), true)) return;
    getDB()->prepare("UPDATE `$table` SET is_active = 1 - is_active WHERE id=?")->execute([(int)$id]);
}

// Reorder a row up/down. Normalizes sort_order within scope then swaps
// neighbours, so it is robust even when several rows share sort_order.
function cms_reorder($table, $id, $dir) {
    if (!in_array($table, cms_allowed_tables(), true)) return;
    $db = getDB();
    $scopeCol = in_array($table, ['product_features', 'product_variations', 'product_images'], true) ? 'product_id' : null;
    $row = $db->prepare("SELECT * FROM `$table` WHERE id=?");
    $row->execute([(int)$id]);
    $row = $row->fetch();
    if (!$row) return;

    $where = $scopeCol ? "WHERE `$scopeCol`=?" : '';
    $params = $scopeCol ? [$row[$scopeCol]] : [];
    $list = $db->prepare("SELECT id FROM `$table` $where ORDER BY sort_order ASC, id ASC");
    $list->execute($params);
    $ids = array_map('intval', $list->fetchAll(PDO::FETCH_COLUMN));

    $pos = array_search((int)$id, $ids, true);
    if ($pos === false) return;
    $target = $dir === 'up' ? $pos - 1 : $pos + 1;
    if ($target < 0 || $target >= count($ids)) return;
    [$ids[$pos], $ids[$target]] = [$ids[$target], $ids[$pos]];

    $upd = $db->prepare("UPDATE `$table` SET sort_order=? WHERE id=?");
    foreach ($ids as $i => $rid) $upd->execute([$i + 1, $rid]);
}

// Delete a row (with child cleanup for products/categories).
function cms_delete($table, $id) {
    if (!in_array($table, cms_allowed_tables(), true)) return;
    $db = getDB();
    $id = (int)$id;
    if ($table === 'products') {
        foreach (['product_features', 'product_variations', 'product_images'] as $t) {
            $db->prepare("DELETE FROM `$t` WHERE product_id=?")->execute([$id]);
        }
    }
    if ($table === 'categories') {
        $prods = $db->prepare("SELECT id FROM products WHERE category_id=?");
        $prods->execute([$id]);
        foreach ($prods->fetchAll(PDO::FETCH_COLUMN) as $pid) cms_delete('products', $pid);
    }
    $db->prepare("DELETE FROM `$table` WHERE id=?")->execute([$id]);
}

// ---------- Small view helpers ----------
function adm_text($label, $name, $value = '', $type = 'text', $attrs = '') {
    echo '<div class="form-group"><label>' . e($label) . '</label>';
    echo '<input type="' . $type . '" name="' . e($name) . '" value="' . e($value) . '" ' . $attrs . '></div>';
}
function adm_textarea($label, $name, $value = '', $attrs = '') {
    echo '<div class="form-group"><label>' . e($label) . '</label>';
    echo '<textarea name="' . e($name) . '" ' . $attrs . '>' . e($value) . '</textarea></div>';
}
function adm_image_field($label, $fileName, $urlName, $current = '') {
    echo '<div class="form-group"><label>' . e($label) . '</label>';
    if ($current !== '') {
        echo '<div class="img-preview"><img src="' . e(asset_url($current)) . '" alt="preview"></div>';
    }
    echo '<input type="file" name="' . e($fileName) . '" accept="image/*" onchange="previewImg(this)">';
    echo '<input type="text" name="' . e($urlName) . '" value="' . e($current) . '" placeholder="or paste an image URL / path" class="mt8">';
    echo '</div>';
}
function adm_active_checkbox($checked) {
    echo '<div class="form-group"><label class="chk"><input type="checkbox" name="is_active" ' . ($checked ? 'checked' : '') . '> Active (visible on site)</label></div>';
}
// Row action buttons: edit, move up/down, toggle active, delete.
function adm_actions($section, $table, $row, $editParam = 'edit') {
    $id = (int)$row['id'];
    echo '<div class="row-actions">';
    echo '<a href="' . e(adm($section . '?' . $editParam . '=' . $id)) . '" class="btn btn-edit btn-sm">Edit</a>';
    foreach (['up' => '↑', 'down' => '↓'] as $dir => $sym) {
        echo '<form method="POST" class="inline"><input type="hidden" name="action" value="reorder">'
            . '<input type="hidden" name="table" value="' . e($table) . '"><input type="hidden" name="id" value="' . $id . '">'
            . '<input type="hidden" name="dir" value="' . $dir . '"><input type="hidden" name="return" value="' . e($section) . '">'
            . '<button class="btn btn-sm btn-move" title="Move ' . $dir . '">' . $sym . '</button></form>';
    }
    if (array_key_exists('is_active', $row)) {
        $lbl = $row['is_active'] ? 'On' : 'Off';
        $cls = $row['is_active'] ? 'btn-green' : 'btn-gray';
        echo '<form method="POST" class="inline"><input type="hidden" name="action" value="toggle">'
            . '<input type="hidden" name="table" value="' . e($table) . '"><input type="hidden" name="id" value="' . $id . '">'
            . '<input type="hidden" name="return" value="' . e($section) . '">'
            . '<button class="btn btn-sm ' . $cls . '" title="Toggle visibility">' . $lbl . '</button></form>';
    }
    echo '<form method="POST" class="inline" onsubmit="return confirm(\'Delete this item? This cannot be undone.\')">'
        . '<input type="hidden" name="action" value="delete"><input type="hidden" name="table" value="' . e($table) . '">'
        . '<input type="hidden" name="id" value="' . $id . '"><input type="hidden" name="return" value="' . e($section) . '">'
        . '<button class="btn btn-red btn-sm">Delete</button></form>';
    echo '</div>';
}
