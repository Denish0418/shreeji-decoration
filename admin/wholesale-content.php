<?php

session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";

if (!isset($_SESSION['admin_id'])) {
    redirect("index.php");
}

if (isset($_POST['save'])) {

    /* CATALOGUE UPLOAD */

    $currentSettings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT wholesale_catalogue FROM settings LIMIT 1"));
    $wholesale_catalogue = $currentSettings['wholesale_catalogue'] ?? '';
    $uploadError = '';

    if (!empty($_FILES['wholesale_catalogue']['name'])) {

        $allowedExt = ['pdf'];
        $fileExt = strtolower(pathinfo($_FILES['wholesale_catalogue']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExt)) {

            $uploadError = "Catalogue must be a PDF file.";

        } elseif ($_FILES['wholesale_catalogue']['size'] > 10 * 1024 * 1024) {

            $uploadError = "Catalogue file must be under 10 MB.";

        } else {

            $newCatalogueFile = 'catalogue_' . time() . '.' . $fileExt;

            // Use an absolute path (based on this file's real location)
            // instead of a relative one, so the file always lands
            // exactly where the website expects to find it.
            $catalogueDir = __DIR__ . '/../uploads/catalogue/';

            if (!is_dir($catalogueDir)) {
                @mkdir($catalogueDir, 0755, true);
            }

            $uploadPath = $catalogueDir . $newCatalogueFile;

            if (!is_dir($catalogueDir) || !is_writable($catalogueDir)) {

                $uploadError = "The uploads/catalogue folder is missing or not writable on the server. Please create it manually and try again.";

            } elseif (move_uploaded_file($_FILES['wholesale_catalogue']['tmp_name'], $uploadPath)) {

                if (!empty($wholesale_catalogue) && file_exists($catalogueDir . $wholesale_catalogue)) {
                    unlink($catalogueDir . $wholesale_catalogue);
                }

                $wholesale_catalogue = $newCatalogueFile;

            } else {

                $uploadError = "Unable to upload the catalogue file. Please check folder permissions.";

            }
        }
    }

    $wholesale_catalogue_esc = mysqli_real_escape_string($conn, $wholesale_catalogue);

    $fields = [
        'wholesale_hero_title',
        'wholesale_hero_subtitle',
        'wholesale_stat1_num', 'wholesale_stat1_label',
        'wholesale_stat2_num', 'wholesale_stat2_label',
        'wholesale_stat3_num', 'wholesale_stat3_label',
        'wholesale_stat4_num', 'wholesale_stat4_label',
        'wholesale_group_link',
        'wholesale_group_heading',
        'wholesale_group_text',
        'wholesale_catalogue_heading',
        'wholesale_catalogue_text',
        'wholesale_phone',
        'wholesale_email',
    ];

    $setParts = [];

    foreach ($fields as $field) {
        $value = mysqli_real_escape_string($conn, trim($_POST[$field] ?? ''));
        $setParts[] = "$field='$value'";
    }

    $setParts[] = "wholesale_catalogue='$wholesale_catalogue_esc'";

    mysqli_query(
        $conn,
        "UPDATE settings SET " . implode(', ', $setParts) . " WHERE id=1"
    );

    /* STEPS */
    if (!empty($_POST['step_id'])) {
        foreach ($_POST['step_id'] as $i => $stepId) {

            $stepId = (int) $stepId;
            $icon = mysqli_real_escape_string($conn, trim($_POST['step_icon'][$i] ?? ''));
            $title = mysqli_real_escape_string($conn, trim($_POST['step_title'][$i] ?? ''));
            $desc = mysqli_real_escape_string($conn, trim($_POST['step_description'][$i] ?? ''));

            mysqli_query(
                $conn,
                "UPDATE wholesale_steps
                 SET icon='$icon', title='$title', description='$desc'
                 WHERE id=$stepId"
            );
        }
    }

    /* BENEFITS */
    if (!empty($_POST['benefit_id'])) {
        foreach ($_POST['benefit_id'] as $i => $benefitId) {

            $benefitId = (int) $benefitId;
            $icon = mysqli_real_escape_string($conn, trim($_POST['benefit_icon'][$i] ?? ''));
            $title = mysqli_real_escape_string($conn, trim($_POST['benefit_title'][$i] ?? ''));
            $desc = mysqli_real_escape_string($conn, trim($_POST['benefit_description'][$i] ?? ''));

            mysqli_query(
                $conn,
                "UPDATE wholesale_benefits
                 SET icon='$icon', title='$title', description='$desc'
                 WHERE id=$benefitId"
            );
        }
    }

    if (!empty($uploadError)) {
        redirect("wholesale-content.php?upload_error=" . urlencode($uploadError));
    }

    redirect("wholesale-content.php?success=1");
}

