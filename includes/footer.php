<?php
/*
|--------------------------------------------------------------------------
| Shreeji Decoration
|--------------------------------------------------------------------------
| Shared site footer.
| Include this AFTER loading $settings from the `settings` table, e.g:
|
|   require_once 'config/db.php';
|   $settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));
|   include 'includes/footer.php';
|
| Relies on the global .main-footer / .footer-* classes already
| defined in style.css.
|--------------------------------------------------------------------------
*/

if (!isset($settings) || !is_array($settings)) {
    $settings = [
        'website_name' => 'Shreeji Decoration',
        'instagram'    => '#',
        'facebook'     => '#',
        'whatsapp'     => '#',
    ];
}
?>
<footer class="main-footer">
  <div class="footer-top">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="footer-brand d-flex align-items-center gap-2 mb-3">
            <div class="brand-icon" style="width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0;"><img src="uploads/site-content/<?= !empty($settings['logo']) ? htmlspecialchars($settings['logo']) : 'logo.jpg' ?>" alt="Shreeji Decoration" style="width:100%;height:100%;object-fit:cover;display:block;"></div>
            <div>
              <div class="brand-name">
                <?= htmlspecialchars($settings['website_name'] ?? 'Shreeji Decoration'); ?>
              </div>
              <div class="brand-tagline" style="color:#C9A063">सजाएँ हर पल, हर जश्न</div>
            </div>
          </div>
          <p class="text-white-50 small">Premium Indian decorations for every celebration. Serving individuals, families, and businesses across India since 2009.</p>
          <div class="social-bar mt-3">
            <a href="<?= htmlspecialchars($settings['instagram'] ?? '#'); ?>" target="_blank" class="text-white">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="<?= htmlspecialchars($settings['facebook'] ?? '#'); ?>" target="_blank" class="text-white">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="<?= htmlspecialchars($settings['whatsapp'] ?? '#'); ?>" target="_blank" class="text-white">
              <i class="bi bi-whatsapp"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-2 col-md-4 col-6">
          <h6 class="footer-head">Quick Links</h6>
          <ul class="footer-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="wholesale.php">Wholesale</a></li>
            <li><a href="retail.php">Retail Shop</a></li>
            <li><a href="index.php#gallery">Gallery</a></li>
            <li><a href="index.php#contact">Contact</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-4 col-6">
          <h6 class="footer-head">Occasions</h6>
          <ul class="footer-links">
            <li><a href="retail.php">Weddings</a></li>
            <li><a href="retail.php">Diwali</a></li>
            <li><a href="retail.php">Navratri</a></li>
            <li><a href="retail.php">Birthdays</a></li>
            <li><a href="wholesale.php">Corporate</a></li>
            <li><a href="retail.php">Puja Decor</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-4">
          <h6 class="footer-head">Help</h6>
          <ul class="footer-links">
            <li><a href="faq.php">FAQs</a></li>
            <li><a href="privacy-policy.php">Privacy Policy</a></li>
            <li><a href="terms.php">Terms & Conditions</a></li>
            <li><a href="refund-policy.php">Refund Policy</a></li>
            <li><a href="return-product.php">Returns</a></li>
            <li><a href="my-orders.php">Track Order</a></li>
          </ul>
          <div class="mt-3">
            <h6 class="footer-head">Payment Methods</h6>
            <div class="payment-badges">
              <span>UPI</span><span>NetBanking</span><span>Cards</span><span>COD</span><span>EMI</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom text-center">
    <div class="container">
      <p class="mb-0 small text-white-50">© <?= date('Y'); ?> <?= htmlspecialchars($settings['website_name'] ?? 'Shreeji Decoration'); ?>.
        All Rights Reserved. | Designed with ❤️ in Ahmedabad, India</p>
      <p class="mb-0 small text-white-50 mt-1">
        <a href="privacy-policy.php" class="text-white-50">Privacy Policy</a> &nbsp;|&nbsp;
        <a href="terms.php" class="text-white-50">Terms</a> &nbsp;|&nbsp;
        <a href="refund-policy.php" class="text-white-50">Refunds</a> &nbsp;|&nbsp;
        <a href="faq.php" class="text-white-50">FAQs</a> &nbsp;|&nbsp;
        <a href="about.php" class="text-white-50">About Us</a>
      </p>
    </div>
  </div>
</footer>