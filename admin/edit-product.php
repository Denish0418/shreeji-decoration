<?php
session_start();

require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

/* DELETE A SINGLE GALLERY IMAGE */
if (isset($_GET['delete_gallery_image'])) {

    $imgId = (int) $_GET['delete_gallery_image'];

    $imgQuery = mysqli_query($conn, "SELECT image FROM product_images WHERE id=$imgId AND product_id=$id");

    if ($imgRow = mysqli_fetch_assoc($imgQuery)) {
        $imgPath = "../uploads/products/" . $imgRow['image'];
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
        mysqli_query($conn, "DELETE FROM product_images WHERE id=$imgId");
    }

    header("Location: edit-product.php?id=$id");
    exit;
}

$query = mysqli_query(
    $conn,
    "SELECT * FROM products WHERE id='$id'"
);

$product = mysqli_fetch_assoc($query);

if (isset($_POST['update_product'])) {

    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $gst_rate = $_POST['gst_rate'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];

    $image = $product['image'];

    // NEW IMAGE UPLOAD
    if ($_FILES['image']['name'] != "") {

        $new_image = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/products/" . $new_image
        );

        // DELETE OLD IMAGE
        if (file_exists("../uploads/products/" . $image)) {
            unlink("../uploads/products/" . $image);
        }

        $image = $new_image;
    }

    mysqli_query($conn, "
UPDATE products SET

name='$name',
category='$category',
price='$price',
discount='$discount',
gst_rate='$gst_rate',
stock='$stock',
description='$description',
image='$image'

WHERE id='$id'
");

    // NEW ADDITIONAL GALLERY IMAGES
    if (!empty($_FILES['additional_images']['name'][0])) {

        $galleryDir = '../uploads/products/';
        if (!is_dir($galleryDir)) {
            mkdir($galleryDir, 0755, true);
        }

        $allowedGallery = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        $orderQuery = mysqli_query($conn, "SELECT MAX(display_order) as maxOrder FROM product_images WHERE product_id=$id");
        $order = (int) (mysqli_fetch_assoc($orderQuery)['maxOrder'] ?? 0);

        foreach ($_FILES['additional_images']['name'] as $i => $fname) {

            if ($fname === '') {
                continue;
            }

            $gExt = strtolower(pathinfo($fname, PATHINFO_EXTENSION));

            if (!in_array($gExt, $allowedGallery)) {
                continue;
            }

            if ($_FILES['additional_images']['size'][$i] > 2 * 1024 * 1024) {
                continue;
            }

            $gImageName = uniqid('prod_gallery_') . '.' . $gExt;

            if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $galleryDir . $gImageName)) {

                $order++;
                $gImageNameEsc = mysqli_real_escape_string($conn, $gImageName);

                mysqli_query(
                    $conn,
                    "INSERT INTO product_images (product_id, image, display_order)
                     VALUES ($id, '$gImageNameEsc', $order)"
                );
            }
        }
    }

    header("Location: products.php");
    exit;
}

