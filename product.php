<?php

require_once 'includes/session.php';
require_once 'config/db.php';

$product_id = (int) ($_GET['id'] ?? 0);

if ($product_id <= 0) {
    header("Location: retail.php");
    exit;
}

$productQuery = mysqli_query(
    $conn,
    "SELECT * FROM products WHERE id='$product_id' AND status=1 LIMIT 1"
);

if (!$productQuery || mysqli_num_rows($productQuery) === 0) {
    header("Location: retail.php");
    exit;
}

$product = mysqli_fetch_assoc($productQuery);

/* GALLERY IMAGES - primary image first, then any additional ones */
$galleryImagesQuery = mysqli_query(
    $conn,
    "SELECT image FROM product_images WHERE product_id='$product_id' ORDER BY display_order ASC, id ASC"
);

$productImages = [];

if (!empty($product['image'])) {
    $productImages[] = $product['image'];
}

while ($row = mysqli_fetch_assoc($galleryImagesQuery)) {
    $productImages[] = $row['image'];
}

if (empty($productImages)) {
    $productImages[] = $product['image'] ?? '';
}

/* REVIEWS */
$reviewQuery = mysqli_query(
    $conn,
    "SELECT rating, review, customer_name, DATE(created_at) AS review_date
     FROM product_reviews
     WHERE product_id='$product_id' AND status='Approved'
     ORDER BY id DESC"
);

$reviews = [];
$totalRating = 0;

while ($row = mysqli_fetch_assoc($reviewQuery)) {
    $reviews[] = $row;
    $totalRating += $row['rating'];
}

$averageRating = count($reviews) > 0 ? round($totalRating / count($reviews), 1) : 0;

/* RELATED PRODUCTS (same category, excluding this one) */
$categoryEsc = mysqli_real_escape_string($conn, $product['category']);
$relatedQuery = mysqli_query(
    $conn,
    "SELECT * FROM products
     WHERE category='$categoryEsc' AND id != '$product_id' AND status=1
     ORDER BY RAND()
     LIMIT 4"
);

$relatedProducts = [];
while ($row = mysqli_fetch_assoc($relatedQuery)) {
    $relatedProducts[] = $row;
}

/* PRICE CALCULATION */
$finalPrice = $product['price'];
if ($product['discount'] > 0) {
    $finalPrice = $product['price'] - ($product['price'] * $product['discount'] / 100);
}

$settingsQuery = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");
$settings = mysqli_fetch_assoc($settingsQuery);

