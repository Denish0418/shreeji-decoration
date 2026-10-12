<?php

require_once 'includes/session.php';
require_once 'config/db.php';
$aboutQuery = mysqli_query($conn,"
SELECT *
FROM site_content
WHERE page_name='about'
LIMIT 1
");

$about = mysqli_fetch_assoc($aboutQuery);
if (!$about) {
  $about = [
    'title' => 'Crafting Memories with',
    'subtitle' => 'Indian Artistry',
    'content' => "Shreeji Decoration was founded with a simple belief — every celebration deserves to look extraordinary.\n\nFrom the bustling streets of Ahmedabad, we have grown to serve thousands of families, event planners, and businesses across India.",
    'since_year' => '2009',
    'image' => '',
    'image2' => ''
  ];
}
/* FETCH SETTINGS */

$settingsQuery = mysqli_query(
  $conn,
  "SELECT * FROM settings LIMIT 1"
);

$settings = mysqli_fetch_assoc($settingsQuery);
$ratingData =
  mysqli_fetch_assoc(
    mysqli_query(
      $conn,
      "SELECT AVG(rating) avg_rating
         FROM product_reviews"
    )
  );

$avgRating =
  $ratingData['avg_rating']
  ? round($ratingData['avg_rating'], 1)
  : 5;

$productData =
  mysqli_fetch_assoc(
    mysqli_query(
      $conn,
      "SELECT COUNT(*) total
         FROM products"
    )
  );

$totalProducts =
  $productData['total'];

$customerData =
  mysqli_fetch_assoc(
    mysqli_query(
      $conn,
      "SELECT COUNT(DISTINCT phone) total
         FROM orders"
    )
  );

$totalCustomers =
  $customerData['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $settings['website_name']; ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>

<body>

<?php include 'includes/header.php'; ?>

  

  <!-- ─── HERO ─── -->
  <section class="hero-section" id="home">
    <div class="hero-overlay"></div>
    <div class="hero-pattern"></div>
    <div class="container position-relative z-1 text-center py-5">
      <div class="hero-badge mb-3">✦ Premium Indian Decorations ✦</div>
      <h1 class="hero-title">Transform Every Celebration<br /><em>into a Masterpiece</em></h1>
      <p class="hero-sub mt-3 mb-5">From grand weddings to intimate pujas — Shreeji Decoration brings tradition,<br class="d-none d-md-block" /> colour, and elegance to every occasion.</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="retail.php" class="btn btn-hero-primary">Shop Retail <i class="bi bi-bag-heart ms-1"></i></a>
        <a href="wholesale.php" class="btn btn-hero-outline">Wholesale Inquiry <i class="bi bi-building ms-1"></i></a>
      </div>
      <div class="hero-stats mt-5 d-flex justify-content-center gap-5 flex-wrap">
        <div class="stat-item">
          <div class="stat-num">15+</div>
          <div class="stat-lbl">Years Experience</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-num">
            <?= $totalCustomers ?>+
          </div>

          <div class="stat-lbl">
            Happy Customers
          </div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-num">
            <?= $totalProducts ?>+
          </div>

          <div class="stat-lbl">
            Products
          </div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <div class="stat-num">
            <?= $avgRating ?>
            ⭐
          </div>

          <div class="stat-lbl">
            Average Rating
          </div>
        </div>
      </div>
    </div>
    <div class="hero-scroll-hint">
      <i class="bi bi-chevron-double-down"></i>
    </div>
  </section>

  <!-- ─── MARQUEE ─── -->
  <div class="marquee-bar py-2">
    <div class="marquee-track">
      <span>🎊 Wedding Decorations &nbsp;•&nbsp; 🪔 Diwali Decor &nbsp;•&nbsp; 🌸 Haldi & Mehndi &nbsp;•&nbsp; 🎉 Birthday Setups &nbsp;•&nbsp; 🏮 Navratri Garlands &nbsp;•&nbsp; 🌺 Puja Accessories &nbsp;•&nbsp; 🎋 Ganesh Chaturthi &nbsp;•&nbsp; 💐 Floral Arrangements &nbsp;•&nbsp; 🎑 Festival Lighting &nbsp;•&nbsp;</span>
      <span>🎊 Wedding Decorations &nbsp;•&nbsp; 🪔 Diwali Decor &nbsp;•&nbsp; 🌸 Haldi & Mehndi &nbsp;•&nbsp; 🎉 Birthday Setups &nbsp;•&nbsp; 🏮 Navratri Garlands &nbsp;•&nbsp; 🌺 Puja Accessories &nbsp;•&nbsp; 🎋 Ganesh Chaturthi &nbsp;•&nbsp; 💐 Floral Arrangements &nbsp;•&nbsp; 🎑 Festival Lighting &nbsp;•&nbsp;</span>
    </div>
  </div>

 <!-- ─── ABOUT ─── -->
<section class="section-pad bg-cream" id="about">
  <div class="container">

    <div class="row align-items-center g-5">

      <!-- LEFT SIDE: ABOUT IMAGES -->
      <div class="col-lg-6">

        <div class="about-img-grid">

          <?php if (!empty($about['image'])) { ?>

            <div
              class="about-img ab1"
              style="
                background-image: url('uploads/site-content/<?= htmlspecialchars($about['image'], ENT_QUOTES, 'UTF-8'); ?>');
                background-size: cover;
                background-position: center;
              ">
            </div>

          <?php } else { ?>

            <div class="about-img ab1"></div>

          <?php } ?>


          <?php if (!empty($about['image2'])) { ?>

            <div
              class="about-img ab2"
              style="
                background-image: url('uploads/site-content/<?= htmlspecialchars($about['image2'], ENT_QUOTES, 'UTF-8'); ?>');
                background-size: cover;
                background-position: center;
              ">
            </div>

          <?php } else { ?>

            <div class="about-img ab2"></div>

          <?php } ?>


          


          <!-- DYNAMIC SINCE YEAR -->
          <div class="about-badge">

            <i class="bi bi-award-fill"></i>

            <br>

            Since

            <br>

            <?= htmlspecialchars(
              $about['since_year'] ?? '2009',
              ENT_QUOTES,
              'UTF-8'
            ); ?>

          </div>

        </div>

      </div>


      <!-- RIGHT SIDE: ABOUT CONTENT -->
      <div class="col-lg-6">

        <div class="section-label">
          Our Story
        </div>


        <!-- DYNAMIC TITLE -->
        <h2 class="section-title">

          <?= htmlspecialchars(
            $about['title'] ?? 'Crafting Memories with',
            ENT_QUOTES,
            'UTF-8'
          ); ?>

          <br>

          <em>

            <?= htmlspecialchars(
              $about['subtitle'] ?? 'Indian Artistry',
              ENT_QUOTES,
              'UTF-8'
            ); ?>

          </em>

        </h2>


        <!-- DYNAMIC DESCRIPTION -->
        <div class="mt-3 text-muted about-dynamic-content">

          <?php

          $aboutContent =
            trim($about['content'] ?? '');

          if ($aboutContent !== '') {

            $paragraphs =
              preg_split(
                '/\R\s*\R/',
                $aboutContent
              );

            foreach ($paragraphs as $paragraph) {

              if (trim($paragraph) === '') {
                continue;
              }

          ?>

              <p class="text-muted">

                <?= nl2br(
                  htmlspecialchars(
                    trim($paragraph),
                    ENT_QUOTES,
                    'UTF-8'
                  )
                ); ?>

              </p>

          <?php

            }

          }

          ?>

        </div>


        <!-- FEATURE CARDS -->
        <div class="row mt-4 g-3">

          <div class="col-6">

            <div class="feat-card">

              <i class="bi bi-patch-check-fill text-saffron"></i>

              <div>

                <strong>
                  100% Authentic
                </strong>

                <p class="small text-muted mb-0">
                  Genuine Indian materials
                </p>

              </div>

            </div>

          </div>


          <div class="col-6">

            <div class="feat-card">

              <i class="bi bi-truck text-saffron"></i>

              <div>

                <strong>
                  Pan India Delivery
                </strong>

                <p class="small text-muted mb-0">
                  Doorstep convenience
                </p>

              </div>

            </div>

          </div>


          <div class="col-6">

            <div class="feat-card">

              <i class="bi bi-palette2 text-saffron"></i>

              <div>

                <strong>
                  Custom Designs
                </strong>

                <p class="small text-muted mb-0">
                  Bespoke decor solutions
                </p>

              </div>

            </div>

          </div>


          <div class="col-6">

            <div class="feat-card">

              <i class="bi bi-headset text-saffron"></i>

              <div>

                <strong>
                  Expert Support
                </strong>

                <p class="small text-muted mb-0">
                  7 days a week assistance
                </p>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>
</section>

  <!-- ─── SECTION DIVIDER ─── -->
  <div class="section-divider">
    <div class="container text-center py-4">
      <h3 class="divider-text">Choose Your Shopping Experience</h3>
      <p class="text-white  ">Whether you need a few pieces or bulk quantities — we have the perfect solution for you.</p>
      <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
        <a href="wholesale.php" class="btn btn-section-primary">Wholesale Section <i class="bi bi-arrow-right ms-1"></i></a>
        <a href="retail.php" class="btn btn-section-outline">Retail Section <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
    </div>
  </div>

  <!-- ─── GALLERY ─── -->

  <section class="section-pad" id="gallery">

    <div class="container">

      <div class="text-center mb-5">

        <div class="section-label">
          Our Work
        </div>

        <h2 class="section-title">
          Decoration Gallery
        </h2>

        <p class="text-muted">
          A glimpse of celebrations we have helped make unforgettable.
        </p>

      </div>

      <div class="row g-4">

        <?php

        $galleryQuery = mysqli_query(
          $conn,
          "SELECT * FROM gallery ORDER BY id DESC"
        );

        $galleryImageList = [];

        while ($gallery = mysqli_fetch_assoc($galleryQuery)) {

          // Skip entries whose image file no longer exists on disk,
          // instead of showing a broken image icon to visitors.
          if (!file_exists(__DIR__ . '/uploads/gallery/' . $gallery['image'])) {
            continue;
          }

          $galleryImageList[] = 'uploads/gallery/' . $gallery['image'];

        ?>

          <div class="col-lg-4 col-md-6">

            <div class="gallery-card">

              <img src="uploads/gallery/<?php echo $gallery['image']; ?>"

                class="gallery-image"

                onclick="openGalleryImage(this.src)">

              <div class="gallery-overlay">

                <h5>
                  <?php echo $gallery['title']; ?>
                </h5>

                <p>
                  <?php echo $gallery['category']; ?>
                </p>

              </div>

            </div>

          </div>

        <?php } ?>

      </div>

    </div>

  </section>

  <!-- ─── TESTIMONIALS ─── -->
  <section class="testimonials-section section-pad" id="testimonials">
    <div class="container">
      <div class="text-center mb-5">
        <div class="section-label">Customer Love</div>
        <h2 class="section-title text-white">What Our Customers Say</h2>
      </div>
      <?php

      $reviews =
        mysqli_query(
          $conn,
          "SELECT *
FROM product_reviews
WHERE status='Approved'
ORDER BY id DESC
LIMIT 3"
        );
        

      ?>
      <div class="row g-4">

        <?php while ($review = mysqli_fetch_assoc($reviews)) { ?>

          <div class="col-lg-4 col-md-6">

            <div class="testimonial-card">

              <div class="stars mb-3">

                <?php
                for ($i = 1; $i <= 5; $i++) {
                  echo $i <= $review['rating']
                    ? '⭐'
                    : '☆';
                }
                ?>

              </div>

              <p class="testimonial-text">

                <?= htmlspecialchars($review['review']) ?>

              </p>

              <div class="testimonial-user">

                <h5>

                  <?= htmlspecialchars($review['customer_name']) ?>

                </h5>

              </div>

            </div>

          </div>

        <?php } ?>

      </div>



      <!-- Trust Badges -->
      <div class="trust-bar mt-5 d-flex justify-content-center align-items-center gap-5 flex-wrap">
        <div class="trust-item"><i class="bi bi-shield-check-fill"></i><span>Trusted Brand</span></div>
        <div class="trust-item"><i class="bi bi-truck"></i><span>Pan India Delivery</span></div>
        <div class="trust-item"><i class="bi bi-arrow-counterclockwise"></i><span>Easy Returns</span></div>
        <div class="trust-item"><i class="bi bi-lock-fill"></i><span>Secure Payments</span></div>
        <div class="trust-item"><i class="bi bi-whatsapp"></i><span>WhatsApp Support</span></div>
      </div>
    </div>
  </section>

  <!-- ─── CONTACT ─── -->
  <section class="section-pad bg-cream" id="contact">
    <div class="container">
      <div class="text-center mb-5">
        <div class="section-label">Reach Us</div>
        <h2 class="section-title">Get in Touch</h2>
      </div>
      <div class="row g-5">
        <div class="col-lg-5">
          <h5 class="mb-4">Visit Our Store</h5>
          <div class="contact-info-list">
            <div class="ci-item">
              <div class="ci-icon"><i class="bi bi-geo-alt-fill"></i></div>
              <div>
                <strong>Shop Address</strong><br />
                <a href="<?php echo $settings['map_link']; ?>"
                  target="_blank"
                  class="text-muted text-decoration-none">

                  <?php echo nl2br($settings['address']); ?>

                </a>
              </div>
            </div>
            <div class="ci-item">
              <div class="ci-icon"><i class="bi bi-telephone-fill"></i></div>
              <div>
                <strong>Phone / WhatsApp</strong><br />
                <a href="tel:+919876543210" class="text-muted"><?= $settings['phone']; ?></a><br />
                <a href="tel:+919876543211" class="text-muted">+91 98765 43211 (Wholesale)</a>
              </div>
            </div>
            <div class="ci-item">
              <div class="ci-icon"><i class="bi bi-envelope-fill"></i></div>
              <div>
                <strong>Email</strong><br />
                <a href="mailto:info@shreejidecoration.com" class="text-muted"><?= $settings['email']; ?></a><br />
              </div>
            </div>
            <div class="ci-item">
              <div class="ci-icon"><i class="bi bi-clock-fill"></i></div>
              <div>
                <strong>Business Hours</strong><br />
                <span class="text-muted">Monday – Sunday: 9:00 AM – 8:00 PM<br />Open on all festivals & holidays</span>
              </div>
            </div>
          </div>
          <div class="social-bar mt-4">
            <a href="<?= $settings['instagram']; ?>"
              target="_blank"
              class="text-white">

              <i class="bi bi-instagram" style="color: black;"></i>

            </a>

            <a href="<?= $settings['facebook']; ?>"
              target="_blank"
              class="text-white">

              <i class="bi bi-facebook" style="color: black;"></i>

            </a>

            <a href="<?= $settings['whatsapp']; ?>"
              target="_blank"
              class="text-white">

              <i class="bi bi-whatsapp" style="color: black;"></i>

            </a>

          </div>
        </div>

        <div class="col-lg-7">
          <div class="contact-form-card">
            <h5 class="mb-4">Send Us a Message</h5>
            <?php if (isset($_GET['quote']) && $_GET['quote'] == "success") { ?>

              <div
                id="successMessage"
                class="alert alert-success mb-3">

                <h5>🎉 Message Sent Successfully!</h5>

                Thank you for contacting Shreeji Decoration.

                Our team has received your inquiry and will get back to you within 24 hours.

              </div>

            <?php } ?>
            <form
              id="contactForm"
              method="POST"
              action="api/contact-api.php">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Your Name *</label>
                  <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="Ramesh Kumar"
                    required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Mobile Number *</label>
                  <input
                    type="tel"
                    name="phone"
                    class="form-control"
                    required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email Address</label>
                  <input
                    type="email"
                    name="email"
                    class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Inquiry Type *</label>
                  <select
                    name="inquiry_type"
                    class="form-select"
                    required>
                    <option value="">Select...</option>
                    <option>Retail Purchase</option>
                    <option>Wholesale / Dealer Inquiry</option>
                    <option>Custom Decoration Order</option>
                    <option>Event Planning Support</option>
                    <option>Product Catalogue Request</option>
                    <option>General Question</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Occasion / Event</label>
                  <input
                    type="text"
                    name="occasion"
                    class="form-control">
                </div>
                <div class="col-12">
                  <label class="form-label">Message *</label>
                  <textarea
                    name="message"
                    class="form-control"
                    required></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-contact-submit w-100">
                    Send Message <i class="bi bi-send-fill ms-2"></i>
                  </button>
                </div>
              </div>
            </form>
            <div class="alert alert-success mt-3 d-none" id="formSuccess">
              🎉 Thank you! We'll get back to you within 24 hours.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── MAP PLACEHOLDER ─── -->
  <div class="map-section">
    <div class="map-placeholder d-flex align-items-center justify-content-center flex-column gap-2">
      <i class="bi bi-geo-alt-fill fs-1 text-saffron"></i>
      <p class="mb-1 fw-semibold">Shreeji Decoration – <?= $settings['address']; ?></p>
      <a href="<?= $settings['map_link']; ?>" class="btn btn-map-link" target="_blank">View on Google Maps</a>
    </div>
  </div>

  <!-- ─── FOOTER ─── -->
  <footer class="main-footer">
    <div class="footer-top">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="footer-brand d-flex align-items-center gap-2 mb-3">
              <?php if (!empty($settings['footer_logo'])) { ?>
                <img src="uploads/site-content/<?= htmlspecialchars($settings['footer_logo']); ?>"
                  alt="<?= htmlspecialchars($settings['website_name'] ?? ''); ?>"
                  style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
              <?php } else { ?>
                <div class="brand-icon" style="width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0;"><img src="uploads/site-content/<?= !empty($settings['logo']) ? htmlspecialchars($settings['logo']) : 'logo.jpg' ?>" alt="Shreeji Decoration" style="width:100%;height:100%;object-fit:cover;display:block;"></div>
              <?php } ?>
              <div>
                <div class="brand-name">
                  <?= $settings['website_name']; ?>
                </div>
                <div class="brand-tagline" style="color:#C9A063"><?= htmlspecialchars($settings['footer_tagline'] ?? 'सजाएँ हर पल, हर जश्न'); ?></div>
              </div>
            </div>
            <p class="text-white-50 small"><?= htmlspecialchars($settings['footer_text'] ?? 'Premium Indian decorations for every celebration. Serving individuals, families, and businesses across India since 2009.'); ?></p>
            <div class="social-bar mt-3">
              <a href="<?= $settings['instagram']; ?>"
                target="_blank"
                class="text-white">

                <i class="bi bi-instagram"></i>

              </a>

              <a href="<?= $settings['facebook']; ?>"
                target="_blank"
                class="text-white">

                <i class="bi bi-facebook"></i>

              </a>

              <a href="<?= $settings['whatsapp']; ?>"
                target="_blank"
                class="text-white">

                <i class="bi bi-whatsapp"></i>

              </a>

            </div>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <h6 class="footer-head">Quick Links</h6>
            <ul class="footer-links">
              <li><a href="#home">Home</a></li>
              <li><a href="#about">About Us</a></li>
              <li><a href="wholesale.php">Wholesale</a></li>
              <li><a href="retail.php">Retail Shop</a></li>
              <li><a href="#gallery">Gallery</a></li>
              <li><a href="faq.php">FAQ</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <h6 class="footer-head">Occasions</h6>
            <ul class="footer-links">
              <?php
              $footerCategoriesQuery = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC LIMIT 8");
              if ($footerCategoriesQuery && mysqli_num_rows($footerCategoriesQuery) > 0) {
                  while ($footerCat = mysqli_fetch_assoc($footerCategoriesQuery)) {
              ?>
                <li><a href="retail.php?category=<?= urlencode($footerCat['name']); ?>"><?= htmlspecialchars($footerCat['name']); ?></a></li>
              <?php
                  }
              } else {
              ?>
                <li><a href="retail.php">All Products</a></li>
              <?php } ?>
            </ul>
          </div>
          <div class="col-lg-4 col-md-4">
            <h6 class="footer-head"><?= htmlspecialchars($settings['footer_newsletter_heading'] ?? 'Newsletter'); ?></h6>
            <p class="text-white-50 small"><?= htmlspecialchars($settings['footer_newsletter_text'] ?? 'Get seasonal offers, new arrivals, and festival special deals.'); ?></p>
            <div class="newsletter-form mt-2">
              <input type="email" class="form-control" placeholder="Your email address" />
              <button class="btn btn-saffron">Subscribe</button>
            </div>
            <div class="mt-3">
              <h6 class="footer-head">Payment Methods</h6>
              <div class="payment-badges">
                <?php
                $paymentMethods = !empty($settings['footer_payment_methods'])
                    ? explode(',', $settings['footer_payment_methods'])
                    : ['UPI', 'NetBanking', 'Cards', 'COD', 'EMI'];
                foreach ($paymentMethods as $method) {
                    echo '<span>' . htmlspecialchars(trim($method)) . '</span>';
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footer-bottom text-center">
      <div class="container">
        <p class="mb-0 small text-white-50">© <?= date('Y'); ?> <?= $settings['website_name']; ?>.
          All Rights Reserved. | Designed with ❤️ in Surat, India</p>
        <p class="mb-0 small text-white-50 mt-1">
          <a href="privacy-policy.php" class="text-white-50">Privacy Policy</a> &nbsp;|&nbsp;
          <a href="terms.php" class="text-white-50">Terms & Conditions</a> &nbsp;|&nbsp;
          <a href="refund-policy.php" class="text-white-50">Refund Policy</a>
        </p>
      </div>
    </div>
  </footer>

  <!-- Floating WhatsApp -->
  <a href="https://wa.me/919876543210" class="whatsapp-float" target="_blank">
    <i class="bi bi-whatsapp"></i>
  </a>

  <!-- Back to Top -->
  <button class="back-to-top" id="backTop"><i class="bi bi-chevron-up"></i></button>

  <!-- IMAGE MODAL -->

  <div class="modal fade"
    id="galleryModal"
    tabindex="-1">

    <div class="modal-dialog modal-dialog-centered modal-xl">

      <div class="modal-content bg-transparent border-0">

        <img id="modalImage"
          src=""
          style="
width:100%;
border-radius:20px;
max-height:90vh;
object-fit:contain;
">

      </div>

    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
  <script>
    const galleryImages = <?php echo json_encode($galleryImageList); ?>;

    let gallerySlideIndex = 0;
    let gallerySlideTimer = null;

    function openGalleryImage(src) {

      document.getElementById("modalImage").src = src;

      const foundIndex = galleryImages.indexOf(src);
      gallerySlideIndex = foundIndex !== -1 ? foundIndex : 0;

      new bootstrap.Modal(
        document.getElementById("galleryModal")
      ).show();

      startGallerySlideshow();

    }

    function startGallerySlideshow() {

      stopGallerySlideshow();

      if (galleryImages.length <= 1) {
        return;
      }

      gallerySlideTimer = setInterval(function() {

        gallerySlideIndex = (gallerySlideIndex + 1) % galleryImages.length;

        document.getElementById("modalImage").src = galleryImages[gallerySlideIndex];

      }, 5000);

    }

    function stopGallerySlideshow() {

      if (gallerySlideTimer) {
        clearInterval(gallerySlideTimer);
        gallerySlideTimer = null;
      }

    }

    document.getElementById("galleryModal").addEventListener("hidden.bs.modal", stopGallerySlideshow);
  </script>

  <script>
    const successMessage =
      document.getElementById('successMessage');

    if (successMessage) {

      setTimeout(function() {

        successMessage.style.display = 'none';

        const url =
          new URL(window.location);

        url.searchParams.delete('quote');

        window.history.replaceState({},
          document.title,
          url.pathname + '#contact'
        );

      }, 5000);

    }
  </script>
  <script>
    window.addEventListener('load', function() {

      const successMessage =
        document.getElementById('successMessage');

      if (successMessage) {

        setTimeout(function() {

          successMessage.style.display = 'none';

          const newUrl =
            window.location.pathname +
            '#contact';

          window.history.replaceState({},
            document.title,
            newUrl
          );

        }, 5000);

      }

    });
  </script>
</body>

</html>