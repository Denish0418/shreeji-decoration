<?php

session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {

    header("Location: index.php");

    exit;
}

?>

<?php

// TOTAL PRODUCTS
$product_q = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM products"
);

$product_data = mysqli_fetch_assoc($product_q);

$total_products = $product_data['total'];


// TOTAL ORDERS
$order_q = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM orders"
);

$order_data = mysqli_fetch_assoc($order_q);

$total_orders = $order_data['total'];


// TOTAL USERS
$user_q = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM users"
);

$user_data = mysqli_fetch_assoc($user_q);

$total_users = $user_data['total'];

// TOTAL WHOLESALE REQUESTS

$wholesale_q = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total
    FROM wholesale_requests"
);

$wholesale_data =
    mysqli_fetch_assoc($wholesale_q);

$total_wholesale =
    $wholesale_data['total'];

// TOTAL REVENUE
$revenue_q = mysqli_query(
    $conn,
    "SELECT SUM(total_amount) as total FROM orders"
);

$revenue_data = mysqli_fetch_assoc($revenue_q);

$total_revenue = $revenue_data['total'];

if (!$total_revenue) {
    $total_revenue = 0;
}


// LOW STOCK NOTIFICATIONS

$notification_q = mysqli_query(
    $conn,
    "SELECT * FROM products WHERE stock <= 25"
);

$notification_count = mysqli_num_rows($notification_q);


// PRODUCT COUNTER
if (
    !isset($_SESSION['products_seen'])
    ||
    $_SESSION['products_seen'] == false
) {
    if (!isset($_SESSION['products_seen'])) {
        $_SESSION['products_seen'] = false;
    }

    if ($_SESSION['products_seen'] == false) {
        $_SESSION['low_stock_count'] = $notification_count;
    }
}


// IF NEW LOW STOCK COMES
if ($notification_count > 0) {

    $_SESSION['products_seen'] = false;
}

// TOTAL QUOTES

$quote_q = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total
    FROM quote_requests"
);

$quote_data =
    mysqli_fetch_assoc($quote_q);

$total_quotes =
    $quote_data['total'];

$newOrders =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id FROM orders
WHERE admin_seen='0'"
        )
    );

$newWholesale =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id FROM wholesale_requests
WHERE admin_seen='0'"
        )
    );

$newQuotes =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id FROM quote_requests
WHERE admin_seen='0'"
        )
    );

$totalNotifications =
    $newOrders +
    $newWholesale +
    $newQuotes;

?>

<!DOCTYPE html>
<html>

<head>

    <title>Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

        .main-content {
            margin-left: 270px;
            padding: 40px;
        }

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
    </style>

</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="fw-bold mb-1">
                    Welcome,
                    <?php echo $_SESSION['admin_name']; ?>
                </h2>

                <p class="text-muted">
                    Admin Dashboard Overview
                </p>

            </div>

            <div style="position:relative;">



                <button
                    id="notificationBtn"
                    style="
width:55px;
height:55px;
border:none;
border-radius:50%;
background:#fff;
font-size:22px;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
cursor:pointer;
">

                    <i class="bi bi-bell"></i>

                </button>
                <div id="notificationBox"
                    style="
display:none;
position:absolute;
top:65px;
right:0;
width:320px;
background:#fff;
border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,.15);
z-index:999;
overflow:hidden;
">

                    <div style="
