<?php

require_once 'includes/session.php';
require_once 'config/db.php';

$faqQuery = mysqli_query($conn, "
    SELECT *
    FROM faq
    WHERE status = 1
    ORDER BY display_order ASC, id ASC
");

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
    <title>FAQs – <?= htmlspecialchars($settings['website_name'] ?? 'Shreeji Decoration'); ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/customer-login.css">

    <style>
        body {
            background: var(--cream);
        }

        .faq-hero {
            background: linear-gradient(135deg, #0D0800 0%, #2E1200 45%, #180A00 100%);
            padding: 90px 0 70px;
            color: #fff;
        }

        .faq-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.1rem, 5vw, 3.2rem);
            font-weight: 700;
        }

        .faq-hero .breadcrumb-txt a {
            color: rgba(255, 255, 255, .5);
            text-decoration: none;
        }

        .faq-hero .breadcrumb-txt span.sep {
            color: rgba(255, 255, 255, .25);
            margin: 0 8px;
        }

        .faq-hero .breadcrumb-txt strong {
            color: #C9A063;
            font-weight: 500;
        }

        .faq-search {
            max-width: 520px;
            margin-top: 22px;
        }

        .faq-search input {
            border-radius: 50px;
            padding: 13px 22px;
            border: none;
        }

        .accordion-item {
            border: none;
            border-radius: 14px !important;
            margin-bottom: 14px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .05);
        }

        .accordion-button {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--dark);
        }

        .accordion-button:not(.collapsed) {
            background: #fff5ef;
            color: var(--saffron-dark);
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .faq-empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .cta-strip {
            background: linear-gradient(135deg, var(--saffron), var(--saffron-dark));
            border-radius: 20px;
            padding: 40px 30px;
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
    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>
    <?php include 'includes/login-modal.php'; ?>

    <!-- HERO -->
    <section class="faq-hero">
        <div class="container">
            <nav class="breadcrumb-txt mb-2" style="font-size:.8rem;">
                <a href="index.php">Home</a>
                <span class="sep">/</span>
                <strong>FAQs</strong>
            </nav>
            <h1>Frequently Asked <em style="color:#C9A063;font-style:italic;">Questions</em></h1>
            <p style="color:rgba(255,255,255,.6);max-width:560px;">
                Answers to the questions we hear most from our retail and wholesale customers.
            </p>

            <div class="faq-search">
                <input type="text" id="faqSearchInput" class="form-control" placeholder="Search a question...">
            </div>
        </div>
    </section>

    <!-- FAQ LIST -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <?php if ($faqQuery && mysqli_num_rows($faqQuery) > 0) { ?>

                        <div class="accordion" id="faqAccordion">
                            <?php
                            $i = 0;
                            while ($faq = mysqli_fetch_assoc($faqQuery)) {
                                $i++;
                                $collapseId = 'faqItem' . $i;
                            ?>
                                <div class="accordion-item faq-item" data-question="<?= htmlspecialchars(strtolower($faq['question']), ENT_QUOTES, 'UTF-8'); ?>">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button <?= $i === 1 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId; ?>">
                                            <?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8'); ?>
                                        </button>
                                    </h2>
                                    <div id="<?= $collapseId; ?>" class="accordion-collapse collapse <?= $i === 1 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted">
                                            <?= nl2br(htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8')); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                    <?php } else { ?>

                        <div class="faq-empty">
                            <i class="bi bi-question-circle" style="font-size:2.5rem;"></i>
                            <p class="mt-3 mb-0">No FAQs have been added yet. Please check back soon.</p>
                        </div>

                    <?php } ?>

                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-4 mb-5">
        <div class="container">
            <div class="cta-strip">
                <h4 style="font-family:'Playfair Display',serif;font-weight:700;">Still have a question?</h4>
                <p class="mb-3" style="opacity:.9;">Our team is happy to help over WhatsApp or via our contact form.</p>
                <a href="index.php#contact" class="btn-light-cta">Contact Us</a>
                </a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script>
        document.getElementById('faqSearchInput').addEventListener('input', function() {
            let term = this.value.trim().toLowerCase();
            document.querySelectorAll('.faq-item').forEach(function(item) {
                let match = item.getAttribute('data-question').indexOf(term) !== -1;
                item.style.display = match ? '' : 'none';
            });
        });
    </script>
</body>

</html>