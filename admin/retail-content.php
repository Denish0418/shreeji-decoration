<?php

session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";

if (!isset($_SESSION['admin_id'])) {
    redirect("index.php");
}

if (isset($_POST['save'])) {

    $fields = [
        'retail_hero_badge',
        'retail_hero_title',
        'retail_hero_subtitle',
        'retail_kpi4_num',
        'retail_kpi4_label',
        'retail_banner_chip',
        'retail_banner_heading',
        'retail_banner_text',
        'retail_switch_heading',
        'retail_switch_text',
    ];

    $setParts = [];

    foreach ($fields as $field) {
        $value = mysqli_real_escape_string($conn, trim($_POST[$field] ?? ''));
        $setParts[] = "$field='$value'";
    }

    mysqli_query(
        $conn,
        "UPDATE settings SET " . implode(', ', $setParts) . " WHERE id=1"
    );

    /* TRUST BAR ITEMS */
    if (!empty($_POST['trust_id'])) {
        foreach ($_POST['trust_id'] as $i => $trustId) {

            $trustId = (int) $trustId;
            $icon = mysqli_real_escape_string($conn, trim($_POST['trust_icon'][$i] ?? ''));
            $title = mysqli_real_escape_string($conn, trim($_POST['trust_title'][$i] ?? ''));
            $subtitle = mysqli_real_escape_string($conn, trim($_POST['trust_subtitle'][$i] ?? ''));

            mysqli_query(
                $conn,
                "UPDATE retail_trust_items
                 SET icon='$icon', title='$title', subtitle='$subtitle'
                 WHERE id=$trustId"
            );
        }
    }

    /* FEATURE CARDS */
    if (!empty($_POST['feature_id'])) {
        foreach ($_POST['feature_id'] as $i => $featureId) {

            $featureId = (int) $featureId;
            $icon = mysqli_real_escape_string($conn, trim($_POST['feature_icon'][$i] ?? ''));
            $title = mysqli_real_escape_string($conn, trim($_POST['feature_title'][$i] ?? ''));
            $desc = mysqli_real_escape_string($conn, trim($_POST['feature_description'][$i] ?? ''));

            mysqli_query(
                $conn,
                "UPDATE retail_features
                 SET icon='$icon', title='$title', description='$desc'
                 WHERE id=$featureId"
            );
        }
    }

    redirect("retail-content.php?success=1");
}

$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));

$trustResult = mysqli_query($conn, "SELECT * FROM retail_trust_items ORDER BY display_order ASC, id ASC");
$trustItems = [];
while ($row = mysqli_fetch_assoc($trustResult)) {
    $trustItems[] = $row;
}

$featuresResult = mysqli_query($conn, "SELECT * FROM retail_features ORDER BY display_order ASC, id ASC");
$features = [];
while ($row = mysqli_fetch_assoc($featuresResult)) {
    $features[] = $row;
}
?>
<!DOCTYPE html>
<html>

<head>

    <title>Retail Page Content</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #f8f5f2;
            font-family: Poppins, sans-serif;
        }

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

        .sidebar ul li a:hover {
            background: #7A2E0D;
        }

        .sidebar ul li a.active {
            background: #7A2E0D;
        }

        .main-content {
            margin-left: 270px;
            padding: 40px;
        }

        .card-box {
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 24px;
        }

        .card-box h5 {
            font-weight: 700;
            margin-bottom: 18px;
        }

        .item-block {
            border: 1px solid #eee;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 14px;
            background: #fbfaf8;
        }

        .item-block .item-num {
            font-weight: 700;
            color: #7A2E0D;
            margin-bottom: 10px;
        }

        .btn-save {
            background: #2d140a;
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
        }

        .btn-save:hover {
            background: #7A2E0D;
            color: #fff;
        }

        .form-hint {
            font-size: 12.5px;
            color: #888;
        }
    </style>

