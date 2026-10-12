<?php

require_once 'includes/session.php';
require_once 'config/db.php';

$policyQuery = mysqli_query($conn, "
    SELECT *
    FROM site_content
    WHERE page_name='return'
    LIMIT 1
");

$policy = mysqli_fetch_assoc($policyQuery);

if (!$policy || empty($policy['content'])) {
    $policy = [
        'title'    => 'Refund & Return Policy',
        'subtitle' => 'How refunds, returns, and cancellations work',
        'content'  => "Return Eligibility

Most products are eligible for return within our standard return window from the date of delivery, provided the item is unused, undamaged, and in its original packaging.

How to Request a Return

To request a return, please use the Return Product option on our website or contact us directly with your order ID and the reason for the return.

Non-Returnable Items

Customised, made-to-order, or personalised decoration items are not eligible for return unless they arrive damaged or defective.

Refunds

Once your returned item is received and inspected, we will notify you of the approval or rejection of your refund. Approved refunds are processed back to your original payment method, or as store credit for Cash on Delivery orders, within a reasonable timeframe.

Damaged or Incorrect Items

If you receive a damaged, defective, or incorrect item, please contact us within 48 hours of delivery with photos of the product, and we will arrange a replacement or refund at no extra cost to you.

Cancellations

Orders can be cancelled before they are dispatched. Once an order has been shipped, it can no longer be cancelled and must instead follow the standard return process.

Delivery Charges

Delivery charges, where applicable, are non-refundable except in cases where the return is due to our error (wrong or damaged item).

Contact Us

If you have any questions about returns or refunds, please reach out to us using the contact details on our website.",
    ];
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
    <title><?= htmlspecialchars($policy['title'] ?? 'Refund & Return Policy', ENT_QUOTES, 'UTF-8'); ?> – <?= htmlspecialchars($settings['website_name'] ?? 'Shreeji Decoration'); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/customer-login.css">

    <style>
        body {
            background: var(--cream);
        }

        .policy-hero {
            background: linear-gradient(135deg, #0D0800 0%, #2E1200 45%, #180A00 100%);
            padding: 80px 0 60px;
            color: #fff;
        }

        .policy-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 4.5vw, 2.9rem);
            font-weight: 700;
        }

        .policy-hero .breadcrumb-txt a {
            color: rgba(255, 255, 255, .5);
            text-decoration: none;
        }

        .policy-hero .breadcrumb-txt span.sep {
            color: rgba(255, 255, 255, .25);
            margin: 0 8px;
        }

        .policy-hero .breadcrumb-txt strong {
            color: #C9A063;
            font-weight: 500;
        }

        .policy-card {
            background: #fff;
            border-radius: 20px;
            padding: 45px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, .06);
        }

        .policy-card h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--dark);
            margin-top: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .policy-card h5:first-child {
            margin-top: 0;
        }

        .policy-card h5 i {
            color: var(--saffron);
            font-size: .95rem;
        }

        .policy-card p {
            color: #6b6b6b;
            line-height: 1.8;
            font-size: .95rem;
        }

        .policy-updated {
            font-size: .8rem;
            color: #999;
        }

        @media (max-width: 767px) {
            .policy-card {
                padding: 28px 20px;
            }
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>
    <?php include 'includes/login-modal.php'; ?>

    <!-- HERO -->
    <section class="policy-hero">
        <div class="container">
            <nav class="breadcrumb-txt mb-2" style="font-size:.8rem;">
                <a href="index.php">Home</a>
                <span class="sep">/</span>
                <strong>Refund & Return Policy</strong>
            </nav>
            <h1><?= htmlspecialchars($policy['title'] ?? 'Refund & Return Policy', ENT_QUOTES, 'UTF-8'); ?></h1>
            <?php if (!empty($policy['subtitle'])) { ?>
                <p style="color:rgba(255,255,255,.6);max-width:560px;">
                    <?= htmlspecialchars($policy['subtitle'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php } ?>
        </div>
    </section>

    <!-- POLICY CONTENT -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="policy-card">

                        <p class="policy-updated mb-4">
                            <i class="bi bi-clock-history me-1"></i>
                            Last updated: <?= date('F Y'); ?>
                        </p>

                        <?php
                        $content = trim($policy['content'] ?? '');

                        if ($content !== '') {
                            $paragraphs = preg_split('/\R\s*\R/', $content);

                            foreach ($paragraphs as $paragraph) {
                                $paragraph = trim($paragraph);

                                if ($paragraph === '') {
                                    continue;
                                }

                                /* Treat short, single-line paragraphs without
                                   trailing punctuation as section headings. */
                                $isHeading =
                                    (strpos($paragraph, "\n") === false) &&
                                    (strlen($paragraph) <= 60) &&
                                    !preg_match('/[.!?]$/', $paragraph);

                                if ($isHeading) {
                        ?>
                                    <h5><i class="bi bi-arrow-return-left"></i> <?= htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8'); ?></h5>
                        <?php
                                } else {
                        ?>
                                    <p><?= nl2br(htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8')); ?></p>
                        <?php
                                }
                            }
                        } else {
                        ?>
                            <p class="text-muted">Our refund & return policy are being updated. Please check back soon, or contact us directly with any questions.</p>
                        <?php } ?>

                    </div>

                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-outline-dark rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Back to Home
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>