<?php

session_start();

require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

/* FETCH SETTINGS */

$settingsQuery = mysqli_query(
    $conn,
    "SELECT * FROM settings LIMIT 1"
);

$settings = mysqli_fetch_assoc($settingsQuery);

/* UPDATE SETTINGS */

if (isset($_POST['save_settings'])) {

    $shop_latitude = $_POST['shop_latitude'];

    $shop_longitude = $_POST['shop_longitude'];

    $free_delivery_km = $_POST['free_delivery_km'];

    $free_delivery_charge = $_POST['free_delivery_charge'];

    $medium_delivery_km = $_POST['medium_delivery_km'];

    $medium_delivery_charge = $_POST['medium_delivery_charge'];

    $long_delivery_charge = $_POST['long_delivery_charge'];

    $free_delivery_order_amount =
        $_POST['free_delivery_order_amount'];

    $website_name = mysqli_real_escape_string(
        $conn,
        $_POST['website_name']
    );

    $phone = mysqli_real_escape_string(
        $conn,
        $_POST['phone']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $address = mysqli_real_escape_string(
        $conn,
        $_POST['address']
    );

    $instagram = mysqli_real_escape_string(
        $conn,
        $_POST['instagram']
    );

    $facebook = mysqli_real_escape_string(
        $conn,
        $_POST['facebook']
    );

    $whatsapp = mysqli_real_escape_string(
        $conn,
        $_POST['whatsapp']
    );

    $map_link = mysqli_real_escape_string(
        $conn,
        $_POST['map_link']
    );

    $hero_title = mysqli_real_escape_string(
        $conn,
        $_POST['hero_title']
    );

    $hero_subtitle = mysqli_real_escape_string(
        $conn,
        $_POST['hero_subtitle']
    );

    $smtp_host = mysqli_real_escape_string(
        $conn,
        $_POST['smtp_host']
    );

    $smtp_port = mysqli_real_escape_string(
        $conn,
        $_POST['smtp_port']
    );

    $smtp_email = mysqli_real_escape_string(
        $conn,
        $_POST['smtp_email']
    );

    $smtp_password = mysqli_real_escape_string(
        $conn,
        $_POST['smtp_password']
    );

    /* ---- Razorpay ---- */

    $razorpay_enabled = isset($_POST['razorpay_enabled']) ? 1 : 0;

    $razorpay_key_id = mysqli_real_escape_string(
        $conn,
        trim($_POST['razorpay_key_id'] ?? '')
    );

    $razorpay_key_secret = mysqli_real_escape_string(
        $conn,
        trim($_POST['razorpay_key_secret'] ?? '')
    );

    $razorpay_webhook_secret = mysqli_real_escape_string(
        $conn,
        trim($_POST['razorpay_webhook_secret'] ?? '')
    );

    $razorpay_mode = (strpos($razorpay_key_id, 'rzp_live_') === 0) ? 'live' : 'test';

    /* FOOTER CONTENT */

    $footer_tagline = mysqli_real_escape_string($conn, $_POST['footer_tagline'] ?? '');
    $footer_text = mysqli_real_escape_string($conn, $_POST['footer_text'] ?? '');
    $footer_newsletter_heading = mysqli_real_escape_string($conn, $_POST['footer_newsletter_heading'] ?? '');
    $footer_newsletter_text = mysqli_real_escape_string($conn, $_POST['footer_newsletter_text'] ?? '');
    $footer_payment_methods = mysqli_real_escape_string($conn, $_POST['footer_payment_methods'] ?? '');

    /* FOOTER LOGO UPLOAD */

    $footer_logo = $settings['footer_logo'] ?? '';

    if (!empty($_FILES['footer_logo']['name'])) {

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        $fileExt = strtolower(pathinfo($_FILES['footer_logo']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExt)) {

            $uploadError = "Footer logo must be an image file (JPG, PNG, WEBP, GIF, or SVG).";

        } elseif ($_FILES['footer_logo']['size'] > 2 * 1024 * 1024) {

            $uploadError = "Footer logo must be under 2 MB.";

        } else {

            $newLogoFile = 'footer_logo_' . time() . '.' . $fileExt;

            $logoDir = __DIR__ . '/../uploads/site-content/';

            if (!is_dir($logoDir)) {
                @mkdir($logoDir, 0755, true);
            }

            $uploadPath = $logoDir . $newLogoFile;

            if (!is_dir($logoDir) || !is_writable($logoDir)) {

                $uploadError = "The uploads/site-content folder is missing or not writable on the server. Please create it manually and try again.";

            } elseif (move_uploaded_file($_FILES['footer_logo']['tmp_name'], $uploadPath)) {

                if (!empty($footer_logo) && file_exists($logoDir . $footer_logo)) {
                    unlink($logoDir . $footer_logo);
                }

                $footer_logo = $newLogoFile;

            } else {

                $uploadError = "Unable to upload the footer logo. Please check folder permissions.";

            }
        }
    }

    $footer_logo_esc = mysqli_real_escape_string($conn, $footer_logo);

    /* SITE LOGO UPLOAD (header + footer brand icon across the site) */

    $logo = $settings['logo'] ?? '';

    if (!empty($_FILES['logo']['name'])) {

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        $fileExt = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExt)) {

            $uploadError = "Logo must be an image file (JPG, PNG, WEBP, GIF, or SVG).";

        } elseif ($_FILES['logo']['size'] > 2 * 1024 * 1024) {

            $uploadError = "Logo must be under 2 MB.";

        } else {

            $newLogoFile = 'logo_' . time() . '.' . $fileExt;

            $logoDir = __DIR__ . '/../uploads/site-content/';

            if (!is_dir($logoDir)) {
                @mkdir($logoDir, 0755, true);
            }

            $uploadPath = $logoDir . $newLogoFile;

            if (!is_dir($logoDir) || !is_writable($logoDir)) {

                $uploadError = "The uploads/site-content folder is missing or not writable on the server. Please create it manually and try again.";

            } elseif (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {

                if (!empty($logo) && file_exists($logoDir . $logo)) {
                    unlink($logoDir . $logo);
                }

                $logo = $newLogoFile;

            } else {

                $uploadError = "Unable to upload the logo. Please check folder permissions.";

            }
        }
    }

    $logo_esc = mysqli_real_escape_string($conn, $logo);

    $return_policy_days = (int)$_POST['return_policy_days'];

    mysqli_query(
        $conn,

        "UPDATE settings SET
        

    website_name='$website_name',

    phone='$phone',

    email='$email',

    address='$address',

    instagram='$instagram',

    facebook='$facebook',

    whatsapp='$whatsapp',

    map_link='$map_link',

    hero_title='$hero_title',

    hero_subtitle='$hero_subtitle',
    shop_latitude='$shop_latitude',
shop_longitude='$shop_longitude',

free_delivery_km='$free_delivery_km',
free_delivery_charge='$free_delivery_charge',

medium_delivery_km='$medium_delivery_km',
medium_delivery_charge='$medium_delivery_charge',

long_delivery_charge='$long_delivery_charge',

free_delivery_order_amount='$free_delivery_order_amount',

return_policy_days='$return_policy_days',

smtp_host='$smtp_host',

smtp_port='$smtp_port',

smtp_email='$smtp_email',

smtp_password='$smtp_password',

razorpay_enabled='$razorpay_enabled',

razorpay_mode='$razorpay_mode',

razorpay_key_id='$razorpay_key_id',

razorpay_key_secret='$razorpay_key_secret',

razorpay_webhook_secret='$razorpay_webhook_secret',

footer_tagline='$footer_tagline',

footer_text='$footer_text',

footer_logo='$footer_logo_esc',

logo='$logo_esc',

footer_newsletter_heading='$footer_newsletter_heading',

footer_newsletter_text='$footer_newsletter_text',

footer_payment_methods='$footer_payment_methods'

WHERE id=1"

    );

    if (!empty($uploadError)) {
        header("Location: settings.php?upload_error=" . urlencode($uploadError));
        exit;
    }

    header("Location: settings.php?success=1");
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Settings</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #f8f5f2;
            font-family: Poppins, sans-serif;
        }

        /* sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 270px;
            height: 100vh;
            background: #2d140a;
            padding: 30px;
            color: #fff;

            overflow-y: auto;
            overflow-x: hidden;

            scrollbar-width: thin;
            scrollbar-color: #7A2E0D #2d140a;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 40px;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
            padding: 14px 18px;
            border-radius: 14px;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #7A2E0D;
        }

        /* main */
        .main-content {
            margin-left: 270px;
            padding: 40px;
        }

        /* card */
        .settings-card {
            background: #fff;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        /* input */
        .form-label {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-control {
            height: 54px;
            border-radius: 14px;
            border: 1px solid #ede8e3;
            background: #f8f5f2;
        }

        textarea.form-control {
            height: 120px;
            resize: none;
        }

        .save-btn {
            background: #2d140a;
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 14px;
            font-weight: 600;
        }

        .save-btn:hover {
            background: #7A2E0D;
        }

        .success-alert {
            background: #DDEFE2;
            color: #2F6B3E;
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            font-weight: 600;
        }
    </style>

</head>

<body>

    <!-- SIDEBAR -->

    <?php include 'includes/sidebar.php'; ?>

    <!-- MAIN -->

    <div class="main-content">

        <h2 class="fw-bold mb-4">
            Website Settings
        </h2>

        <?php if (isset($_GET['success'])) { ?>

            <div class="success-alert">

                <i class="bi bi-check-circle-fill"></i>

                Settings Updated Successfully

            </div>

        <?php } ?>

        <?php if (isset($_GET['upload_error'])) { ?>

            <div class="alert alert-danger">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                <?= htmlspecialchars($_GET['upload_error']); ?>

            </div>

        <?php } ?>

        <div class="settings-card">

            <form method="POST" enctype="multipart/form-data">

                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Website Name
                        </label>

                        <input type="text"
                            name="website_name"
                            class="form-control"

                            value="<?= $settings['website_name']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Phone Number
                        </label>

                        <input type="text"
                            name="phone"
                            class="form-control"

                            value="<?= $settings['phone']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Email
                        </label>

                        <input type="email"
                            name="email"
                            class="form-control"

                            value="<?= $settings['email']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            WhatsApp
                        </label>

                        <input type="text"
                            name="whatsapp"
                            class="form-control"

                            value="<?= $settings['whatsapp']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Instagram Link
                        </label>

                        <input type="text"
                            name="instagram"
                            class="form-control"

                            value="<?= $settings['instagram']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Facebook Link
                        </label>

                        <input type="text"
                            name="facebook"
                            class="form-control"

                            value="<?= $settings['facebook']; ?>">

                    </div>

                    <div class="col-md-12 mb-4">

                        <label class="form-label">
                            Google Maps Link
                        </label>

                        <input type="text"
                            name="map_link"
                            class="form-control"

                            value="<?= $settings['map_link']; ?>">

                    </div>

                    <div class="col-md-12 mb-4">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea name="address"
                            class="form-control"><?= $settings['address']; ?></textarea>

                    </div>

                    <div class="col-md-12 mb-4">

                        <label class="form-label">
                            Hero Title
                        </label>

                        <textarea name="hero_title"
                            class="form-control"><?= $settings['hero_title']; ?></textarea>

                    </div>

                    <div class="col-md-12 mb-4">

                        <label class="form-label">
                            Hero Subtitle
                        </label>

                        <textarea name="hero_subtitle"
                            class="form-control"><?= $settings['hero_subtitle']; ?></textarea>

                    </div>
                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Shop Latitude
                        </label>

                        <input
                            type="text"
                            name="shop_latitude"
                            class="form-control"
                            value="<?= $settings['shop_latitude']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Shop Longitude
                        </label>

                        <input
                            type="text"
                            name="shop_longitude"
                            class="form-control"
                            value="<?= $settings['shop_longitude']; ?>">

                    </div>

                    <div class="col-md-4 mb-4">

                        <label class="form-label">
                            Free Delivery KM
                        </label>

                        <input
                            type="number"
                            name="free_delivery_km"
                            class="form-control"
                            value="<?= $settings['free_delivery_km']; ?>">

                    </div>

                    <div class="col-md-4 mb-4">

                        <label class="form-label">
                            Free Delivery Charge
                        </label>

                        <input
                            type="number"
                            name="free_delivery_charge"
                            class="form-control"
                            value="<?= $settings['free_delivery_charge']; ?>">

                    </div>

                    <div class="col-md-4 mb-4">

                        <label class="form-label">
                            Medium Distance KM
                        </label>

                        <input
                            type="number"
                            name="medium_delivery_km"
                            class="form-control"
                            value="<?= $settings['medium_delivery_km']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Medium Delivery Charge
                        </label>

                        <input
                            type="number"
                            name="medium_delivery_charge"
                            class="form-control"
                            value="<?= $settings['medium_delivery_charge']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Long Distance Charge
                        </label>

                        <input
                            type="number"
                            name="long_delivery_charge"
                            class="form-control"
                            value="<?= $settings['long_delivery_charge']; ?>">

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label">
                            Free Delivery Above Order Amount
                        </label>

                        <input
                            type="number"
                            name="free_delivery_order_amount"
                            class="form-control"
                            value="<?= $settings['free_delivery_order_amount']; ?>">

                    </div>






                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            Return Policy Days
                        </label>

                        <input
                            type="number"
                            name="return_policy_days"
                            class="form-control"
                            value="<?= $settings['return_policy_days']; ?>">
                    </div>

                </div>

                <div class="mb-3">
                    <label>SMTP Host</label>
                    <input type="text"
                        name="smtp_host"
                        class="form-control"
                        value="<?= $settings['smtp_host']  ?>">
                </div>

                <div class="mb-3">
                    <label>SMTP Port</label>
                    <input type="text"
                        name="smtp_port"
                        class="form-control"
                        value="<?= $settings['smtp_port']  ?>">
                </div>

                <div class="mb-3">
                    <label>SMTP Email</label>
                    <input type="text"
                        name="smtp_email"
                        class="form-control"
                        value="<?= $settings['smtp_email']  ?>">
                </div>

                <div class="mb-3">
                    <label>SMTP Password / Brevo API Key</label>
                    <input type="password"
                        name="smtp_password"
                        class="form-control"
                        value="<?= $settings['smtp_password']  ?>">
                    <div class="form-text">
                        Paste a <strong>Brevo API key</strong> here (it starts with
                        <code>xkeysib-</code>) and the site sends all email over HTTPS
                        instead of SMTP. Use this on live hosting &mdash; most hosts block
                        the SMTP ports, which is why Gmail SMTP works on localhost but not
                        on the server.
                        <a href="mail-test.php" target="_blank">Open Email Diagnostics &rarr;</a>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Online Payment (Razorpay)</h5>
                <p class="text-muted small mb-3">
                    Get these from your Razorpay Dashboard &rarr; <strong>Account &amp; Settings
                    &rarr; API Keys</strong>. Keys starting <code>rzp_test_</code> take fake
                    payments for testing; <code>rzp_live_</code> takes real money and only works
                    after your KYC is approved.
                </p>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input"
                        type="checkbox"
                        name="razorpay_enabled"
                        id="razorpay_enabled"
                        value="1"
                        <?= !empty($settings['razorpay_enabled']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="razorpay_enabled">
                        Show "Pay Online" at checkout
                    </label>
                    <div class="form-text">
                        Turn this off and the site falls back to Cash On Delivery only.
                    </div>
                </div>

                <div class="mb-3">
                    <label>Razorpay Key ID</label>
                    <input type="text"
                        name="razorpay_key_id"
                        class="form-control"
                        placeholder="rzp_test_xxxxxxxxxxxxxx"
                        value="<?= htmlspecialchars($settings['razorpay_key_id'] ?? '') ?>">
                    <div class="form-text">
                        This one is public &mdash; it appears in the payment popup. That's normal.
                    </div>
                </div>

                <div class="mb-3">
                    <label>Razorpay Key Secret</label>
                    <input type="password"
                        name="razorpay_key_secret"
                        class="form-control"
                        value="<?= htmlspecialchars($settings['razorpay_key_secret'] ?? '') ?>">
                    <div class="form-text text-danger">
                        Never share this or put it in any HTML/JavaScript. It is what proves a
                        payment is genuine. Razorpay shows it only once when you generate the
                        key &mdash; if you lost it, generate a new key pair.
                    </div>
                </div>

                <div class="mb-3">
                    <label>Razorpay Webhook Secret</label>
                    <input type="password"
                        name="razorpay_webhook_secret"
                        class="form-control"
                        value="<?= htmlspecialchars($settings['razorpay_webhook_secret'] ?? '') ?>">
                    <div class="form-text">
                        Invent any random string, then paste the same one into Razorpay
                        Dashboard &rarr; Settings &rarr; Webhooks. Point the webhook at
                        <code>https://<?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'yourdomain.com') ?>/api/razorpay-webhook.php</code>
                        and subscribe to <code>payment.captured</code> and <code>payment.failed</code>.
                        Without this, orders can get stuck on "Awaiting Payment" when a customer
                        closes the browser right after paying.
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Site Logo</h5>
                <p class="text-muted small mb-3">
                    Shown in the header navigation bar and every footer across the site.
                </p>

                <?php if (!empty($settings['logo'])) { ?>
                    <div class="mb-3">
                        <label>Current Logo</label>
                        <div>
                            <img src="../uploads/site-content/<?= htmlspecialchars($settings['logo']); ?>"
                                style="max-height:80px;border-radius:8px;">
                        </div>
                    </div>
                <?php } ?>

                <div class="mb-3">
                    <label>Upload New Logo (image, max 2MB)</label>
                    <input type="file"
                        name="logo"
                        class="form-control"
                        accept="image/*">
                    <div class="form-hint mt-1">Leave empty to keep the current logo.</div>
                </div>

                <hr class="my-4">
                <h5 class="mb-3">Footer Content</h5>
                <p class="text-muted small mb-3">
                    Controls the logo, tagline, description, newsletter text, and payment badges shown
                    in the footer on every page.
                    <br>
                    <strong>Note:</strong> the "Quick Links" and "Occasions" columns in the footer are not
                    edited here — Quick Links always mirror your site's real pages, and Occasions is
                    pulled automatically from Admin &rarr; Categories, so it always stays in sync with
                    the categories you manage there.
                </p>

                <?php if (!empty($settings['footer_logo'])) { ?>
                    <div class="mb-3">
                        <label>Current Footer Logo</label>
                        <div>
                            <img src="../uploads/site-content/<?= htmlspecialchars($settings['footer_logo']); ?>"
                                style="max-height:60px;border-radius:8px;">
                        </div>
                    </div>
                <?php } ?>

                <div class="mb-3">
                    <label>Upload New Footer Logo (image, max 2MB)</label>
                    <input type="file"
                        name="footer_logo"
                        class="form-control"
                        accept="image/*">
                    <div class="form-hint mt-1">Leave empty to keep the current logo icon.</div>
                </div>

                <div class="mb-3">
                    <label>Footer Tagline</label>
                    <input type="text"
                        name="footer_tagline"
                        class="form-control"
                        value="<?= htmlspecialchars($settings['footer_tagline'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label>Footer Description</label>
                    <textarea
                        name="footer_text"
                        rows="3"
                        class="form-control"><?= htmlspecialchars($settings['footer_text'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Newsletter Heading</label>
                        <input type="text"
                            name="footer_newsletter_heading"
                            class="form-control"
                            value="<?= htmlspecialchars($settings['footer_newsletter_heading'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Newsletter Description</label>
                        <input type="text"
                            name="footer_newsletter_text"
                            class="form-control"
                            value="<?= htmlspecialchars($settings['footer_newsletter_text'] ?? '') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Payment Methods</label>
                    <input type="text"
                        name="footer_payment_methods"
                        class="form-control"
                        value="<?= htmlspecialchars($settings['footer_payment_methods'] ?? '') ?>">
                    <div class="form-hint mt-1">Comma-separated, e.g. UPI,NetBanking,Cards,COD,EMI</div>
                </div>

                <button type="submit"
                    name="save_settings"
                    class="save-btn">

                    <i class="bi bi-check-lg"></i>

                    Save Settings

                </button>

            </form>

        </div>

    </div>

</body>

</html>