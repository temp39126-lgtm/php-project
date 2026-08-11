<?php
$categories = $db->query("SELECT * FROM categories ORDER BY sort_order, name")->fetchAll();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit = null;
if ($editId) {
    $s = $db->prepare("SELECT * FROM products WHERE id=?");
    $s->execute([$editId]);
    $edit = $s->fetch();
}

// ---------------- EDIT MODE ----------------
if ($edit):
    // One-time import of legacy image1-4 into product_images for older products.
    $imgCount = (int)$db->query("SELECT COUNT(*) FROM product_images WHERE product_id=" . $editId)->fetchColumn();
    if ($imgCount === 0) {
        $o = 1;
        foreach (['image1', 'image2', 'image3', 'image4'] as $col) {
            if (!empty($edit[$col])) {
                $db->prepare("INSERT INTO product_images (product_id,image,sort_order,is_primary) VALUES (?,?,?,?)")
                   ->execute([$editId, $edit[$col], $o, $o === 1 ? 1 : 0]);
                $o++;
            }
        }
    }
    $images = $db->query("SELECT * FROM product_images WHERE product_id=$editId ORDER BY is_primary DESC, sort_order, id")->fetchAll();
    $features = $db->query("SELECT * FROM product_features WHERE product_id=$editId ORDER BY sort_order, id")->fetchAll();
    $variations = $db->query("SELECT * FROM product_variations WHERE product_id=$editId ORDER BY sort_order, id")->fetchAll();
    $ret = 'products?edit=' . $editId;
?>
<a href="<?= e(adm('products')) ?>" class="back-link">&larr; Back to all products</a>
<h2 class="page-title">Edit Product: <?= e($edit['name']) ?> <a href="/<?= e($edit['slug']) ?>" target="_blank" style="font-size:13px;">(view live ↗)</a></h2>

