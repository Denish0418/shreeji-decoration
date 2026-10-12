<?php

session_start();

require_once '../config/db.php';



if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['name']);
    $category    = trim($_POST['category']);
    $price       = trim($_POST['price']);
    $stock       = trim($_POST['stock']);
    $type        = trim($_POST['type']);
    $description = trim($_POST['description']);

    $gst_rate    = (float)($_POST['gst_rate'] ?? 18);

    /* ── Validation ── */
    if ($name === '')                        $errors[] = 'Product name is required.';
    if ($category === '')                    $errors[] = 'Category is required.';
    if (!is_numeric($price) || $price < 0)  $errors[] = 'Enter a valid price.';
    if (!is_numeric($stock) || $stock < 0)  $errors[] = 'Enter a valid stock quantity.';

    /* ── Image Upload ── */
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = 'Only JPG, PNG, WEBP and GIF images are allowed.';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image must be under 2 MB.';
        } else {
            $upload_dir = '../uploads/products/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $image_name = uniqid('prod_') . '.' . $ext;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name)) {
                $errors[] = 'Failed to upload image. Check folder permissions.';
                $image_name = '';
            }
        }
    }

    /* ── Insert ── */
    if (empty($errors)) {
        $name_s        = mysqli_real_escape_string($conn, $name);
        $category_s    = mysqli_real_escape_string($conn, $category);
        $description_s = mysqli_real_escape_string($conn, $description);
        $type_s        = mysqli_real_escape_string($conn, $type);
        $price_f       = (float)$price;
        $stock_i       = (int)$stock;
        $discount = (int)($_POST['discount'] ?? 0);

        $sql = "INSERT INTO products (
    name,
    category,
    price,
    discount,
    gst_rate,
    stock,
    type,
    description,
    image
)
VALUES (
    '$name_s',
    '$category_s',
    $price_f,
    $discount,
    $gst_rate,
    $stock_i,
    '$type_s',
    '$description_s',
    '$image_name'
)";

        if (mysqli_query($conn, $sql)) {

            $newProductId = mysqli_insert_id($conn);

            /* ── Additional Gallery Images ── */
            if (!empty($_FILES['additional_images']['name'][0])) {

                $galleryDir = '../uploads/products/';
                if (!is_dir($galleryDir)) {
                    mkdir($galleryDir, 0755, true);
                }

                $allowedGallery = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $order = 0;

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
                             VALUES ($newProductId, '$gImageNameEsc', $order)"
                        );
                    }
                }
            }

            header("Location: products.php?msg=added");
            exit;
        } else {
            $errors[] = 'Database error: ' . mysqli_error($conn);
        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Add Product – Shreeji Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #f8f5f2;
            font-family: Poppins, sans-serif;
        }

        /* ── SIDEBAR — identical to products.php / dashboard.php ── */
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

        .sidebar ul li a:hover {
            background: #7A2E0D;
        }

        .sidebar ul li a.active {
            background: #7A2E0D;
        }

        /* ── MAIN CONTENT ── */
        .main-content {
            margin-left: 270px;
            padding: 40px;
        }

        /* ── TOP BAR ── */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f3f0ec;
            color: #2d140a;
            border: none;
            padding: 12px 20px;
            border-radius: 14px;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: all .2s;
        }

        .back-btn:hover {
            background: #e8e3de;
            color: #2d140a;
        }

        /* ── FORM CARD ── */
        .form-card {
            background: #fff;
            padding: 36px;
            border-radius: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .hero-card {
            background: linear-gradient(135deg, #2d140a, #7A2E0D);
            color: #fff;
            padding: 35px;
            border-radius: 25px;
            margin-bottom: 30px;
        }

        .hero-card h2 {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-card p {
            opacity: .85;
            margin: 0;
        }

        .form-card {
            background: #fff;
            padding: 36px;
            border-radius: 28px;
            border: 1px solid #f0e7df;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
            transition: .3s;
        }

        .form-card:hover {
            transform: translateY(-3px);
        }

        .form-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1.5px solid #f3f0ec;
        }

        /* ── FORM ELEMENTS ── */
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-control,
        .form-select {
            border: 1.5px solid #ede8e3;
            border-radius: 14px;
            padding: 12px 16px;
            font-family: Poppins, sans-serif;
            font-size: 14px;
            color: #1a0a04;
            background: #fdfcfb;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #7A2E0D;
            box-shadow: 0 0 0 3px rgba(122, 46, 13, .1);
            outline: none;
            background: #fff;
        }

        .form-control::placeholder {
            color: #bbb;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* ── IMAGE UPLOAD ── */
        .upload-zone {
            border: 2px dashed #ede8e3;
            border-radius: 18px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all .25s;
            background: #fdfcfb;
            position: relative;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: #7A2E0D;
            background: #fdf6f2;
        }

        .upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .upload-icon {
            font-size: 2.5rem;
            color: #d4b8a8;
            margin-bottom: 10px;
        }

        .upload-zone h6 {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .upload-zone p {
            font-size: 12px;
            color: #9CA3AF;
            margin: 0;
        }

        .img-preview {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 18px;
            border: 3px solid #7A2E0D;
            padding: 4px;
            background: #fff;
        }

        /* ── SUBMIT BUTTON ── */
        .btn-submit {
            background: #2d140a;
            color: #fff;
            border: none;
            padding: 15px 36px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            font-family: Poppins, sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all .25s;
        }

        .btn-submit:hover {
            background: #7A2E0D;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(122, 46, 13, .25);
        }

        /* ── ERROR / REQUIRED ── */
        .error-list {
            background: #FEF2F2;
            border: 1.5px solid #FECACA;
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .error-list li {
            font-size: 13px;
            color: #9B1C1C;
            font-weight: 500;
        }

        .required::after {
            content: ' *';
            color: #9B1C1C;
        }

        /* ── CHAR COUNTER ── */
        .char-count {
            font-size: 11px;
            color: #9CA3AF;
            text-align: right;
            margin-top: 4px;
        }



        .discount-preview {

            background:
                linear-gradient(135deg,
                    #fff7f0,
                    #fff);

            border: 2px solid #ead7ca;

            padding: 20px;

            border-radius: 20px;

            margin-bottom: 25px;

        }

        .discount-preview h3 {

            color: #7A2E0D;

            font-weight: 700;

            margin: 0;

        }
    </style>
</head>

<body>

    <!-- ══ SIDEBAR ══ -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- ══ MAIN CONTENT ══ -->
    <div class="main-content">

        <!-- Top Bar -->
        <div class="hero-card">

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <div>

                    <h2>
                        <i class="bi bi-plus-circle-fill"></i>
                        Add New Product
                    </h2>

                    <p>
                        Create beautiful products for your retail and wholesale customers
                    </p>

                </div>

                <a href="products.php" class="back-btn">
                    <i class="bi bi-arrow-left"></i>
                    Back to Products
                </a>

            </div>

        </div>

        <!-- Error List -->
        <?php if (!empty($errors)): ?>
            <ul class="error-list">
                <?php foreach ($errors as $e): ?>
                    <li><i class="bi bi-exclamation-circle-fill me-2"></i><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" id="addForm" novalidate>
            <div class="row g-4">

                <!-- LEFT COLUMN -->
                <div class="col-lg-8">
                    <div class="form-card">

                        <p class="form-section-title">
                            📦 Product Information
                        </p>

                        <!-- Name -->
                        <div class="mb-4">
                            <label class="form-label required">Product Name</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="e.g. Diwali Decoration Kit"
                                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                maxlength="150" id="nameInput"
                                oninput="updateCount('nameInput','nameCount',150)" required />
                            <div class="char-count"><span id="nameCount">0</span>/150</div>
                        </div>

                        <!-- Category + Type -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">Category</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select category…</option>
                                    <?php

                                    $category_query = mysqli_query(
                                        $conn,
                                        "SELECT * FROM categories
     WHERE status = 1
     ORDER BY name ASC"
                                    );

                                    while ($cat = mysqli_fetch_assoc($category_query)) {

                                        $selected = '';

                                        if (
                                            isset($_POST['category']) &&
                                            $_POST['category'] == $cat['name']
                                        ) {
                                            $selected = 'selected';
                                        }

                                    ?>

                                        <option
                                            value="<?php echo $cat['name']; ?>"
                                            <?php echo $selected; ?>>

                                            <?php echo $cat['name']; ?>

                                        </option>

                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Product Type</label>
                                <select name="type" class="form-select" required>
                                    <?php
                                    $types = ['Retail', 'Wholesale', 'Both'];
                                    foreach ($types as $t) {
                                        $sel = (($_POST['type'] ?? 'Retail') === $t) ? 'selected' : '';
                                        echo "<option value=\"$t\" $sel>$t</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Price + Stock -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label required">Price (₹)</label>
                                <div style="position:relative">
                                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9CA3AF;font-size:15px;">₹</span>
                                    <input type="number"
                                        name="price"
                                        id="price"
                                        class="form-control"
                                        style="padding-left:30px;"
                                        placeholder="0.00" min="0" step="0.01"
                                        value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required />
                                </div>
                            </div>
                            <div class="col-md-4">

                                <label class="form-label">

                                    🎯 Discount (%)

                                </label>

                                <input
                                    type="number"
                                    name="discount"
                                    id="discount"
                                    class="form-control"
                                    min="0"
                                    max="100"
                                    value="0">

                            </div>

                            <div class="col-md-4">

                                <label class="form-label">

                                    GST (%)

                                </label>

                                <input
                                    type="number"
                                    name="gst_rate"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="18">

                            </div>
                            <div class="col-md-4">

                                <label class="form-label required">
                                    Stock Quantity
                                </label>

                                <input type="number"
                                    name="stock"
                                    class="form-control"
                                    placeholder="e.g. 50"
                                    min="0"
                                    value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>"
                                    required />

                                <div id="stockStatus"
                                    style="margin-top:10px;">
                                </div>

                            </div>
                        </div>


                        <div class="discount-preview">

                            <div class="d-flex justify-content-between">

                                <span>
                                    Final Selling Price
                                </span>

                                <div style="text-align:right;">

                                    <small
                                        style="
    color:#999;
    display:block;
    ">
                                        After Discount
                                    </small>

                                    <h2 id="finalPrice"
                                        style="
        color:#7A2E0D;
        font-weight:700;
        margin:0;
        ">
                                        ₹0.00
                                    </h2>

                                </div>
                            </div>

                        </div>

                        <!-- Description -->
                        <div class="mb-2">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" id="descInput"
                                placeholder="Describe the product — materials, usage, occasion…"
                                maxlength="500"
                                oninput="updateCount('descInput','descCount',500)"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <div class="char-count"><span id="descCount">0</span>/500</div>
                        </div>

                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="col-lg-4">

                    <!-- Image Upload -->
                    <div class="form-card mb-4">
                        <p class="form-section-title">
                            🖼 Product Image
                        </p>

                        <div class="upload-zone" id="uploadZone">
                            <input type="file" name="image" id="imageInput"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                onchange="previewImage(this)" />
                            <div class="upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                            <h6>Click or drag image here</h6>
                            <p>JPG, PNG, WEBP, GIF — max 2 MB</p>
                        </div>

                        <img id="imgPreview" class="img-preview" src="" alt="Preview" />

                        <p style="font-size:12px;color:#9CA3AF;margin-top:10px;margin-bottom:0;">
                            <i class="bi bi-info-circle me-1"></i>
                            Recommended: 600×600px square image
                        </p>
                    </div>

                    <!-- Additional Gallery Images -->
                    <div class="form-card mb-4">
                        <p class="form-section-title">
                            🖼️ Additional Images (Gallery)
                        </p>

                        <div class="upload-zone">
                            <input type="file" name="additional_images[]" id="galleryInput"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                multiple onchange="previewGallery(this)" />
                            <div class="upload-icon"><i class="bi bi-images"></i></div>
                            <h6>Click or drag multiple images here</h6>
                            <p>Shown as extra photos on the product page (max 2 MB each)</p>
                        </div>

                        <div id="galleryPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>

                    <!-- Quick Summary -->
                    <div class="form-card" style="background:#fdfaf7;">
                        <p class="form-section-title">
                            📋 Live Product Preview
                        </p>
                        <div id="summaryBox" style="font-size:13px;color:#6B7280;line-height:2;">
                            Fill in the form to see a summary here.
                        </div>
                    </div>

                </div>

                <!-- SUBMIT -->
                <div class="col-12">
                    <div class="form-card" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
                        <p style="margin:0;font-size:13px;color:#9CA3AF;">
                            <i class="bi bi-info-circle me-1"></i>
                            Fields marked with <span style="color:#9B1C1C;font-weight:700;">*</span> are required.
                        </p>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;">
                            <a href="products.php" class="back-btn">
                                <i class="bi bi-x-lg"></i> Cancel
                            </a>
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <i class="bi bi-plus-circle-fill"></i> Add Product
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>
    <!-- /main-content -->

    <script>
        /* ── IMAGE PREVIEW ── */
        function previewImage(input) {
            const preview = document.getElementById('imgPreview');
            const zone = document.getElementById('uploadZone');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    zone.style.borderColor = '#7A2E0D';
                    zone.style.borderStyle = 'solid';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewGallery(input) {
            const container = document.getElementById('galleryPreview');
            container.innerHTML = '';

            if (!input.files) {
                return;
            }

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '70px';
                    img.style.height = '70px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '10px';
                    img.style.border = '1px solid #eee';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        /* ── CHAR COUNTER ── */
        function updateCount(inputId, countId, max) {
            const len = document.getElementById(inputId).value.length;
            document.getElementById(countId).textContent = len;
            document.getElementById(countId).style.color = len > max * 0.9 ? '#B8863F' : '#9CA3AF';
        }

        /* Init char counts */
        ['nameInput'].forEach(id => {
            const el = document.getElementById(id);
            if (el) updateCount(id, id.replace('Input', 'Count'), 150);
        });
        updateCount('descInput', 'descCount', 500);

        /* ── LIVE SUMMARY ── */
        function updateSummary() {
            const name = document.querySelector('[name="name"]').value || '—';
            const cat = document.querySelector('[name="category"]').value || '—';
            const price = document.querySelector('[name="price"]').value || '—';
            const stock = document.querySelector('[name="stock"]').value || '—';
            const type = document.querySelector('[name="type"]').value || '—';

            document.getElementById('summaryBox').innerHTML = `

<div style="
background:white;
padding:18px;
border-radius:18px;
box-shadow:0 4px 15px rgba(0,0,0,.05);
">

<h5 style="
color:#7A2E0D;
font-weight:700;
">

📦 ${name}

</h5>

<hr>

<p>
📂 <b>Category:</b> ${cat}
</p>

<p>
🏷 <b>Type:</b> ${type}
</p>

<p>
📦 <b>Stock:</b> ${stock}
</p>

<p>
💰 <b>Price:</b> ₹${price}
</p>

</div>
`;
        }

        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('input', updateSummary);
            el.addEventListener('change', updateSummary);
        });

        /* ── DRAG & DROP ── */
        const zone = document.getElementById('uploadZone');
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.classList.add('dragover');
        });
        zone.addEventListener('dragleave', e => {
            e.preventDefault();
            zone.classList.remove('dragover');
        });
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('dragover');
            const input = document.getElementById('imageInput');
            input.files = e.dataTransfer.files;
            previewImage(input);
        });
        document.querySelector(
            '[name="stock"]'
        ).addEventListener(
            'input',
            function() {

                let stock = parseInt(this.value) || 0;

                let html = '';

                if (stock > 50) {

                    html =
                        '<span class="badge bg-success">🟢 High Stock</span>';

                } else if (stock > 10) {

                    html =
                        '<span class="badge bg-warning text-dark">🟡 Medium Stock</span>';

                } else {

                    html =
                        '<span class="badge bg-danger">🔴 Low Stock</span>';

                }

                document.getElementById(
                    'stockStatus'
                ).innerHTML = html;

            });

        /* ── DISCOUNT CALCULATOR ── */

        function updateDiscountPrice() {

            let price =
                parseFloat(
                    document.getElementById('price').value
                ) || 0;

            let discount =
                parseFloat(
                    document.getElementById('discount').value
                ) || 0;

            let finalPrice =
                price -
                (price * discount / 100);

            document.getElementById('finalPrice').innerHTML =
                '₹' + finalPrice.toFixed(2);
        }

        /* Price change */
        document.getElementById('price')
            .addEventListener(
                'input',
                updateDiscountPrice
            );

        /* Discount change */
        document.getElementById('discount')
            .addEventListener(
                'input',
                updateDiscountPrice
            );

        /* Initial load */
        updateDiscountPrice();
        /* ── SUBMIT FEEDBACK ── */
        document.getElementById('addForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding…';
            btn.disabled = true;
        });


        document.getElementById('discount')
            .addEventListener('wheel', function(e) {

                e.preventDefault();

            }, {
                passive: false
            });
    </script>

</body>

</html>