</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Retail Page Content</h2>
            <a href="../retail.php" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-box-arrow-up-right me-1"></i> Preview Page
            </a>
        </div>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success">Retail page content updated successfully.</div>
        <?php } ?>

        <p class="text-muted small mb-4">
            <i class="bi bi-info-circle"></i>
            Your actual products and categories are managed separately under
            <a href="products.php">Products</a> and <a href="categories.php">Categories</a> — not here.
            The 3 live stats in the hero (product count, happy customers, average rating) update
            automatically and aren't editable, since they reflect real data.
        </p>

        <form method="POST">

            <!-- HERO -->
            <div class="card-box">
                <h5><i class="bi bi-flag me-1"></i> Hero Section</h5>

                <div class="mb-3">
                    <label>Badge Text</label>
                    <input type="text" name="retail_hero_badge" class="form-control"
                        value="<?= htmlspecialchars($settings['retail_hero_badge'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label>Hero Heading</label>
                    <input type="text" name="retail_hero_title" class="form-control"
                        value="<?= htmlspecialchars($settings['retail_hero_title'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label>Hero Subtitle</label>
                    <textarea name="retail_hero_subtitle" rows="2" class="form-control"><?= htmlspecialchars($settings['retail_hero_subtitle'] ?? '') ?></textarea>
                </div>

                <label class="mb-2">4th Stat (the other 3 are live numbers and aren't editable)</label>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <input type="text" name="retail_kpi4_num" class="form-control mb-2"
                            placeholder="Number" value="<?= htmlspecialchars($settings['retail_kpi4_num'] ?? '') ?>">
                        <input type="text" name="retail_kpi4_label" class="form-control"
                            placeholder="Label" value="<?= htmlspecialchars($settings['retail_kpi4_label'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- TRUST BAR -->
            <div class="card-box">
                <h5><i class="bi bi-patch-check me-1"></i> Trust Bar (5 Items)</h5>

                <?php foreach ($trustItems as $i => $item) { ?>
                    <div class="item-block">
                        <div class="item-num">Item <?= $i + 1; ?></div>
                        <input type="hidden" name="trust_id[]" value="<?= (int) $item['id']; ?>">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-hint">Bootstrap Icon Class</label>
                                <input type="text" name="trust_icon[]" class="form-control"
                                    value="<?= htmlspecialchars($item['icon']); ?>">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-hint">Title</label>
                                <input type="text" name="trust_title[]" class="form-control"
                                    value="<?= htmlspecialchars($item['title']); ?>">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-hint">Subtitle</label>
                                <input type="text" name="trust_subtitle[]" class="form-control"
                                    value="<?= htmlspecialchars($item['subtitle']); ?>">
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- SHOP WITH CONFIDENCE BANNER -->
            <div class="card-box">
                <h5><i class="bi bi-award me-1"></i> "Shop with Confidence" Banner</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-hint">Chip Label</label>
                        <input type="text" name="retail_banner_chip" class="form-control"
                            value="<?= htmlspecialchars($settings['retail_banner_chip'] ?? '') ?>">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-hint">Heading</label>
                        <input type="text" name="retail_banner_heading" class="form-control"
                            value="<?= htmlspecialchars($settings['retail_banner_heading'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-hint">Description</label>
                    <textarea name="retail_banner_text" rows="2" class="form-control"><?= htmlspecialchars($settings['retail_banner_text'] ?? '') ?></textarea>
                </div>

                <label class="mb-2">Feature Cards (4)</label>
                <?php foreach ($features as $i => $feature) { ?>
                    <div class="item-block">
                        <div class="item-num">Card <?= $i + 1; ?></div>
                        <input type="hidden" name="feature_id[]" value="<?= (int) $feature['id']; ?>">
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <label class="form-hint">Emoji</label>
                                <input type="text" name="feature_icon[]" class="form-control"
                                    value="<?= htmlspecialchars($feature['icon']); ?>">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-hint">Title</label>
                                <input type="text" name="feature_title[]" class="form-control"
                                    value="<?= htmlspecialchars($feature['title']); ?>">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-hint">Description</label>
                                <input type="text" name="feature_description[]" class="form-control"
                                    value="<?= htmlspecialchars($feature['description']); ?>">
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- SWITCH TO WHOLESALE BANNER -->
            <div class="card-box">
                <h5><i class="bi bi-building me-1"></i> "Are You a Dealer?" Banner</h5>

                <div class="mb-3">
                    <label>Heading</label>
                    <input type="text" name="retail_switch_heading" class="form-control"
                        value="<?= htmlspecialchars($settings['retail_switch_heading'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="retail_switch_text" rows="2" class="form-control"><?= htmlspecialchars($settings['retail_switch_text'] ?? '') ?></textarea>
                </div>
            </div>

            <button type="submit" name="save" class="btn-save">
                <i class="bi bi-check-lg"></i> Save All Changes
            </button>

        </form>

    </div>

</body>

</html>