<?php

session_start();

require_once "../config/db.php";

require_once "../includes/functions.php";

if (!isset($_SESSION['admin_id'])) {

    redirect("index.php");
}

if (isset($_POST['save'])) {

    $title = mysqli_real_escape_string($conn, $_POST['title']);

    $subtitle = mysqli_real_escape_string($conn, $_POST['subtitle']);

    $content = mysqli_real_escape_string($conn, $_POST['content']);

    mysqli_query(
        $conn,
        "UPDATE site_content
         SET title='$title', subtitle='$subtitle', content='$content'
         WHERE page_name='terms'"
    );

    redirect("terms.php?success=1");
}

$query = mysqli_query(
    $conn,
    "SELECT * FROM site_content
     WHERE page_name='terms'
     LIMIT 1"
);

$policy = mysqli_fetch_assoc($query);

/* If the row somehow doesn't exist yet, create it so the form still works */
if (!$policy) {

    mysqli_query(
        $conn,
        "INSERT INTO site_content (page_name, title, subtitle, content)
         VALUES ('terms', 'Terms & Conditions', '', '')"
    );

    $policy = [
        'title'    => 'Terms & Conditions',
        'subtitle' => '',
        'content'  => '',
    ];
}
?>
<!DOCTYPE html>

<html>

<head>

    <title>
        Terms & Conditions CMS
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
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

        <h2 class="mb-4 fw-bold">
            Terms & Conditions Content
        </h2>

        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success">Terms & Conditions updated successfully.</div>
        <?php } ?>

        <form method="POST">

            <div class="card-box">

                <div class="mb-3">

                    <label>
                        Page Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        value="<?= htmlspecialchars($policy['title'] ?? '') ?>">

                </div>

                <div class="mb-3">

                    <label>
                        Subtitle
                    </label>

                    <input
                        type="text"
                        name="subtitle"
                        class="form-control"
                        value="<?= htmlspecialchars($policy['subtitle'] ?? '') ?>">

                </div>

                <div class="mb-3">

                    <label>
                        Policy Content
                    </label>

                    <p class="form-hint mb-2">
                        Tip: separate sections with a blank line. Short lines (under ~60 characters) with no
                        ending punctuation will automatically display as section headings on the public page.
                    </p>

                    <textarea
                        name="content"
                        rows="18"
                        class="form-control"><?= htmlspecialchars($policy['content'] ?? '') ?></textarea>

                </div>

                <button
                    class="btn-save"
                    name="save">
                    Save Changes
                </button>

                <a href="../terms.php" target="_blank" class="btn btn-outline-secondary ms-2">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Preview Page
                </a>

            </div>

        </form>

    </div>

</body>

</html>