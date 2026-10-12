<?php

require_once 'includes/session.php';
require_once 'config/db.php';

/* ABOUT CONTENT */
$aboutQuery = mysqli_query($conn, "
    SELECT *
    FROM site_content
    WHERE page_name='about'
    LIMIT 1
");

$about = mysqli_fetch_assoc($aboutQuery);

if (!$about) {
    $about = [
        'title'      => 'Crafting Memories with',
        'subtitle'   => 'Indian Artistry',
        'content'    => "Shreeji Decoration was founded with a simple belief — every celebration deserves to look extraordinary.\n\nFrom the bustling streets of Ahmedabad, we have grown to serve thousands of families, event planners, and businesses across India.",
        'since_year' => '2009',
        'image'      => '',
        'image2'     => '',
    ];
}

/* FEATURES */
$featuresQuery = mysqli_query($conn, "SELECT * FROM about_features ORDER BY id ASC");

/* SETTINGS */
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
    <title>About Us – <?= htmlspecialchars($settings['website_name'] ?? 'Shreeji Decoration'); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/customer-login.css">

    <style>
        body {
            background: var(--cream);
        }

        /* PAGE HERO */
        .about-hero {
            background: linear-gradient(135deg, #0D0800 0%, #2E1200 45%, #180A00 100%);
            padding: 90px 0 70px;
            position: relative;
            overflow: hidden;
            color: #fff;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 65% 75% at 85% 45%, rgba(200, 167, 106, .08), transparent 60%);
        }

        .about-hero .container {
            position: relative;
            z-index: 1;
        }

        .about-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.1rem, 5vw, 3.2rem);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .about-hero .breadcrumb-txt a {
            color: rgba(255, 255, 255, .5);
            text-decoration: none;
        }

        .about-hero .breadcrumb-txt span.sep {
            color: rgba(255, 255, 255, .25);
            margin: 0 8px;
        }

        .about-hero .breadcrumb-txt strong {
            color: #C9A063;
            font-weight: 500;
        }

        /* ABOUT MAIN SECTION */
        .about-img-wrap {
            position: relative;
        }

        .about-img-main {
            width: 100%;
            height: 380px;
            border-radius: 20px;
            background-size: cover;
            background-position: center;
            background-color: #eee2cf;
            box-shadow: 0 20px 45px rgba(0, 0, 0, .12);
        }

        .about-badge-year {
            position: absolute;
            bottom: -20px;
            right: -20px;
            background: var(--saffron);
            color: #fff;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(232, 119, 46, .35);
            font-size: 13px;
        }

        .about-badge-year small {
            font-size: 10px;
            font-weight: 400;
            opacity: .85;
        }

        .feature-tile {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            height: 100%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .05);
            border: 1px solid rgba(0, 0, 0, .04);
        }

        .feature-tile i {
            font-size: 1.7rem;
            color: var(--saffron);
            margin-bottom: 10px;
            display: block;
        }

        .feature-tile h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .feature-tile p {
            font-size: .85rem;
            color: #6b6b6b;
            margin: 0;
        }

        .stats-strip {
            background: var(--dark);
            border-radius: 20px;
            padding: 40px 20px;
            color: #fff;
        }

        .stats-strip .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--gold);
        }

        .stats-strip .stat-label {
            font-size: .8rem;
            color: rgba(255, 255, 255, .6);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cta-strip {
            background: linear-gradient(135deg, var(--saffron), var(--saffron-dark));
            border-radius: 20px;
            padding: 45px 30px;
            color: #fff;
            text-align: center;
        }

        .cta-strip .btn-light-cta {
            background: #fff;
            color: var(--saffron-dark);
            font-weight: 700;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin: 6px;
        }

        .cta-strip .btn-outline-cta {
            border: 1px solid rgba(255, 255, 255, .6);
            color: #fff;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin: 6px;
        }
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>
    <?php include 'includes/login-modal.php'; ?>

    <!-- HERO -->
    <section class="about-hero">
        <div class="container">
            <nav class="breadcrumb-txt mb-2" style="font-size:.8rem;">
                <a href="index.php">Home</a>
                <span class="sep">/</span>
                <strong>About Us</strong>
            </nav>
            <h1><?= nl2br(htmlspecialchars($about['title'] ?? 'About Us', ENT_QUOTES, 'UTF-8')); ?>
                <?php if (!empty($about['subtitle'])) { ?>
                    <br><em style="color:#C9A063;font-style:italic;"><?= htmlspecialchars($about['subtitle'], ENT_QUOTES, 'UTF-8'); ?></em>
                <?php } ?>
            </h1>
            <p style="color:rgba(255,255,255,.6);max-width:560px;">
                Get to know the story, the people, and the passion behind every decoration we create.
            </p>
        </div>
    </section>

    <!-- MAIN ABOUT CONTENT -->
    <section class="py-5">
        <div class="container py-4">
            <div class="row align-items-center g-5">

                <div class="col-lg-5">
                    <div class="about-img-wrap">
                        <?php if (!empty($about['image'])) { ?>
                            <div class="about-img-main" style="background-image:url('uploads/site-content/<?= htmlspecialchars($about['image'], ENT_QUOTES, 'UTF-8'); ?>');"></div>
                        <?php } else { ?>
                            <div class="about-img-main d-flex align-items-center justify-content-center">
                                <i class="bi bi-flower1" style="font-size:4rem;color:#C9A063;"></i>
                            </div>
                        <?php } ?>
                        <div class="about-badge-year">
                            <small>Since</small>
                            <?= htmlspecialchars($about['since_year'] ?? '2009', ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="section-label">Our Story</div>
                    <h2 class="section-title mb-3">Who We Are</h2>

                    <div class="text-muted">
                        <?php
                        $aboutContent = trim($about['content'] ?? '');

                        if ($aboutContent !== '') {
                            $paragraphs = preg_split('/\R\s*\R/', $aboutContent);

                            foreach ($paragraphs as $paragraph) {
                                if (trim($paragraph) === '') {
                                    continue;
                                }
                        ?>
                                <p class="text-muted"><?= nl2br(htmlspecialchars(trim($paragraph), ENT_QUOTES, 'UTF-8')); ?></p>
                        <?php
                            }
                        }
                        ?>
                    </div>

                    <div class="row g-3 mt-2">
                        <?php
                        if ($featuresQuery && mysqli_num_rows($featuresQuery) > 0) {
                            while ($feat = mysqli_fetch_assoc($featuresQuery)) {
                        ?>
                                <div class="col-md-6">
                                    <div class="feature-tile">
                                        <i class="bi <?= htmlspecialchars($feat['icon'] ?: 'bi-star-fill', ENT_QUOTES, 'UTF-8'); ?>"></i>
                                        <h6><?= htmlspecialchars($feat['title'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                        <p><?= htmlspecialchars($feat['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="py-4">
        <div class="container">
            <div class="stats-strip">
                <div class="row text-center g-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-num"><?= htmlspecialchars($settings['total_products'] ?? '200+', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="stat-label">Products</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-num"><?= htmlspecialchars($settings['happy_customers'] ?? '5000+', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-num"><?= htmlspecialchars($settings['avg_rating'] ?? '4.9★', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-num"><?= htmlspecialchars($about['since_year'] ?? '2009', ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="stat-label">Established</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5">
        <div class="container">
            <div class="cta-strip">
                <h3 style="font-family:'Playfair Display',serif;font-weight:700;">Ready to plan your celebration?</h3>
                <p class="mb-3" style="opacity:.9;">Explore our retail collection or get in touch for a custom quote.</p>
                <a href="retail.php" class="btn-light-cta">Shop Retail</a>
                <a href="index.php#contact" class="btn-outline-cta">Get a Quote</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>

</html>