padding:15px;
font-weight:600;
border-bottom:1px solid #eee;
background:#f8f8f8;
">

                        Notifications

                    </div>

                    <?php

                    $ordersNotif = mysqli_query(
                        $conn,
                        "SELECT id
FROM orders
WHERE admin_seen='0'
ORDER BY id DESC
LIMIT 5"
                    );

                    while ($n = mysqli_fetch_assoc($ordersNotif)) {
                    ?>

                        <a href="orders.php"
                            style="
display:block;
padding:12px 15px;
text-decoration:none;
color:#333;
border-bottom:1px solid #eee;
">

                            🛒 New Order #<?= $n['id']; ?>

                        </a>

                    <?php } ?>

                    <?php

                    $wholeNotif = mysqli_query(
                        $conn,
                        "SELECT id
FROM wholesale_requests
WHERE admin_seen='0'
ORDER BY id DESC
LIMIT 5"
                    );

                    while ($n = mysqli_fetch_assoc($wholeNotif)) {
                    ?>

                        <a href="wholesale.php"
                            style="
display:block;
padding:12px 15px;
text-decoration:none;
color:#333;
border-bottom:1px solid #eee;
">

                            🏢 New Wholesale Request

                        </a>

                    <?php } ?>

                    <?php

                    $quoteNotif = mysqli_query(
                        $conn,
                        "SELECT id
FROM quote_requests
WHERE admin_seen='0'
ORDER BY id DESC
LIMIT 5"
                    );

                    while ($n = mysqli_fetch_assoc($quoteNotif)) {
                    ?>

                        <a href="quotes.php"
                            style="
display:block;
padding:12px 15px;
text-decoration:none;
color:#333;
border-bottom:1px solid #eee;
">

                            💬 New Quote Request

                        </a>

                    <?php } ?>

                </div>



                <?php if ($totalNotifications > 0) { ?>

                    <span style="
position:absolute;
top:-5px;
right:-5px;
background:red;
color:white;
width:24px;
height:24px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-size:12px;
font-weight:700;
">

                        <?php echo $totalNotifications; ?>

                    </span>

                <?php } ?>

            </div>

        </div>

        <div class="row g-4">

            <div class="col-lg-3">

                <div class="stat-card">

                    <i class="bi bi-box"></i>

                    <h3 class="mt-3">
                        <?php echo $total_products; ?>
                    </h3>

                    <p>
                        Total Products
                    </p>

                </div>

            </div>

            <div class="col-lg-3">

                <div class="stat-card">

                    <i class="bi bi-cart"></i>

                    <h3 class="mt-3">
                        <?php echo $total_orders; ?>
                    </h3>

                    <p>
                        Orders
                    </p>

                </div>

            </div>

            <div class="col-lg-3">

                <div class="stat-card">

                    <i class="bi bi-people"></i>

                    <h3 class="mt-3">
                        <?php echo $total_users; ?>
                    </h3>

                    <p>
                        Users
                    </p>

                </div>

            </div>

            <div class="col-lg-3">

                <div class="stat-card">

                    <i class="bi bi-currency-rupee"></i>

                    <h3 class="mt-3">
                        ₹<?php echo number_format($total_revenue); ?>
                    </h3>

                    <p>
                        Revenue
                    </p>

                </div>

            </div>

            <div class="col-lg-3">

                <div class="stat-card">

                    <i class="bi bi-building"></i>

                    <h3 class="mt-3">

                        <?php echo $total_wholesale; ?>

                    </h3>

                    <p>

                        Wholesale Requests

                    </p>

                </div>

            </div>

        </div>

        <?php if ($notification_count > 0) { ?>

            <div class="alert alert-warning rounded-4 p-4 border-0 mb-4">

                <h5 class="fw-bold mb-3">

                    ⚠ Low Stock Alerts

                </h5>

                <?php

                while ($notify = mysqli_fetch_assoc($notification_q)) {

                ?>

                    <div class="mb-2">

                        <b>

                            <?php echo $notify['name']; ?>

                        </b>

                        is running low.

                        Only

                        <b>

                            <?php echo $notify['stock']; ?>

                        </b>

                        left in stock.

                    </div>

                <?php } ?>

            </div>

        <?php } ?>
        <div class="card border-0 shadow-sm rounded-4 mt-5">

            <div class="card-body p-4">

                <h4 class="fw-bold mb-4">
                    Recent Orders
                </h4>

                <table class="table">

                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>

                    <?php

                    $recent_orders = mysqli_query(
                        $conn,

                        "SELECT * FROM orders
ORDER BY id DESC
LIMIT 5"
                    );

                    while ($order = mysqli_fetch_assoc($recent_orders)) {

                    ?>

                        <tr>

                            <td>

                                #<?php echo $order['id']; ?>

                            </td>

                            <td>

                                <?php echo $order['customer_name']; ?>

                            </td>

                            <td>

                                <?php

                                $badge = "bg-warning";

                                if ($order['status'] == "Completed") {
                                    $badge = "bg-success";
                                }

                                if ($order['status'] == "Cancelled") {
                                    $badge = "bg-danger";
                                }

                                ?>

                                <span class="badge <?= $badge; ?>">

                                    <?= $order['status']; ?>

                                </span>

                            </td>

                            <td>

                                ₹<?php echo number_format($order['total_amount']); ?>

                            </td>

                        </tr>

                    <?php } ?>

                </table>

            </div>

        </div>

    </div>

    <script>
        const bell =
            document.getElementById(
                'notificationBtn'
            );

        const box =
            document.getElementById(
                'notificationBox'
            );

        bell.addEventListener(
            'click',
            function() {

                if (box.style.display === 'block') {
                    box.style.display = 'none';
                } else {
                    box.style.display = 'block';
                }

            }
        );

        document.addEventListener(
            'click',
            function(e) {

                if (
                    !bell.contains(e.target) &&
                    !box.contains(e.target)
                ) {
                    box.style.display = 'none';
                }

            }
        );
    </script>

</body>

</html>