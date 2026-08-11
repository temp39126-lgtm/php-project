<?php
// Mark all as read when viewing.
$db->query("UPDATE enquiries SET is_read=1 WHERE is_read=0");
$rows = $db->query("SELECT * FROM enquiries ORDER BY id DESC LIMIT 200")->fetchAll();
?>
<h2 class="page-title">Enquiries</h2>
<div class="card">
    <table>
        <tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th></th></tr>
        <?php foreach ($rows as $eq): ?>
        <tr>
            <td><?= date('d M Y H:i', strtotime($eq['created_at'])) ?></td>
            <td><strong><?= e($eq['name']) ?></strong></td>
            <td><a href="mailto:<?= e($eq['email']) ?>"><?= e($eq['email']) ?></a></td>
            <td><?= e($eq['phone']) ?></td>
            <td><?= e($eq['message']) ?></td>
            <td>
                <form method="POST" class="inline" onsubmit="return confirm('Delete this enquiry?')">
                    <input type="hidden" name="action" value="delete"><input type="hidden" name="table" value="enquiries">
                    <input type="hidden" name="id" value="<?= (int)$eq['id'] ?>"><input type="hidden" name="return" value="enquiries">
                    <button class="btn btn-red btn-sm">✕</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="6" style="text-align:center;color:#999;">No enquiries yet.</td></tr><?php endif; ?>
    </table>
</div>