if (!$settings) {
    $settings = [];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($product['name']); ?> – <?= htmlspecialchars($settings['website_name'] ?? 'Shreeji Decoration'); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/customer-login.css">

    <style>
        body {
            background: var(--cream);
        }

        .pd-hero {
            background: linear-gradient(135deg, #0D0800 0%, #2E1200 45%, #180A00 100%);
            padding: 14px 0;
            color: #fff;
        }

        .pd-breadcrumb {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            color: rgba(255,255,255,.55);
            font-size: .8rem;
            font-weight: 500;
        }

        .pd-breadcrumb a {
            color: rgba(255,255,255,.55);
            text-decoration: none;
        }

        .pd-breadcrumb a:hover {
            color: #B8863F;
        }

        .pd-breadcrumb .pd-crumb-current {
            color: #B8863F;
            font-weight: 600;
        }

        .pd-gallery {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
            position: relative;
        }

        .pd-gallery img {
            width: 100%;
            height: 460px;
            object-fit: cover;
            display: block;
        }

        .pd-thumb-strip {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .pd-thumb {
            width: 76px;
            height: 76px;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            transition: border-color .15s ease, transform .15s ease;
            flex-shrink: 0;
        }

        .pd-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pd-thumb:hover {
            transform: translateY(-2px);
        }

        .pd-thumb.active {
            border-color: var(--saffron);
        }

        .pd-badge {
            position: absolute;
            top: 18px;
            left: 18px;
            background: var(--saffron);
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 30px;
        }

        .pd-category {
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .78rem;
            color: var(--saffron-dark);
            font-weight: 700;
        }

        .pd-name {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            color: var(--dark);
            margin: 6px 0 14px;
        }

        .pd-rating-line {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .pd-price-old {
            text-decoration: line-through;
            color: #aaa;
            font-size: 1.1rem;
            margin-right: 10px;
        }

        .pd-price {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 2rem;
            color: var(--saffron-dark);
        }

        .pd-discount-chip {
            background: #e6f7ec;
            color: #1a7f3c;
            font-size: .78rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 30px;
            margin-left: 10px;
        }

        .pd-stock.in-stock {
            color: #1a7f3c;
        }

        .pd-stock.out-stock {
            color: #c0392b;
        }

        .pd-meta-row {
            display: flex;
            gap: 28px;
            flex-wrap: wrap;
            margin: 18px 0;
            font-size: .88rem;
            color: #6b6b6b;
        }

        .pd-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn-pd-cart {
            background: linear-gradient(135deg, #B8863F, #8F6428);
            color: #fff;
            border: none;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 700;
            font-size: .92rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all .2s ease;
        }

        .btn-pd-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(232, 119, 46, .35);
            color: #fff;
        }

        .pd-section-card {
            background: #fff;
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
            margin-top: 30px;
        }

        .pd-section-card h4 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .pd-section-card p {
            color: #6b6b6b;
            line-height: 1.85;
        }

        .review-card {
            border-bottom: 1px solid #f0ebe3;
            padding: 16px 0;
        }

        .review-card:last-child {
            border-bottom: none;
        }

        .review-stars {
            color: #E8A63A;
            font-size: .85rem;
        }

        .related-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .05);
            text-decoration: none;
            display: block;
            color: inherit;
            transition: transform .2s ease;
        }

        .related-card:hover {
            transform: translateY(-4px);
            color: inherit;
        }

        .related-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .related-card .rc-body {
            padding: 14px;
        }

        .related-card .rc-body h6 {
            font-weight: 700;
            margin-bottom: 4px;
            font-size: .92rem;
        }

        .related-card .rc-price {
            color: var(--saffron-dark);
            font-weight: 700;
        }

        @media (max-width: 767px) {
            .pd-gallery img {
                height: 300px;
            }

            .pd-section-card {
                padding: 22px;
            }
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>
    <?php include 'includes/login-modal.php'; ?>

    <!-- BACK BUTTON -->
    <section class="pd-hero">
        <div class="container" style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
            <a href="retail.php" id="pdBackBtn" style="display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.75);text-decoration:none;font-size:.85rem;font-weight:600;">
                <i class="bi bi-arrow-left"></i> Back
            </a>

            <span style="width:1px;height:16px;background:rgba(255,255,255,.2);"></span>

            <nav class="pd-breadcrumb" aria-label="breadcrumb">
                <a href="index.php">Home</a>
                <i class="bi bi-chevron-right" style="font-size:.65rem;"></i>
                <a href="retail.php"><?= htmlspecialchars($product['category']); ?></a>
                <i class="bi bi-chevron-right" style="font-size:.65rem;"></i>
                <span class="pd-crumb-current"><?= htmlspecialchars($product['name']); ?></span>
            </nav>
        </div>
    </section>

    <!-- MAIN PRODUCT -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5">

                <div class="col-lg-5">
                    <div class="pd-gallery">
                        <?php if (!empty($product['badge'])) { ?>
                            <span class="pd-badge"><?= htmlspecialchars($product['badge']); ?></span>
                        <?php } elseif ($product['discount'] > 0) { ?>
                            <span class="pd-badge"><?= (int) $product['discount']; ?>% OFF</span>
                        <?php } ?>
                        <img src="uploads/products/<?= htmlspecialchars($productImages[0]); ?>" alt="<?= htmlspecialchars($product['name']); ?>" id="pdMainImage">
                    </div>

                    <?php if (count($productImages) > 1) { ?>
                        <div class="pd-thumb-strip">
                            <?php foreach ($productImages as $i => $img) { ?>
                                <div class="pd-thumb <?= $i === 0 ? 'active' : ''; ?>" onclick="switchImage(this, 'uploads/products/<?= htmlspecialchars($img); ?>')">
                                    <img src="uploads/products/<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($product['name']); ?> photo <?= $i + 1; ?>">
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-lg-7">

                    <div class="pd-category"><?= htmlspecialchars($product['category']); ?></div>
                    <h1 class="pd-name"><?= htmlspecialchars($product['name']); ?></h1>

                    <div class="pd-rating-line">
                        <span class="review-stars">
                            <?php
                            $fullStars = floor($averageRating);
                            for ($s = 1; $s <= 5; $s++) {
                                echo $s <= $fullStars ? '★' : '☆';
                            }
                            ?>
                        </span>
                        <span class="text-muted small"><?= $averageRating; ?> (<?= count($reviews); ?> review<?= count($reviews) !== 1 ? 's' : ''; ?>)</span>
                    </div>

                    <div class="d-flex align-items-center flex-wrap mb-2">
                        <?php if ($product['discount'] > 0) { ?>
                            <span class="pd-price-old">₹<?= number_format($product['price'], 2); ?></span>
                        <?php } ?>
                        <span class="pd-price">₹<?= number_format($finalPrice, 2); ?></span>
                        <?php if ($product['discount'] > 0) { ?>
                            <span class="pd-discount-chip">Save <?= (int) $product['discount']; ?>%</span>
                        <?php } ?>
                    </div>

                    <div class="pd-stock <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock'; ?> fw-semibold mb-3">
                        <?php if ($product['stock'] > 0) { ?>
                            <i class="bi bi-check-circle-fill"></i> In Stock
                            <?php if ($product['stock'] <= 5) { ?>
                                <span class="text-muted">(only <?= (int) $product['stock']; ?> left)</span>
                            <?php } ?>
                        <?php } else { ?>
                            <i class="bi bi-x-circle-fill"></i> Out of Stock
                        <?php } ?>
                    </div>

                    <div class="pd-meta-row">
                        <div><i class="bi bi-receipt me-1"></i> GST: <?= htmlspecialchars($product['gst_rate']); ?>%</div>
                        <div><i class="bi bi-truck me-1"></i> Pan India Delivery</div>
                        <div><i class="bi bi-arrow-counterclockwise me-1"></i> <?= htmlspecialchars($settings['return_policy_days'] ?? '7'); ?>-Day Returns</div>
                    </div>

                    <div class="pd-actions">
                        <?php if ($product['stock'] > 0) { ?>
                            <button class="btn-pd-cart" id="pdAddCart">
                                <i class="bi bi-cart-plus-fill"></i> Add To Cart
                            </button>
                            <button class="btn-pd-cart" id="pdBuyNow" style="background:linear-gradient(135deg, #2d140a, #4a2413);">
                                <i class="bi bi-lightning-fill"></i> Buy Now
                            </button>
                        <?php } else { ?>
                            <button class="btn-pd-cart" disabled style="opacity:.5;cursor:not-allowed;">
                                <i class="bi bi-cart-x"></i> Out of Stock
                            </button>
                        <?php } ?>
                    </div>

                </div>
            </div>

            <!-- DESCRIPTION -->
            <div class="pd-section-card">
                <h4><i class="bi bi-file-text me-2"></i>Product Description</h4>
                <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <!-- REVIEWS -->
            <div class="pd-section-card">
                <h4><i class="bi bi-star me-2"></i>Customer Reviews</h4>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="text-center">
                        <div style="font-size:2.2rem;font-weight:700;color:var(--saffron-dark);font-family:'Playfair Display',serif;"><?= $averageRating; ?></div>
                        <div class="review-stars">
                            <?php for ($s = 1; $s <= 5; $s++) { echo $s <= $fullStars ? '★' : '☆'; } ?>
                        </div>
                        <div class="text-muted small"><?= count($reviews); ?> review<?= count($reviews) !== 1 ? 's' : ''; ?></div>
                    </div>
                </div>

                <?php if (count($reviews) === 0) { ?>
                    <div class="alert alert-light border">No reviews yet — be the first to review this product after your purchase.</div>
                <?php } else { ?>
                    <?php foreach ($reviews as $review) { ?>
                        <div class="review-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <strong><?= htmlspecialchars($review['customer_name']); ?></strong>
                                <small class="text-muted"><?= htmlspecialchars($review['review_date']); ?></small>
                            </div>
                            <div class="review-stars mb-1">
                                <?php for ($s = 1; $s <= 5; $s++) { echo $s <= $review['rating'] ? '★' : '☆'; } ?>
                            </div>
                            <p class="mb-0 text-muted small"><?= nl2br(htmlspecialchars($review['review'])); ?></p>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <!-- RELATED PRODUCTS -->
            <?php if (count($relatedProducts) > 0) { ?>
                <div class="pd-section-card">
                    <h4><i class="bi bi-grid me-2"></i>You May Also Like</h4>
                    <div class="row g-3">
                        <?php foreach ($relatedProducts as $rp) {
                            $rpFinalPrice = $rp['price'];
                            if ($rp['discount'] > 0) {
                                $rpFinalPrice = $rp['price'] - ($rp['price'] * $rp['discount'] / 100);
                            }
                        ?>
                            <div class="col-6 col-md-3">
                                <a href="product.php?id=<?= (int) $rp['id']; ?>" class="related-card">
                                    <img src="uploads/products/<?= htmlspecialchars($rp['image']); ?>" alt="<?= htmlspecialchars($rp['name']); ?>">
                                    <div class="rc-body">
                                        <h6><?= htmlspecialchars($rp['name']); ?></h6>
                                        <span class="rc-price">₹<?= number_format($rpFinalPrice, 2); ?></span>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>

    <script>
        /* ==========================================================
           SAFETY FALLBACK
           This page normally relies on includes/header.php to define
           window.cart / window.saveCart / window.renderCart. If for
           any reason that's missing (e.g. an older copy of header.php
           is still in place), set up a working fallback here so this
           page keeps functioning correctly on its own either way.
        ========================================================== */
        (function() {

            if (typeof window.cart !== 'undefined' && typeof window.saveCart === 'function') {
                return; // header.php already provided a working cart - nothing to do
            }

            const customerId =
                <?= isset($_SESSION['customer_id']) ? (int) $_SESSION['customer_id'] : 0 ?>;

            window.CART_KEY = window.CART_KEY || ("CART_KEY" + customerId);

            try {
                window.cart = JSON.parse(localStorage.getItem(window.CART_KEY)) || [];
            } catch (e) {
                window.cart = [];
            }

            window.saveCart = function() {
                localStorage.setItem(window.CART_KEY, JSON.stringify(window.cart));
            };

            window.renderCart = function() {
                let cartBubble = document.getElementById('cartBubble');
                let cartItemCount = document.getElementById('cartItemCount');
                let cartItemsEl = document.getElementById('cartItems');

                let total = 0;
                window.cart.forEach(item => total += item.qty);

                if (cartBubble) cartBubble.innerHTML = total;
                if (cartItemCount) cartItemCount.innerHTML = total + ' Items';

                if (cartItemsEl) {
                    if (window.cart.length === 0) {
                        cartItemsEl.innerHTML = '<div class="empty-cart"><i class="bi bi-cart-x"></i><p>Your cart is empty</p></div>';
                    } else {
                        let html = '';
                        window.cart.forEach((item, index) => {
                            html += `
                                <div class="cart-item-card">
                                    <div class="cart-item-image"><img src="uploads/products/${item.image}"></div>
                                    <div class="cart-item-details">
                                        <div class="cart-item-name">${item.name}</div>
                                        <div class="cart-item-price">₹${item.price}</div>
                                        <div class="cart-item-qty-control">
                                            <span>${item.qty}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        cartItemsEl.innerHTML = html;
                    }
                }
            };

            window.renderCart();

        })();

        function switchImage(thumbEl, src) {
            document.getElementById('pdMainImage').src = src;
            document.querySelectorAll('.pd-thumb').forEach(t => t.classList.remove('active'));
            thumbEl.classList.add('active');
        }

        /* ==========================================================
           ADD TO CART / BUY NOW
           Matches retail.php's own addToCart exactly: each click adds
           1 unit (or increments by 1 if it's already in the cart).
           Quantity itself is then adjusted from the cart/checkout
           page, not from this page - so this page and retail.php are
           always working with the exact same cart, the exact same way.
        ========================================================== */
        function pdAddToCart() {

            let productData = {
                id: <?= (int) $product['id']; ?>,
                name: <?= json_encode($product['name']); ?>,
                price: <?= (float) $finalPrice; ?>,
                image: <?= json_encode($product['image']); ?>,
                gst_rate: <?= (float) $product['gst_rate']; ?>
            };

            let existing = window.cart.find(item => item.id == productData.id);

            if (existing) {
                existing.qty += 1;
            } else {
                productData.qty = 1;
                window.cart.push(productData);
            }

            window.saveCart();
            window.renderCart();
        }

        document.addEventListener('DOMContentLoaded', function() {

            let backBtn = document.getElementById('pdBackBtn');
            if (backBtn) {
                backBtn.addEventListener('click', function(e) {
                    let cameFromSameSite =
                        document.referrer && document.referrer.indexOf(window.location.origin) === 0;

                    if (cameFromSameSite && window.history.length > 1) {
                        e.preventDefault();
                        window.history.back();
                    }
                    // otherwise, let the normal href="retail.php" take over
                });
            }

            let addCartBtn = document.getElementById('pdAddCart');
            let buyNowBtn = document.getElementById('pdBuyNow');

            if (addCartBtn) {
                addCartBtn.addEventListener('click', function() {

                    <?php if (!isset($_SESSION['customer_logged_in'])) { ?>
                        let loginBtnCart = document.getElementById('openLoginModal');
                        if (loginBtnCart) {
                            loginBtnCart.click();
                            return;
                        }
                    <?php } ?>

                    pdAddToCart();

                    let cartDropdown = document.getElementById('cartDropdown');
                    if (cartDropdown) {
                        cartDropdown.classList.add('show');
                    }

                    addCartBtn.innerHTML = '<i class="bi bi-check-lg"></i> Added to Cart';
                    setTimeout(function() {
                        addCartBtn.innerHTML = '<i class="bi bi-cart-plus-fill"></i> Add To Cart';
                    }, 1800);
                });
            }

            if (buyNowBtn) {
                buyNowBtn.addEventListener('click', function() {

                    <?php if (!isset($_SESSION['customer_logged_in'])) { ?>
                        let loginBtn = document.getElementById('openLoginModal');
                        if (loginBtn) {
                            loginBtn.click();
                            return;
                        }
                    <?php } ?>

                    pdAddToCart();
                    window.location.href = 'checkout.php';
                });
            }

        });
    </script>

</body>

</html>