$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));

$stepsResult = mysqli_query($conn, "SELECT * FROM wholesale_steps ORDER BY display_order ASC, id ASC");
$steps = [];
while ($row = mysqli_fetch_assoc($stepsResult)) {
    $steps[] = $row;
}

$benefitsResult = mysqli_query($conn, "SELECT * FROM wholesale_benefits ORDER BY display_order ASC, id ASC");
$benefits = [];
while ($row = mysqli_fetch_assoc($benefitsResult)) {
    $benefits[] = $row;
}
?>
<!DOCTYPE html>
<html>

<head>

    <title>Wholesale Page Content</title>

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
            <h2 class="fw-bold mb-0">Wholesale Page Content</h2>
            <a href="../wholesale.php" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-box-arrow-up-right me-1"></i> Preview Page
            </a>
        </div>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success">Wholesale page content updated successfully.</div>
        <?php } ?>

        <?php if (isset($_GET['upload_error'])) { ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_GET['upload_error']); ?>
            </div>
        <?php } ?>

        <p class="text-muted small mb-4">
            <i class="bi bi-info-circle"></i>
            Address, main phone, WhatsApp, and email are managed on the main
            <a href="settings.php">Settings</a> page — they're shared across the whole site.
            The "Occasions" links on this page automatically follow whatever you manage under
            <a href="categories.php">Categories</a>.
        </p>

        <form method="POST" enctype="multipart/form-data">

            <!-- HERO -->
            <div class="card-box">
                <h5><i class="bi bi-flag me-1"></i> Hero Section</h5>

                <div class="mb-3">
                    <label>Hero Heading</label>
                    <input type="text" name="wholesale_hero_title" class="form-control"
                        value="<?= htmlspecialchars($settings['wholesale_hero_title'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label>Hero Subtitle</label>
                    <textarea name="wholesale_hero_subtitle" rows="2" class="form-control"><?= htmlspecialchars($settings['wholesale_hero_subtitle'] ?? '') ?></textarea>
                </div>

                <label class="mb-2">Stat Strip (the 4 numbers under the hero)</label>
                <div class="row">
                    <?php for ($i = 1; $i <= 4; $i++) { ?>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="wholesale_stat<?= $i ?>_num" class="form-control mb-2"
                                placeholder="Number" value="<?= htmlspecialchars($settings['wholesale_stat' . $i . '_num'] ?? '') ?>">
                            <input type="text" name="wholesale_stat<?= $i ?>_label" class="form-control"
                                placeholder="Label" value="<?= htmlspecialchars($settings['wholesale_stat' . $i . '_label'] ?? '') ?>">
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- HOW IT WORKS STEPS -->
            <div class="card-box">
                <h5><i class="bi bi-list-ol me-1"></i> How It Works (4 Steps)</h5>

                <?php foreach ($steps as $i => $step) { ?>
                    <div class="item-block">
                        <div class="item-num">Step <?= (int) $step['step_number']; ?></div>
                        <input type="hidden" name="step_id[]" value="<?= (int) $step['id']; ?>">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-hint">Bootstrap Icon Class</label>
                                <input type="text" name="step_icon[]" class="form-control"
                                    value="<?= htmlspecialchars($step['icon']); ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-hint">Title</label>
                                <input type="text" name="step_title[]" class="form-control"
                                    value="<?= htmlspecialchars($step['title']); ?>">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-hint">Description</label>
                                <input type="text" name="step_description[]" class="form-control"
                                    value="<?= htmlspecialchars($step['description']); ?>">
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- BENEFITS -->
            <div class="card-box">
                <h5><i class="bi bi-award me-1"></i> Wholesale Benefits (6 Cards)</h5>

                <?php foreach ($benefits as $i => $benefit) { ?>
                    <div class="item-block">
                        <div class="item-num">Benefit <?= $i + 1; ?></div>
                        <input type="hidden" name="benefit_id[]" value="<?= (int) $benefit['id']; ?>">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-hint">Bootstrap Icon Class</label>
                                <input type="text" name="benefit_icon[]" class="form-control"
                                    value="<?= htmlspecialchars($benefit['icon']); ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-hint">Title</label>
                                <input type="text" name="benefit_title[]" class="form-control"
                                    value="<?= htmlspecialchars($benefit['title']); ?>">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-hint">Description</label>
                                <input type="text" name="benefit_description[]" class="form-control"
                                    value="<?= htmlspecialchars($benefit['description']); ?>">
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- CTA CARDS -->
            <div class="card-box">
                <h5><i class="bi bi-megaphone me-1"></i> WhatsApp Group & Catalogue Cards</h5>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-2">Join WhatsApp Group card</h6>
                        <div class="mb-3">
                            <label class="form-hint">Group Invite Link</label>
                            <input type="text" name="wholesale_group_link" class="form-control"
                                value="<?= htmlspecialchars($settings['wholesale_group_link'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-hint">Heading</label>
                            <input type="text" name="wholesale_group_heading" class="form-control"
                                value="<?= htmlspecialchars($settings['wholesale_group_heading'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-hint">Description</label>
                            <input type="text" name="wholesale_group_text" class="form-control"
                                value="<?= htmlspecialchars($settings['wholesale_group_text'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-2">Download Catalogue card</h6>
                        <div class="mb-3">
                            <label class="form-hint">Heading</label>
                            <input type="text" name="wholesale_catalogue_heading" class="form-control"
                                value="<?= htmlspecialchars($settings['wholesale_catalogue_heading'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-hint">Description</label>
                            <input type="text" name="wholesale_catalogue_text" class="form-control"
                                value="<?= htmlspecialchars($settings['wholesale_catalogue_text'] ?? '') ?>">
                        </div>

                        <?php if (!empty($settings['wholesale_catalogue'])) { ?>
                            <div class="mb-2">
                                <label class="form-hint">Current Catalogue</label>
                                <div>
                                    <a href="../uploads/catalogue/<?= htmlspecialchars($settings['wholesale_catalogue']); ?>" target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <?= htmlspecialchars($settings['wholesale_catalogue']); ?>
                                    </a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="mb-2 text-muted small">
                                <i class="bi bi-info-circle"></i>
                                No catalogue uploaded yet — the "View Catalogue" button won't work until you add one.
                            </div>
                        <?php } ?>

                        <div class="mb-3">
                            <label class="form-hint">Upload New Catalogue (PDF, max 10MB)</label>
                            <input type="file" name="wholesale_catalogue" class="form-control" accept="application/pdf">
                        </div>
                    </div>
                </div>
            </div>

            <!-- WHOLESALE-SPECIFIC CONTACT -->
            <div class="card-box">
                <h5><i class="bi bi-telephone me-1"></i> Wholesale-specific Contact</h5>
                <p class="form-hint mb-3">
                    Shown as the second phone/email on the Contact Wholesale section (the main phone/email
                    from Settings are shown as the first option).
                </p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Wholesale Phone</label>
                        <input type="text" name="wholesale_phone" class="form-control"
                            value="<?= htmlspecialchars($settings['wholesale_phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Wholesale Email</label>
                        <input type="text" name="wholesale_email" class="form-control"
                            value="<?= htmlspecialchars($settings['wholesale_email'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <button type="submit" name="save" class="btn-save">
                <i class="bi bi-check-lg"></i> Save All Changes
            </button>

        </form>

    </div>

</body>

</html>