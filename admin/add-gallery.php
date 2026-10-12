<?php

session_start();

require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

/* ADD IMAGE */

if (isset($_POST['add_image'])) {

    $title = mysqli_real_escape_string(
        $conn,
        $_POST['title']
    );

    $category = mysqli_real_escape_string(
        $conn,
        $_POST['category']
    );

    $image = $_FILES['image']['name'];

    $tmp_name = $_FILES['image']['tmp_name'];

    $new_name = time() . "_" . $image;

    move_uploaded_file(
        $tmp_name,
        "../uploads/gallery/" . $new_name
    );

    mysqli_query(
        $conn,

        "INSERT INTO gallery(

    title,
    category,
    image

    )

    VALUES(

    '$title',
    '$category',
    '$new_name'

    )"

    );

    header("Location: gallery.php");
    exit;
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Add Gallery Image</title>

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
            width: 270px;
            height: 100vh;
            background: #2d140a;
            padding: 30px;
            color: #fff;
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

        /* form card */
        .form-card {
            background: #fff;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
            max-width: 700px;
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

        .file-input {
            padding: 12px;
            height: auto;
        }

        /* button */
        .submit-btn {
            background: #2d140a;
            color: #fff;
            border: none;
            padding: 14px 28px;
            border-radius: 14px;
            font-weight: 600;
        }

        .submit-btn:hover {
            background: #7A2E0D;
        }
    </style>

</head>

<body>

    <!-- SIDEBAR -->

<?php include 'includes/sidebar.php'; ?>
    <!-- MAIN -->

    <div class="main-content">

        <h2 class="fw-bold mb-4">
            Add Gallery Image
        </h2>

        <div class="form-card">

            <form method="POST"
                enctype="multipart/form-data">

                <div class="mb-4">

                    <label class="form-label">
                        Image Title
                    </label>

                    <input type="text"
                        name="title"
                        class="form-control"
                        required>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Category
                    </label>

                    <select name="category"
                        class="form-control"
                        required>

                        <option value="">
                            Select Category
                        </option>

                        <option>
                            Wedding
                        </option>

                        <option>
                            Birthday
                        </option>

                        <option>
                            Festival
                        </option>

                        <option>
                            Puja
                        </option>

                        <option>
                            Home Decor
                        </option>

                    </select>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Upload Image
                    </label>

                    <input type="file"
                        name="image"
                        class="form-control file-input"
                        required>

                </div>

                <button type="submit"
                    name="add_image"
                    class="submit-btn">

                    <i class="bi bi-plus-lg"></i>

                    Add Image

                </button>

            </form>

        </div>

    </div>

</body>

</html>