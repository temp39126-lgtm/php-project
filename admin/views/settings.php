<?php ?>
<h2 class="page-title">Account</h2>
<div class="card">
    <h3>Change Password</h3>
    <form method="POST">
        <input type="hidden" name="action" value="change_password">
        <div class="form-group"><label>New Password (min 6 chars)</label><input type="password" name="new_password" minlength="6" required></div>
        <button class="btn btn-blue">Change Password</button>
    </form>
</div>
<div class="card">
    <h3>Logged in as</h3>
    <p><?= e($_SESSION['admin_user'] ?? '') ?></p>
</div>
