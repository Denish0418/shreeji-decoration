<?php

session_start();


require_once '../config/db.php';

$_SESSION['products_seen'] = true;
$_SESSION['low_stock_count'] = 0;


if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: products.php?msg=deleted");
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");

?>
<!DOCTYPE html>
<html>

<head>

    <title>Products</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* ── exact copy from dashboard.php ── */
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

        /* ── stat card (same as dashboard) ── */
        .stat-card {
            background: #fff;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-card i {
            font-size: 38px;
            color: #7A2E0D;
        }

        /* ── products table card ── */
        .products-card {
            background: #fff;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }

        /* ── top bar ── */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

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
            font-family: Poppins, sans-serif;
            font-size: 14px;
            cursor: pointer;
        }

        .add-btn:hover {
            background: #7A2E0D;
            color: #fff;
        }

        /* ── search bar ── */
        .search-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
            align-items: center;
        }

        .search-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f5f2;
            border: 1.5px solid #ede8e3;
            border-radius: 14px;
            padding: 10px 16px;
            flex: 1;
            min-width: 200px;
        }

        .search-wrap i {
            color: #aaa;
        }

        .search-wrap input {
            border: none;
            background: transparent;
            outline: none;
            font-family: Poppins, sans-serif;
            font-size: 14px;
            color: #333;
            width: 100%;
        }

        .search-wrap input::placeholder {
            color: #bbb;
        }

        .filter-sel {
            background: #f8f5f2;
            border: 1.5px solid #ede8e3;
            border-radius: 14px;
            padding: 10px 16px;
            font-family: Poppins, sans-serif;
            font-size: 13px;
            color: #444;
            outline: none;
            cursor: pointer;
        }

        /* ── table ── */
        .table {
            margin: 0;
            vertical-align: middle;
        }

        .table thead th {
            border: none;
            padding: 16px 18px;
            font-size: 13px;
            font-weight: 600;
            color: #9CA3AF;
            background: #f8f5f2;
        }

        .table thead th:first-child {
            border-radius: 12px 0 0 12px;
        }

        .table thead th:last-child {
            border-radius: 0 12px 12px 0;
        }

        .table tbody td {
            padding: 14px 18px;
            border-color: #f3f0ec;
            font-size: 14px;
        }

        /* product image */
        .prod-img {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            object-fit: cover;
            background: #f3f0ec;
            border: 1px solid #ede8e3;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .prod-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .prod-name {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .prod-cat {
            font-size: 12px;
            color: #9CA3AF;
        }

        .price {
            font-size: 15px;
            font-weight: 700;
        }

        /* badges */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .in-stock {
            background: #DDEFE2;
            color: #2F6B3E;
        }

        .low-stock {
            background: #F6E7C8;
            color: #8B5E00;
        }

        .out-stock {
            background: #F5D7D7;
            color: #9B1C1C;
        }

        .type-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            background: #EFE3D3;
            color: #7A3B00;
        }

        .type-wholesale {
            background: #dde9ff;
            color: #1d4ed8;
        }

        /* action buttons */
        .action-group {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .view-btn {
            background: #DDEFE2;
            color: #2F6B3E;
        }

        .edit-btn {
            background: #EFE3D3;
            color: #7A3B00;
        }

        .delete-btn {
            background: #F5D7D7;
            color: #9B1C1C;
        }

        .action-btn:hover {
            opacity: 0.8;
            transform: scale(1.08);
        }

        /* empty */
        .empty-box {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-box i {
            font-size: 64px;
            color: #D1D5DB;
            display: block;
            margin-bottom: 16px;
        }

        .empty-box h5 {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .empty-box p {
            color: #9CA3AF;
            font-size: 14px;
        }

        /* alert */
        .success-alert {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #DDEFE2;
            color: #2F6B3E;
            padding: 14px 20px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        /* delete modal */
        .modal-backdrop-custom {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-backdrop-custom.show {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 24px;
            padding: 40px 32px;
            text-align: center;
            max-width: 380px;
            width: 90%;
        }

        .modal-del-icon {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: #F5D7D7;
            color: #9B1C1C;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .modal-box h5 {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .modal-box p {
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .modal-btns {
            display: flex;
            gap: 10px;
        }

        .btn-cancel {
            flex: 1;
            background: #f3f0ec;
            color: #374151;
            border: none;
            padding: 13px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            font-family: Poppins, sans-serif;
        }

        .btn-cancel:hover {
            background: #e8e4e0;
        }

        .btn-del-confirm {
            flex: 1;
            background: #9B1C1C;
            color: #fff;
            border: none;
            padding: 13px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            font-family: Poppins, sans-serif;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-del-confirm:hover {
            background: #7f1d1d;
            color: #fff;
        }

        /* row count */
        .row-count {
            font-size: 13px;
            color: #9CA3AF;
            margin-top: 16px;
        }
    </style>
</head>

<body>

    <!-- ══ SIDEBAR — exact same as dashboard.php ══ -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- ══ MAIN CONTENT ══ -->
    <div class="main-content">

        <!-- Top Bar -->
        <div class="top-bar">
            <div>
                <h2 class="fw-bold mb-1">Products</h2>
                <p class="text-muted mb-0" style="font-size:14px;">Manage retail &amp; wholesale products</p>
            </div>
            <a href="add-product.php" class="add-btn">
                <i class="bi bi-plus-lg"></i> Add Product
            </a>
        </div>

        <!-- Success Alert -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="success-alert">
                <i class="bi bi-check-circle-fill"></i>
                <?php
                if ($_GET['msg'] === 'deleted') echo 'Product deleted successfully.';
                elseif ($_GET['msg'] === 'added')   echo 'Product added successfully.';
                elseif ($_GET['msg'] === 'updated') echo 'Product updated successfully.';
                ?>
            </div>
        <?php endif; ?>

        <!-- Stat Cards — same style as dashboard -->
        <?php
        $all_rows  = [];
        $in_stock  = 0;
        $low_stock = 0;
        $out_stock = 0;
        $wholesale = 0;
        while ($r = mysqli_fetch_assoc($products)) {
            $all_rows[] = $r;
            $s = isset($r['stock']) ? (int)$r['stock'] : 0;
            if ($s > 10)     $in_stock++;
            elseif ($s > 0)  $low_stock++;
            else            $out_stock++;
            if (isset($r['type']) && strtolower($r['type']) === 'wholesale') $wholesale++;
        }
        $total = count($all_rows);
        ?>

        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <div class="stat-card">
                    <i class="bi bi-box"></i>
                    <h3 class="mt-3"><?= $total ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="stat-card">
                    <i class="bi bi-check-circle"></i>
                    <h3 class="mt-3"><?= $in_stock ?></h3>
                    <p>In Stock</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="stat-card">
                    <i class="bi bi-exclamation-circle"></i>
                    <h3 class="mt-3"><?= $low_stock ?></h3>
                    <p>Low Stock</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="stat-card">
                    <i class="bi bi-building"></i>
                    <h3 class="mt-3"><?= $wholesale ?></h3>
                    <p>Wholesale</p>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="products-card">

            <!-- Search & Filter -->
            <div class="search-bar">
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" placeholder="Search products by name or category…" oninput="filterTable()" />
                </div>
                <select class="filter-sel" id="categoryFilter" onchange="filterTable()">
                    <option value="">All Categories</option>
                    <option value="Wedding">Wedding</option>
                    <option value="Festival">Festival</option>
                    <option value="Birthday">Birthday</option>
                    <option value="Puja">Puja</option>
                    <option value="Home Decor">Home Decor</option>
                    <option value="Navratri">Navratri</option>
                </select>
                <select class="filter-sel" id="typeFilter" onchange="filterTable()">
                    <option value="">All Types</option>
                    <option value="Retail">Retail</option>
                    <option value="Wholesale">Wholesale</option>
                </select>
                <select class="filter-sel" id="stockFilter" onchange="filterTable()">
                    <option value="">All Stock</option>
                    <option value="in">In Stock</option>
                    <option value="low">Low Stock</option>
                    <option value="out">Out of Stock</option>
                </select>
            </div>

            <?php if ($total > 0): ?>

                <div class="table-responsive">
                    <table class="table" id="productsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Stock</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_rows as $i => $row):
                                $stock = isset($row['stock']) ? (int)$row['stock'] : 0;
                                $type  = isset($row['type'])  ? $row['type'] : 'Retail';
                                $cat   = isset($row['category']) ? $row['category'] : '—';

                                if ($stock > 10) {
                                    $sClass = 'in-stock';
                                    $sLabel = $stock . ' In Stock';
                                    $sStk = 'in';
                                } elseif ($stock > 0) {
                                    $sClass = 'low-stock';
                                    $sLabel = $stock . ' Left';
                                    $sStk = 'low';
                                } else {
                                    $sClass = 'out-stock';
                                    $sLabel = '0 Stock';
                                    $sStk = 'out';
                                }

                                $tClass = (strtolower($type) === 'wholesale') ? 'type-badge type-wholesale' : 'type-badge';
                            ?>
                                <tr data-name="<?= strtolower(htmlspecialchars($row['name'])) ?>"
                                    data-cat="<?= htmlspecialchars($cat) ?>"
                                    data-type="<?= htmlspecialchars($type) ?>"
                                    data-stk="<?= $sStk ?>">

                                    <td style="color:#9CA3AF; font-weight:600;"><?= $i + 1 ?></td>

                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="prod-img">
                                                <?php if (!empty($row['image'])): ?>
                                                    <img src="../uploads/products/<?= htmlspecialchars($row['image']) ?>" alt="" />
                                                <?php else: ?>
                                                    🎁
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="prod-name"><?= htmlspecialchars($row['name']) ?></div>
                                                <div class="prod-cat"><?= htmlspecialchars($cat) ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td><span class="price">
                                            <?php

                                            if ($row['discount'] > 0) {

                                                $discounted =
                                                    $row['price'] -
                                                    ($row['price'] * $row['discount'] / 100);

                                            ?>

                                                ₹<?= number_format($discounted) ?>

                                                <br>

                                                <small style="text-decoration:line-through;color:#999;">
                                                    ₹<?= number_format($row['price']) ?>
                                                </small>

                                            <?php } else { ?>

                                                ₹<?= number_format($row['price']) ?>

                                            <?php } ?>

                                        </span></td>

                                    <td>
                                        <span style="
        background:#DCFCE7;
        color:#166534;
        padding:6px 12px;
        border-radius:20px;
        font-size:12px;
        font-weight:600;
    ">
                                            <?php echo $row['discount']; ?>% OFF

                                        </span>

                                        <br>

                                        <small>

                                            Final:
                                            ₹<?= number_format(
                                                    $row['price'] -
                                                        ($row['price'] * $row['discount'] / 100)
                                                ); ?>

                                        </small>
                                    </td>

                                    <td>
                                        <span class="stock-badge <?= $sClass ?>">
                                            <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                                            <?= $sLabel ?>
                                        </span>
                                    </td>

                                    <td><span class="<?= $tClass ?>"><?= htmlspecialchars($type) ?></span></td>

                                    <td style="color:#6B7280;"><?= htmlspecialchars($cat) ?></td>

                                    <td>

                                        <div class="action-group">

                                            <!-- VIEW -->
                                            <a href="view-product.php?id=<?php echo $row['id']; ?>"
                                                class="action-btn view-btn"
                                                title="View Product">

                                                <i class="bi bi-eye"></i>

                                            </a>

                                            <!-- EDIT -->
                                            <a href="edit-product.php?id=<?php echo $row['id']; ?>"
                                                class="action-btn edit-btn"
                                                title="Edit Product">

                                                <i class="bi bi-pencil"></i>

                                            </a>

                                            <!-- DELETE -->
                                            <a href="javascript:void(0)"
                                                class="action-btn delete-btn"
                                                onclick="openDelete(
    <?php echo $row['id']; ?>,
    '<?php echo addslashes($row['name']); ?>'
    )">

                                                <i class="bi bi-trash"></i>

                                            </a>

                                        </div>

                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <p class="row-count" id="rowCount">
                    Showing <?= $total ?> of <?= $total ?> products
                </p>

            <?php else: ?>

                <div class="empty-box">
                    <i class="bi bi-box-seam"></i>
                    <h5>No Products Found</h5>
                    <p>Start by adding your first product.</p>
                    <a href="add-product.php" class="add-btn mt-3" style="display:inline-flex;">
                        <i class="bi bi-plus-lg"></i> Add Product
                    </a>
                </div>

            <?php endif; ?>
        </div>

    </div>
    <!-- /main-content -->

    <!-- ══ DELETE MODAL ══ -->
    <div class="modal-backdrop-custom" id="deleteModal">
        <div class="modal-box">
            <div class="modal-del-icon"><i class="bi bi-trash3-fill"></i></div>
            <h5>Delete Product?</h5>
            <p id="deleteMsg">This action cannot be undone.</p>
            <div class="modal-btns">
                <button class="btn-cancel" onclick="closeDelete()">Cancel</button>
                <a href="#" class="btn-del-confirm" id="confirmDelBtn">
                    <i class="bi bi-trash3-fill"></i> Delete
                </a>
            </div>
        </div>
    </div>

    <script>
        /* ── FILTER ── */
        function filterTable() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            const cat = document.getElementById('categoryFilter').value;
            const type = document.getElementById('typeFilter').value;
            const stk = document.getElementById('stockFilter').value;
            const rows = document.querySelectorAll('#productsTable tbody tr');
            let visible = 0;

            rows.forEach(r => {
                const show =
                    (!q || r.dataset.name.includes(q) || r.dataset.cat.toLowerCase().includes(q)) &&
                    (!cat || r.dataset.cat === cat) &&
                    (!type || r.dataset.type === type) &&
                    (!stk || r.dataset.stk === stk);

                r.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            document.getElementById('rowCount').textContent =
                `Showing ${visible} of ${rows.length} products`;
        }

        /* ── DELETE MODAL ── */
        function openDelete(id, name) {
            document.getElementById('deleteMsg').textContent =
                `Are you sure you want to delete "${name}"? This action cannot be undone.`;
            document.getElementById('confirmDelBtn').href = `products.php?delete=${id}`;
            document.getElementById('deleteModal').classList.add('show');
        }

        function closeDelete() {
            document.getElementById('deleteModal').classList.remove('show');
        }

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDelete();
        });

        /* ── AUTO HIDE ALERT ── */
        const alert = document.querySelector('.success-alert');
        if (alert) setTimeout(() => {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    </script>

</body>

</html>