<?php

session_start();
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);

if (isset($_POST['status'])) {

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status']
    );

    mysqli_query(
        $conn,
        "UPDATE quote_requests
         SET status='$status'
         WHERE id=$id"
    );

    header(
        "Location:view-quote.php?id=$id"
    );

    exit;
}

$result = mysqli_query(
    $conn,
    "SELECT * FROM quote_requests
    WHERE id = $id
"
);

$quote = mysqli_fetch_assoc($result);

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
    <?php include 'includes/sidebar.php'; ?>

    <div style="margin-left:280px;padding:30px;">
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-body">

                <h2>

                    📨 Quote Details

                </h2>

                <p class="text-muted mb-0">

                    Customer inquiry information

                </p>

            </div>

        </div>
        <div class="card shadow-sm border-0">

            <div class="mb-3">

                <a href="quotes.php"
                    class="btn btn-secondary">

                    <a href="delete-quote.php?id=<?= $quote['id']; ?>"
                        class="btn btn-danger"
                        onclick="return confirm('Delete this quote?')">

                        Delete Quote

                    </a>

                </a>

            </div>

            <div class="card-body">

                <h4 class="mb-4">

                    Customer Information

                </h4>

                <div class="row">

                    <div class="col-md-6">

                        <p>

                            <strong>Name:</strong>

                            <?= $quote['name']; ?>

                        </p>

                        <p>

                            <strong>Phone:</strong>

                            <?= $quote['phone']; ?>

                        </p>

                        <p>

                            <strong>Email:</strong>

                            <?= $quote['email']; ?>

                        </p>

                        <p>

                            <strong>Inquiry Type:</strong>

                            <?= $quote['inquiry_type']; ?>

                        </p>

                    </div>

                    <div class="col-md-6">

                        <p>

                            <strong>Occasion:</strong>

                            <?= $quote['occasion']; ?>

                        </p>

                        <p>

                            <strong>Status:</strong>

                            <?php

                            $badge = 'bg-warning';

                            if ($quote['status'] == 'Contacted') {
                                $badge = 'bg-primary';
                            }

                            if ($quote['status'] == 'Completed') {
                                $badge = 'bg-success';
                            }

                            ?>

                            <span class="badge <?= $badge ?>">

                                <?= $quote['status']; ?>

                            </span>

                        </p>

                        <form method="POST">

                            <select
                                name="status"
                                class="form-select">

                                <option value="New">
                                    New
                                </option>

                                <option value="Contacted">
                                    Contacted
                                </option>

                                <option value="Completed">
                                    Completed
                                </option>

                            </select>

                            <button
                                type="submit"
                                class="btn btn-success mt-2">

                                Update Status

                            </button>

                        </form>

                        <p>

                            <strong>Date:</strong>

                            <?= date(
                                'd M Y h:i A',
                                strtotime($quote['created_at'])
                            ); ?>

                        </p>

                    </div>

                </div>

                <hr>

                <h5>

                    Message

                </h5>

                <div class="p-3 bg-light rounded">

                    <?= nl2br($quote['message']); ?>

                </div>

            </div>

        </div>
    </div>
</body>

</html>