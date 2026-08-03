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
                <div class="contact-info-card">
                    <h6>Email</h6>
                    <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
                </div>
                <div class="contact-info-card">
                    <h6>Phone</h6>
                    <a href="tel:<?= str_replace(' ','',SITE_PHONE) ?>"><?= SITE_PHONE ?></a>
                </div>
                <div class="contact-info-card">
                    <h6>WhatsApp</h6>
                    <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>" target="_blank" rel="noopener"><?= SITE_PHONE ?></a>
                </div>
                <div class="contact-info-card">
                    <h6>Address</h6>
                    <p><?= SITE_ADDRESS ?></p>
                </div>
                <div class="contact-info-card">
                    <h6>Opening Hours</h6>
                    <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 4:00 PM<br>Sunday: Closed</p>
                </div>
                
                <!-- Google Map -->
                <div class="contact-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3499.6775!2d77.1456!3d28.7004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d03e5f0000001%3A0x0!2sLok%20Vihar%2C%20Pitampura%2C%20New%20Delhi%2C%20110034!5e0!3m2!1sen!2sin!4v1700000000000" width="100%" height="250" style="border:0;border-radius:12px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
