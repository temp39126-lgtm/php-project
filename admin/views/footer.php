<?php
function ft_text($key, $label, $ph = '') {
    echo '<div class="form-group"><label>' . e($label) . '</label><input type="text" name="s[' . e($key) . ']" value="' . e(setting($key)) . '" placeholder="' . e($ph) . '"></div>';
}
function ft_area($key, $label) {
    echo '<div class="form-group"><label>' . e($label) . '</label><textarea name="s[' . e($key) . ']">' . e(setting($key)) . '</textarea></div>';
}
function ft_chk($key, $label) {
    echo '<div class="form-group"><label class="chk"><input type="hidden" name="s[' . e($key) . ']" value="0">'
        . '<input type="checkbox" name="s[' . e($key) . ']" value="1" ' . (cms_flag($key) ? 'checked' : '') . '> ' . e($label) . '</label></div>';
}
?>
<h2 class="page-title">Footer / Contact Information</h2>
<p class="hint" style="margin-bottom:16px;">Contact details here are shared across the footer, the contact page and product enquiry buttons.</p>
<form method="POST">
    <input type="hidden" name="action" value="save_settings">
    <input type="hidden" name="return" value="footer">

    <div class="card">
        <h3>Contact &amp; WhatsApp</h3>
        <div class="form-row">
            <?php ft_text('contact_email', 'Email'); ft_text('contact_phone', 'Phone'); ?>
        </div>
        <div class="form-row">
            <?php ft_text('contact_whatsapp', 'WhatsApp Number (digits only, incl. country code)'); ft_text('contact_address', 'Address'); ?>
        </div>
        <?php ft_area('contact_hours', 'Opening Hours (contact page)'); ?>
        <?php ft_text('contact_map', 'Google Maps Embed URL (contact page)'); ?>
    </div>

    <div class="card">
        <h3>Social Links</h3>
        <div class="form-row-3">
            <?php ft_text('social_facebook', 'Facebook'); ft_text('social_instagram', 'Instagram'); ft_text('social_twitter', 'X / Twitter'); ?>
        </div>
    </div>

    <div class="card">
        <h3>Footer Content</h3>
        <?php ft_chk('footer_enabled', 'Show footer'); ?>
        <?php ft_chk('footer_newsletter_enabled', 'Show newsletter signup box'); ?>
        <div class="form-row">
            <?php ft_text('site_tagline', 'Footer Tagline'); ft_text('footer_copy', 'Copyright Text'); ?>
        </div>
    </div>

    <button class="btn btn-blue">Save Footer / Contact</button>
</form>
