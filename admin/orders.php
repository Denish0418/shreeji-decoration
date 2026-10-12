<?php

session_start();


require_once '../config/db.php';

mysqli_query(
    $conn,
    "UPDATE orders
SET admin_seen='1'
WHERE admin_seen='0'"
);

$_SESSION['orders_seen'] = true;
$_SESSION['order_count'] = 0;

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

$orders = mysqli_query(
    $conn,
    "SELECT * FROM orders ORDER BY id DESC"
);

$pending_orders =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT * FROM orders
WHERE status='Pending'"
        )
    );

$completed_orders =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT * FROM orders
WHERE status='Completed'"
        )
    );

$revenue =
    mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT SUM(total_amount) AS total
FROM orders"
        )
    );

$total_revenue =
    $revenue['total'] ?? 0;

?>
<!DOCTYPE html>
<html>

<head>

    <title>Orders</title>

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

        /* cards */
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

        .orders-card {
            background: #fff;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }

        /* top bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* search */
        .search-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f5f2;
            border: 1.5px solid #ede8e3;
            border-radius: 14px;
            padding: 10px 16px;
            margin-bottom: 24px;
        }

        .search-wrap input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
        }

        /* table */
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

        .table tbody td {
            padding: 14px 18px;
            border-color: #f3f0ec;
            font-size: 14px;
        }

        /* status */
        .status-badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .pending {
            background: #F6E7C8;
            color: #8B5E00;
        }

        .return-requested {
            background: #FFF3CD;
            color: #856404;
        }

        .return-approved {
            background: #D4EDDA;
            color: #155724;
        }

        .return-rejected {
            background: #F8D7DA;
            color: #721C24;
        }

        .completed {
            background: #DDEFE2;
            color: #2F6B3E;
        }

        .cancelled {
            background: #F5D7D7;
            color: #9B1C1C;
        }

        /* buttons */
        .action-btn {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .view-btn {
            background: #DDEFE2;
            color: #2F6B3E;
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
                <h2 class="fw-bold mb-1">Orders</h2>
                <p class="text-muted mb-0" style="font-size:14px;">
                    Manage customer orders
                </p>
            </div>

        </div>

        <!-- STATS -->

        <div class="row g-4 mb-4">

            <div class="col-lg-4">

                <div class="stat-card">

                    <i class="bi bi-cart-check"></i>

                    <h3 class="mt-3">
                        <?php echo mysqli_num_rows($orders); ?>
                    </h3>

                    <p>Total Orders</p>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="stat-card">

                    <i class="bi bi-currency-rupee"></i>

                    <h3 class="mt-3">

                        ₹<?php echo number_format($total_revenue); ?>

                    </h3>

                    <p>Total Revenue</p>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="stat-card">

                    <i class="bi bi-clock-history"></i>

                    <h3 class="mt-3">
                        <?php echo $pending_orders; ?>
                    </h3>

                    <p>Pending Orders</p>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="stat-card">

                    <i class="bi bi-check-circle"></i>

                    <h3 class="mt-3">
                        <?php echo $completed_orders; ?>
                    </h3>

                    <p>Completed Orders</p>

                </div>

            </div>

        </div>

        <!-- TABLE -->

        <div class="orders-card">

            <div class="search-wrap">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search Orders..."
                    onkeyup="searchOrders()">

            </div>

            <div class="table-responsive">

                <table class="table">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = mysqli_fetch_assoc($orders)) { ?>

                            <tr>

                                <td>
                                    #<?php echo $row['id']; ?>
                                </td>

                                <td>
                                    <?php echo $row['customer_name']; ?>
                                </td>

                                <td>
                                    ₹<?php echo $row['total_amount']; ?>
                                </td>
                                <td>

                                    <?= date(
                                        'd M Y',
                                        strtotime($row['created_at'])
                                    ); ?>

                                </td>

                                <td>

                                    <?php

                                    $statusClass =
                                        strtolower(
                                            str_replace(' ', '-', $row['status'])
                                        );

                                    ?>

                                    <span class="status-badge <?= $statusClass ?>">

                                        <?php echo $row['status']; ?>

                                    </span>

                                </td>

                                <td>

                                    <a href="view-order.php?id=<?php echo $row['id']; ?>"
                                        class="action-btn view-btn">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                    <a href="delete-order.php?id=<?php echo $row['id']; ?>"
                                        class="action-btn delete-btn"
                                        onclick="return confirm('Delete this order?')">

                                        <i class="bi bi-trash"></i>

                                    </a>

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script>
        function searchOrders() {

            let input =
                document.getElementById("searchInput")
                .value.toLowerCase();

            let rows =
                document.querySelectorAll(
                    "tbody tr"
                );

            rows.forEach(row => {

                let text =
                    row.innerText.toLowerCase();

                row.style.display =
                    text.includes(input) ?
                    "" :
                    "none";

            });

        }
    </script>

</body>

</html>