<div class="card">
    <h3>Product Information</h3>
    <form method="POST">
        <input type="hidden" name="action" value="save_product">
        <input type="hidden" name="id" value="<?= $editId ?>">
        <div class="form-row">
            <?php adm_text('Product Title *', 'name', $edit['name'], 'text', 'required'); ?>
            <div class="form-group"><label>Category *</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= $edit['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row-3">
            <?php adm_text('Grams / Weight', 'grams', $edit['grams'] ?? '', 'text', 'placeholder="e.g. 50g"'); ?>
            <?php adm_text('Flavour', 'flavour', $edit['flavour'] ?? '', 'text', 'placeholder="e.g. Chocolate"'); ?>
            <?php adm_text('Default Price (₹)', 'price', $edit['price'] ?? '', 'number', 'step="0.01"'); ?>
        </div>
        <div class="form-row-3">
            <?php adm_text('WhatsApp Number', 'whatsapp_number', $edit['whatsapp_number'] ?? '', 'text', 'placeholder="leave blank to use site default"'); ?>
            <?php adm_text('Contact Number', 'contact_number', $edit['contact_number'] ?? '', 'text', 'placeholder="leave blank to use site default"'); ?>
            <div class="form-group"><label>Badge</label>
                <select name="badge">
                    <?php foreach (['' => 'None', 'New Arrival' => 'New Arrival', 'New Flavor' => 'New Flavor', 'Bestseller' => 'Bestseller'] as $bv => $bl): ?>
                    <option value="<?= e($bv) ?>" <?= ($edit['badge'] ?? '') === $bv ? 'selected' : '' ?>><?= e($bl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php adm_text('Short Description', 'short_desc', $edit['short_desc'] ?? '', 'text', 'maxlength="500"'); ?>
        <div class="form-group"><label>Full Description (HTML allowed: &lt;p&gt;, &lt;b&gt;, &lt;ul&gt;, &lt;img&gt; …)</label>
            <textarea name="description" style="min-height:160px;"><?= e($edit['description'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <?php adm_text('Sort Order', 'sort_order', $edit['sort_order'] ?? 0, 'number'); ?>
            <div class="form-group"><label class="chk" style="margin-top:28px;"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" <?= $edit['is_active'] ? 'checked' : '' ?>> Active (visible on site)</label></div>
        </div>
        <button class="btn btn-blue">Save Product Information</button>
    </form>
</div>

<div class="card">
    <h3>Product Images</h3>
    <p class="hint">Upload multiple images, choose the primary one, reorder or delete. The primary image is shown first.</p>
    <table>
        <tr><th>Image</th><th>Primary</th><th>Order</th><th>Actions</th></tr>
        <?php foreach ($images as $img): ?>
        <tr>
            <td><img class="thumb" src="<?= e(asset_url($img['image'])) ?>"></td>
            <td><?php if ($img['is_primary']): ?><span class="badge-sm badge-green">Primary</span><?php else: ?>
                <form method="POST" class="inline"><input type="hidden" name="action" value="set_primary_image"><input type="hidden" name="id" value="<?= (int)$img['id'] ?>"><input type="hidden" name="product_id" value="<?= $editId ?>"><button class="btn btn-sm btn-gray">Make primary</button></form>
            <?php endif; ?></td>
            <td><?= (int)$img['sort_order'] ?></td>
            <td class="row-actions">
                <?php foreach (['up' => '↑', 'down' => '↓'] as $dir => $sym): ?>
                <form method="POST" class="inline"><input type="hidden" name="action" value="reorder"><input type="hidden" name="table" value="product_images"><input type="hidden" name="id" value="<?= (int)$img['id'] ?>"><input type="hidden" name="dir" value="<?= $dir ?>"><input type="hidden" name="return" value="<?= e($ret) ?>"><button class="btn btn-sm btn-move"><?= $sym ?></button></form>
                <?php endforeach; ?>
                <form method="POST" class="inline" onsubmit="return confirm('Delete this image?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="table" value="product_images"><input type="hidden" name="id" value="<?= (int)$img['id'] ?>"><input type="hidden" name="return" value="<?= e($ret) ?>"><button class="btn btn-sm btn-red">Delete</button></form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($images)): ?><tr><td colspan="4" style="color:#999;">No images yet.</td></tr><?php endif; ?>
    </table>
    <form method="POST" enctype="multipart/form-data" style="margin-top:16px;">
        <input type="hidden" name="action" value="upload_image">
        <input type="hidden" name="product_id" value="<?= $editId ?>">
        <div class="form-group"><label>Add Image</label><input type="file" name="image_file" accept="image/*"><input type="text" name="image_url" class="mt8" placeholder="or paste an image URL"></div>
        <button class="btn btn-blue btn-sm">Upload Image</button>
    </form>
</div>

<div class="card">
    <h3>Product Features</h3>
    <p class="hint">Bullet-point features shown on the product page.</p>
    <?php foreach ($features as $f): ?>
    <div class="row-actions" style="margin-bottom:8px;">
        <form method="POST" class="inline" style="flex:1;display:flex;gap:6px;">
            <input type="hidden" name="action" value="save_feature"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><input type="hidden" name="product_id" value="<?= $editId ?>">
            <input type="text" name="feature" value="<?= e($f['feature']) ?>" style="flex:1;padding:8px 12px;border:1px solid #ddd;border-radius:8px;">
            <button class="btn btn-sm btn-blue">Save</button>
        </form>
        <?php foreach (['up' => '↑', 'down' => '↓'] as $dir => $sym): ?>
        <form method="POST" class="inline"><input type="hidden" name="action" value="reorder"><input type="hidden" name="table" value="product_features"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><input type="hidden" name="dir" value="<?= $dir ?>"><input type="hidden" name="return" value="<?= e($ret) ?>"><button class="btn btn-sm btn-move"><?= $sym ?></button></form>
        <?php endforeach; ?>
        <form method="POST" class="inline" onsubmit="return confirm('Delete this feature?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="table" value="product_features"><input type="hidden" name="id" value="<?= (int)$f['id'] ?>"><input type="hidden" name="return" value="<?= e($ret) ?>"><button class="btn btn-sm btn-red">✕</button></form>
    </div>
    <?php endforeach; ?>
    <form method="POST" style="display:flex;gap:6px;margin-top:12px;">
        <input type="hidden" name="action" value="save_feature"><input type="hidden" name="product_id" value="<?= $editId ?>">
        <input type="text" name="feature" placeholder="Add a feature…" required style="flex:1;padding:10px 14px;border:1px solid #ddd;border-radius:8px;">
        <button class="btn btn-blue btn-sm">Add Feature</button>
    </form>
</div>

<div class="card">
    <h3>Pack Size Variations</h3>
    <p class="hint">Different pack sizes with optional price and availability.</p>
    <table>
        <tr><th>Pack Size</th><th>Price (₹)</th><th>Status</th><th>Active</th><th>Order</th><th>Actions</th></tr>
        <?php foreach ($variations as $v): ?>
        <tr>
            <form method="POST">
            <input type="hidden" name="action" value="save_variation"><input type="hidden" name="id" value="<?= (int)$v['id'] ?>"><input type="hidden" name="product_id" value="<?= $editId ?>">
            <td><input type="text" name="pack_size" value="<?= e($v['pack_size']) ?>" style="padding:6px 10px;border:1px solid #ddd;border-radius:6px;width:110px;"></td>
            <td><input type="number" step="0.01" name="price" value="<?= e($v['price']) ?>" style="padding:6px 10px;border:1px solid #ddd;border-radius:6px;width:100px;"></td>
            <td><input type="text" name="status" value="<?= e($v['status']) ?>" style="padding:6px 10px;border:1px solid #ddd;border-radius:6px;width:110px;"></td>
            <td><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" <?= $v['is_active'] ? 'checked' : '' ?>></td>
            <td><?= (int)$v['sort_order'] ?></td>
            <td class="row-actions"><button class="btn btn-sm btn-blue">Save</button>
        </form>
                <?php foreach (['up' => '↑', 'down' => '↓'] as $dir => $sym): ?>
                <form method="POST" class="inline"><input type="hidden" name="action" value="reorder"><input type="hidden" name="table" value="product_variations"><input type="hidden" name="id" value="<?= (int)$v['id'] ?>"><input type="hidden" name="dir" value="<?= $dir ?>"><input type="hidden" name="return" value="<?= e($ret) ?>"><button class="btn btn-sm btn-move"><?= $sym ?></button></form>
                <?php endforeach; ?>
                <form method="POST" class="inline" onsubmit="return confirm('Delete this pack size?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="table" value="product_variations"><input type="hidden" name="id" value="<?= (int)$v['id'] ?>"><input type="hidden" name="return" value="<?= e($ret) ?>"><button class="btn btn-sm btn-red">✕</button></form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <form method="POST" style="display:flex;gap:6px;margin-top:12px;flex-wrap:wrap;align-items:center;">
        <input type="hidden" name="action" value="save_variation"><input type="hidden" name="product_id" value="<?= $editId ?>">
        <input type="text" name="pack_size" placeholder="Pack size (e.g. 500g)" required style="padding:10px 14px;border:1px solid #ddd;border-radius:8px;">
        <input type="number" step="0.01" name="price" placeholder="Price" style="padding:10px 14px;border:1px solid #ddd;border-radius:8px;width:120px;">
        <input type="text" name="status" placeholder="Status" value="In Stock" style="padding:10px 14px;border:1px solid #ddd;border-radius:8px;width:130px;">
        <input type="hidden" name="is_active" value="1">
        <button class="btn btn-blue btn-sm">Add Pack Size</button>
    </form>
</div>

<?php
// ---------------- LIST MODE ----------------
else:
    $products = $db->query("SELECT p.*, c.name AS cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id ORDER BY p.sort_order, p.id DESC")->fetchAll();
?>
<h2 class="page-title">Products</h2>
<div class="card">
    <h3>Add New Product</h3>
    <p class="hint">Create the product, then add images, features and pack sizes on the next screen.</p>
    <form method="POST">
        <input type="hidden" name="action" value="save_product">
        <div class="form-row">
            <?php adm_text('Product Title *', 'name', '', 'text', 'required'); ?>
            <div class="form-group"><label>Category *</label>
                <select name="category_id" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row-3">
            <?php adm_text('Grams', 'grams', '', 'text', 'placeholder="e.g. 50g"'); ?>
            <?php adm_text('Flavour', 'flavour', ''); ?>
            <?php adm_text('Price (₹)', 'price', '', 'number', 'step="0.01"'); ?>
        </div>
        <?php adm_text('Short Description', 'short_desc', ''); ?>
        <input type="hidden" name="badge" value="">
        <input type="hidden" name="description" value="">
        <input type="hidden" name="whatsapp_number" value="">
        <input type="hidden" name="contact_number" value="">
        <input type="hidden" name="sort_order" value="0">
        <button class="btn btn-blue">Create Product</button>
    </form>
</div>
<div class="card">
    <h3>All Products (<?= count($products) ?>)</h3>
    <table>
        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Badge</th><th>Order</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($products as $p):
            $primary = $db->query("SELECT image FROM product_images WHERE product_id=" . (int)$p['id'] . " ORDER BY is_primary DESC, sort_order LIMIT 1")->fetchColumn();
            if (!$primary) $primary = $p['image1'];
        ?>
        <tr>
            <td><?php if ($primary): ?><img class="thumb" src="<?= e(asset_url($primary)) ?>"><?php else: ?>—<?php endif; ?></td>
            <td><strong><?= e($p['name']) ?></strong></td>
            <td><?= e($p['cat_name']) ?></td>
            <td><?= $p['price'] ? '₹' . number_format($p['price'], 2) : '—' ?></td>
            <td><?= $p['badge'] ? '<span class="badge-sm badge-green">' . e($p['badge']) . '</span>' : '—' ?></td>
            <td><?= (int)$p['sort_order'] ?></td>
            <td><span class="badge-sm <?= $p['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $p['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('products', 'products', $p); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>