$galleryImagesQuery = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id=$id ORDER BY display_order ASC, id ASC");
$galleryImages = [];
while ($row = mysqli_fetch_assoc($galleryImagesQuery)) {
    $galleryImages[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Product</title>

    <meta charset="UTF-8">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f8f5f1;
            font-family: Poppins;
        }

        .main-box {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            border-radius: 25px;
            padding: 40px;
        }

        .title {
            font-size: 40px;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control,
        .form-select {
            height: 55px;
            border-radius: 14px;
        }

        textarea {
            height: 140px !important;
        }

        .btn-save {
            background: #4a1200;
            color: #fff;
            border: none;
            padding: 14px 35px;
            border-radius: 14px;
            font-weight: 600;
        }

        .btn-save:hover {
            background: #2d0a00;
        }

        .product-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 20px;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="main-box shadow-sm">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <div class="title">
                        Edit Product
                    </div>

                    <p class="text-muted">
                        Update product details
                    </p>

                </div>

                <a href="products.php"
                    class="btn btn-light">

                    <i class="bi bi-arrow-left"></i>
                    Back

                </a>

            </div>

            <form method="POST"
                enctype="multipart/form-data">

                <div class="row">

                    <div class="col-md-8">

                        <!-- PRODUCT NAME -->
                        <div class="mb-4">

                            <label class="form-label">
                                Product Name
                            </label>

                            <input type="text"
                                name="name"
                                class="form-control"
                                value="<?php echo $product['name']; ?>"
                                required>

                        </div>

                        <div class="row">

                            <!-- CATEGORY -->
                            <div class="col-md-6 mb-4">

                                <label class="form-label">
                                    Category
                                </label>

                                <select name="category"
                                    class="form-select"
                                    required>

                                    <?php
                                    $cat_query = mysqli_query(
                                        $conn,
                                        "SELECT * FROM categories"
                                    );

                                    while ($cat = mysqli_fetch_assoc($cat_query)) {
                                    ?>

                                        <option value="<?php echo $cat['name']; ?>"

                                            <?php
                                            if ($product['category'] == $cat['name']) {
                                                echo "selected";
                                            }
                                            ?>>

                                            <?php echo $cat['name']; ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- PRICE -->
                            <div class="col-md-6 mb-4">

                                <label class="form-label">
                                    Price
                                </label>

                                <input type="number"
                                    name="price"
                                    class="form-control"
                                    value="<?php echo $product['price']; ?>"
                                    required>

                            </div>

                            <!-- DISCOUNT -->
                            <div class="col-md-6 mb-4">

                                <label class="form-label">
                                    Discount (%)
                                </label>

                                <input type="number"
                                    name="discount"
                                    class="form-control"
                                    value="<?php echo $product['discount']; ?>"
                                    min="0"
                                    max="100">

                            </div>

                            <div class="col-md-6 mb-4">

                                <label class="form-label">
                                    GST (%)
                                </label>

                                <input
                                    type="number"
                                    name="gst_rate"
                                    class="form-control"
                                    value="<?php echo $product['gst_rate']; ?>"
                                    min="0"
                                    step="0.01">

                            </div>

                        </div>

                        <!-- STOCK -->
                        <div class="mb-4">

                            <label class="form-label">
                                Stock Quantity
                            </label>

                            <input type="number"
                                name="stock"
                                class="form-control"
                                value="<?php echo $product['stock']; ?>"
                                required>

                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-4">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea name="description"
                                class="form-control"
                                required><?php echo $product['description']; ?></textarea>

                        </div>

                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="col-md-4">

                        <label class="form-label">
                            Current Image
                        </label>

                        <br>

                        <img src="../uploads/products/<?php echo $product['image']; ?>"
                            class="product-img">

                        <div class="mb-4">

                            <label class="form-label">
                                Change Image
                            </label>

                            <input type="file"
                                name="image"
                                class="form-control">

                        </div>

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Additional Gallery Images
                    </label>

                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <?php if (count($galleryImages) === 0) { ?>
                            <p class="text-muted small mb-0">No additional images yet.</p>
                        <?php } ?>
                        <?php foreach ($galleryImages as $img) { ?>
                            <div style="position:relative;width:90px;">
                                <img src="../uploads/products/<?= htmlspecialchars($img['image']); ?>"
                                    style="width:90px;height:90px;object-fit:cover;border-radius:10px;border:1px solid #eee;">
                                <a href="edit-product.php?id=<?= $id; ?>&delete_gallery_image=<?= (int) $img['id']; ?>"
                                    onclick="return confirm('Remove this image from the gallery?')"
                                    style="position:absolute;top:-6px;right:-6px;background:#c0392b;color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;">
                                    <i class="bi bi-x"></i>
                                </a>
                            </div>
                        <?php } ?>
                    </div>

                    <input type="file"
                        name="additional_images[]"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        multiple>
                    <div class="form-text">Add more photos to the product page's gallery (max 2MB each).</div>

                </div>

                <button type="submit"
                    name="update_product"
                    class="btn-save">

                    <i class="bi bi-check-circle"></i>
                    Update Product

                </button>

            </form>

        </div>

    </div>

</body>

</html>