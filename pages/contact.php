<?php
$pageTitle = "Contact";
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf'])) {
    if (verifyCSRF($_POST['csrf'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($name && $email && $message) {
            $stmt = $db->prepare("INSERT INTO enquiries (name, email, phone, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $message]);
            $msg = 'success';
        }
    }
}
$csrf = generateCSRFToken();
?>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-form">
                <h1>Contact us</h1>
                <p>Feel free to contact us with any questions or concerns. We appreciate your interest and look forward to hearing from you.</p>
                
                <?php if ($msg === 'success'): ?>
                <div style="padding:15px;background:#d4edda;border-radius:8px;margin-bottom:20px;color:#155724;">Thank you! We'll get back to you soon.</div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" name="name" placeholder="Your name" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" placeholder="Your email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" placeholder="Your phone number">
                    </div>
                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" placeholder="Your message" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Submit</button>
                </form>
            </div>
            <div class="contact-info">
                <?php if (setting('contact_email')): ?>
                <div class="contact-info-card">
                    <h6>Email</h6>
                    <a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a>
                </div>
                <?php endif; ?>
                <?php if (setting('contact_phone')): ?>
                <div class="contact-info-card">
                    <h6>Phone</h6>
                    <a href="tel:<?= e(str_replace(' ','',setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a>
                </div>
                <?php endif; ?>
                <?php if (setting('contact_whatsapp')): ?>
                <div class="contact-info-card">
                    <h6>WhatsApp</h6>
                    <a href="https://wa.me/<?= e(setting('contact_whatsapp')) ?>" target="_blank" rel="noopener"><?= e(setting('contact_phone')) ?></a>
                </div>
                <?php endif; ?>
                <?php if (setting('contact_address')): ?>
                <div class="contact-info-card">
                    <h6>Address</h6>
                    <p><?= e(setting('contact_address')) ?></p>
                </div>
                <?php endif; ?>
                <?php if (setting('contact_hours')): ?>
                <div class="contact-info-card">
                    <h6>Opening Hours</h6>
                    <p><?= nl2br(e(setting('contact_hours'))) ?></p>
                </div>
                <?php endif; ?>

                <?php if (setting('contact_map')): ?>
                <div class="contact-map">
                    <iframe src="<?= e(setting('contact_map')) ?>" width="100%" height="250" style="border:0;border-radius:12px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
