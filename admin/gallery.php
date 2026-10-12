<?php

session_start();

require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$gallery = mysqli_query(
    $conn,
    "SELECT * FROM gallery ORDER BY id DESC"
);

$total_gallery =
mysqli_num_rows($gallery);

?>
<!DOCTYPE html>
<html>

<head>

    <title>Gallery</title>

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

        /* top bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* button */
        .add-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2d140a;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .add-btn:hover {
            background: #7A2E0D;
            color: #fff;
        }

        /* gallery card */
        .gallery-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .gallery-img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            background: #f3f0ec;
        }

        .gallery-body {
            padding: 20px;
        }

        .gallery-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .gallery-category {
            color: #9CA3AF;
            font-size: 13px;
        }

        /* buttons */
        .action-group {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .edit-btn {
            background: #EFE3D3;
            color: #7A3B00;
        }

        .delete-btn {
            background: #F5D7D7;
            color: #9B1C1C;
        }
    </style>

</head>

<body>

    <!-- SIDEBAR -->

    <?php include 'includes/sidebar.php'; ?>

    <!-- MAIN -->

    <div class="main-content">

        <div class="top-bar">

            <div>

                <h2 class="fw-bold mb-1">
                    Gallery
                </h2>

                <p class="text-muted mb-0" style="font-size:14px;">
                    Manage gallery images
                </p>

            </div>

            <a href="add-gallery.php"
                class="add-btn">

                <i class="bi bi-plus-lg"></i>

                Add Image

            </a>

        </div>

        <!-- GALLERY -->

        <div class="row g-4">
<?php if(mysqli_num_rows($gallery)==0){ ?>

<div class="text-center p-5 bg-white rounded-4">

    <i class="bi bi-images"
       style="font-size:70px;color:#ccc;"></i>

    <h4 class="mt-3">
        No Gallery Images Found
    </h4>

    <p class="text-muted">
        Upload your first gallery image.
    </p>

</div>

<?php } else { ?>

<?php } ?>

            <?php while ($row = mysqli_fetch_assoc($gallery)) {

                $imageExists = file_exists(__DIR__ . '/../uploads/gallery/' . $row['image']);

            ?>

                <div class="col-lg-4 col-md-6">

                    <div class="gallery-card">

                        <?php if ($imageExists) { ?>
                            <img src="../uploads/gallery/<?php echo $row['image']; ?>"
                                class="gallery-img">
                        <?php } else { ?>
                            <div class="gallery-img d-flex flex-column align-items-center justify-content-center bg-light text-danger">
                                <i class="bi bi-exclamation-triangle fs-2"></i>
                                <small class="mt-1">Image file missing</small>
                            </div>
                        <?php } ?>

                        <div class="gallery-body">

                            <div class="gallery-title">

                                <?php echo $row['title']; ?>

                            </div>

                            <div class="gallery-category">

                                <?php echo $row['category']; ?>

                            </div>

                            <div class="action-group">

                                <a href="edit-gallery.php?id=<?php echo $row['id']; ?>"
                                    class="action-btn edit-btn">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                <a href="delete-gallery.php?id=<?php echo $row['id']; ?>"
                                    class="action-btn delete-btn"
                                    onclick="return confirm('Delete this gallery image?')">

                                    <i class="bi bi-trash"></i>

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } ?>

        </div>

    </div>

</body>

</html>