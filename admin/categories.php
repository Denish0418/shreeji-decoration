<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

/* ADD CATEGORY */
if (isset($_POST['add_category'])) {

    $name = mysqli_real_escape_string(
        $conn,
        $_POST['category_name']
    );

    $icon = mysqli_real_escape_string(
        $conn,
        $_POST['category_icon']
    );

    mysqli_query(
    $conn,
    "INSERT INTO categories(name,icon)
     VALUES('$name','$icon')"
);

    header("Location: categories.php?success=1");
    exit;
}

/* DELETE CATEGORY */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    mysqli_query(
        $conn,
        "DELETE FROM categories WHERE id='$id'"
    );

    header("Location: categories.php?deleted=1");
    exit;
}

$categories = mysqli_query(
    $conn,
    "SELECT * FROM categories ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Categories</title>

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

        .table td {
            vertical-align: middle;
        }
    </style>

</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <h2 class="fw-bold mb-4">
            Manage Categories
        </h2>

        <div class="card-box mb-4">

            <form method="POST">

                <div class="row">

                    <div class="col-md-5">
                        <input type="text"
                            name="category_name"
                            class="form-control"
                            placeholder="Category Name"
                            required>
                    </div>

                    <div class="col-md-3">
                        <input type="text"
                            name="category_icon"
                            class="form-control"
                            placeholder="Emoji">
                    </div>

                    <div class="col-md-2">
                        <button type="submit"
                            name="add_category"
                            class="btn-save">
                            Add Category
                        </button>
                    </div>

                </div>

            </form>

        </div>

        <div class="card-box">

            <table class="table">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>

                        <tr>

                            <td><?= $cat['id']; ?></td>

                            <td style="font-size:25px;">
                                <?= $cat['icon']; ?>
                            </td>

                            <td>
                                <?= $cat['name']; ?>
                            </td>

                            <td>

                                <a href="categories.php?delete=<?= $cat['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete Category?')">
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</body>

</html>