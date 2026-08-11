<?php
$edit = null;
if (isset($_GET['edit'])) {
    $s = $db->prepare("SELECT * FROM reviews WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}
$rows = $db->query("SELECT * FROM reviews ORDER BY sort_order, id")->fetchAll();
?>
<h2 class="page-title">Reviews</h2>
<div class="card">
    <h3><?= $edit ? 'Edit Review' : 'Add Review' ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="save_review">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <?php adm_text('Customer Name *', 'name', $edit['name'] ?? '', 'text', 'required'); ?>
            <?php adm_text('Location', 'location', $edit['location'] ?? ''); ?>
        </div>
        <div class="form-group"><label>Rating</label>
            <select name="rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                <option value="<?= $i ?>" <?= (($edit['rating'] ?? 5) == $i) ? 'selected' : '' ?>><?= str_repeat('★', $i) ?> (<?= $i ?>)</option>
                <?php endfor; ?>
            </select>
        </div>
        <?php adm_textarea('Review Text *', 'review_text', $edit['review_text'] ?? '', 'required'); ?>
        <?php adm_textarea('Answer / Reply (optional)', 'answer', $edit['answer'] ?? ''); ?>
        <input type="hidden" name="current_image" value="<?= e($edit['avatar'] ?? '') ?>">
        <?php adm_image_field('Customer Avatar (optional)', 'image_file', 'image_url', $edit['avatar'] ?? ''); ?>
        <input type="hidden" name="is_active" value="0">
        <div class="form-group"><label class="chk"><input type="checkbox" name="is_active" value="1" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>> Active</label></div>
        <button class="btn btn-blue"><?= $edit ? 'Update' : 'Add Review' ?></button>
        <?php if ($edit): ?><a href="<?= e(adm('reviews')) ?>" class="btn btn-sm btn-gray" style="margin-left:8px;">Cancel</a><?php endif; ?>
    </form>
</div>
<div class="card">
    <h3>All Reviews</h3>
    <table>
        <tr><th>Avatar</th><th>Name</th><th>Rating</th><th>Review</th><th>Reply</th><th>Order</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php if ($r['avatar']): ?><img class="thumb" src="<?= e(asset_url($r['avatar'])) ?>"><?php else: ?>—<?php endif; ?></td>
            <td><strong><?= e($r['name']) ?></strong><br><span style="color:#999;font-size:12px;"><?= e($r['location']) ?></span></td>
            <td style="color:#fbbf24;"><?= str_repeat('★', (int)$r['rating']) ?></td>
            <td><?= e(mb_strimwidth($r['review_text'], 0, 70, '…')) ?></td>
            <td><?= $r['answer'] ? '✓' : '—' ?></td>
            <td><?= (int)$r['sort_order'] ?></td>
            <td><span class="badge-sm <?= $r['is_active'] ? 'badge-green' : 'badge-gray' ?>"><?= $r['is_active'] ? 'Active' : 'Off' ?></span></td>
            <td><?php adm_actions('reviews', 'reviews', $r); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
