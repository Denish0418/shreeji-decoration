<?php

require_once '../config/db.php';

$id = $_GET['id'];

$order_query = mysqli_query(
    $conn,
    "SELECT * FROM orders WHERE id='$id'"
);

$order = mysqli_fetch_assoc($order_query);

if (isset($_POST['status'])) {

    $checkOrder =
        mysqli_fetch_assoc(
            mysqli_query(
                $conn,
                "SELECT status
                 FROM orders
                 WHERE id='$id'"
            )
        );

    $lockedStatuses = [
        'Return Requested',
        'Return Approved',
        'Return Rejected'
    ];

    if (!in_array($checkOrder['status'], $lockedStatuses)) {
        $status = mysqli_real_escape_string(
            $conn,
            $_POST['status']
        );

        mysqli_query(
            $conn,
            "UPDATE orders
             SET status='$status'
             WHERE id='$id'"
        );
    }

    header("Location:view-order.php?id=$id");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>View Order</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f5f2;
            font-family: Poppins, sans-serif;
            padding: 40px;
        }

        .order-card {
            background: #fff;
            border-radius: 25px;
            padding: 40px;
            max-width: 900px;
            margin: auto;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .order-id {
            background: #7A2E0D;
            color: #fff;
            padding: 10px 18px;
            border-radius: 12px;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }

        .info-box {
            background: #faf7f4;
            padding: 20px;
            border-radius: 18px;
        }

        .label {
            color: #888;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .value {
            font-size: 18px;
            font-weight: 600;
        }

        .status-pending {
            color: #8B5E00;
        }

        .status-completed {
            color: #2F6B3E;
        }

        .status-cancelled {
            color: #9B1C1C;
        }

        .products {
            margin-top: 30px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px;
            border: 1px solid #eee;
            border-radius: 16px;
            margin-bottom: 15px;
        }

        .product-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-image {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
        }

        .total-box {
            margin-top: 30px;
            background: #7A2E0D;
            color: #fff;
            padding: 25px;
            border-radius: 20px;
            text-align: right;
        }

        .total-box h3 {
            margin: 0;
        }

        .back-btn {
            margin-top: 30px;
        }
    </style>

</head>

<body>

    <div class="order-card">

        <div class="heading">

            <h1>Order Details</h1>

            <div class="order-id">
                Order #<?php echo $order['id']; ?>
            </div>

        </div>

        <div class="info-grid">

            <div class="info-box">
                <div class="label">Customer Name</div>
                <div class="value">
                    <?php echo $order['customer_name']; ?>
                </div>
            </div>

            <div class="info-box">
                <div class="label">Phone Number</div>
                <div class="value">
                    <?php echo $order['phone']; ?>
                </div>
            </div>

            <div class="info-box">
                <div class="label">Email Address</div>
                <div class="value">
                    <?php echo $order['email']; ?>
                </div>
            </div>

            <div class="info-box">
                <div class="label">Payment Method</div>
                <div class="value">
                    <?php echo $order['payment_method']; ?>
                </div>
            </div>

            <div class="info-box">

                <div class="label">
                    Order Status
                </div>

                <div class="value mb-3">

                    <span
                        class="status-<?= strtolower($order['status']); ?>">

                        <?= $order['status']; ?>

                    </span>

                </div>
                <?php

                $lockedStatuses = [
                    'Return Requested',
                    'Return Approved',
                    'Return Rejected'
                ];

                ?>
                <?php if (!in_array($order['status'], $lockedStatuses)) { ?>

                    <form method="POST">

                        <select
                            name="status"
                            class="form-select">

                            <option
                                value="Pending"
                                <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>

                                Pending

                            </option>

                            <option
                                value="Completed"
                                <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>

                                Completed

                            </option>

                            <option
                                value="Cancelled"
                                <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>

                                Cancelled

                            </option>

                        </select>

                        <button
                            type="submit"
                            class="btn btn-success mt-3 w-100">

                            Update Status

                        </button>
                    <?php } else { ?>

                        <div class="alert alert-warning mt-3">

                            This order is under return process.
                            Status can no longer be changed.

                        </div>

                    <?php } ?>
                    </form>

            </div>

            <div class="info-box">
                <div class="label">Order Date & Time</div>
                <div class="value">
                    <?php echo date(
                        "d M Y h:i A",
                        strtotime($order['created_at'])
                    ); ?>
                </div>
            </div>

            <div class="info-box">
                <div class="label">Order ID</div>
                <div class="value">
                    #<?php echo $order['id']; ?>
                </div>
            </div>

            <div class="info-box">
                <div class="label">Total Amount</div>
                <div class="value">
                    ₹<?php echo number_format($order['total_amount']); ?>
                </div>
            </div>

        </div>

        <div class="info-box mb-4">

            <div class="label">Delivery Address</div>

            <div class="value">
                <?php echo $order['address']; ?>
            </div>

        </div>

        <h3 class="mb-4">Ordered Products</h3>

        <div class="products">

            <?php

            $cart = json_decode($order['products'] ?? '[]', true);

            if (!is_array($cart)) {
                $cart = [];
            }

            if (!empty($cart)) {

                foreach ($cart as $item) {

            ?>



                    <div class="product-item">

                        <div class="product-left">

                            <?php if (!empty($item['image'])) { ?>

                                <img src="../uploads/products/<?php echo $item['image']; ?>"
                                    class="product-image">

                            <?php } else { ?>

                                <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                    📦
                                </div>

                            <?php } ?>

                            <div>

                                <h5 class="mb-1">
                                    <?php echo $item['name']; ?>
                                </h5>

                                <p class="text-muted mb-0">
                                    Qty : <?php echo $item['qty']; ?>

                                    <br>

                                    Price : ₹<?php echo $item['price']; ?>

                                    <br>

                                    Subtotal :
                                    ₹<?php echo $item['price'] * $item['qty']; ?>
                                </p>

                            </div>

                        </div>

                        <h5>
                            ₹<?php echo $item['price']; ?>
                        </h5>

                    </div>

            <?php

                }
            }

            ?>

        </div>

        <div class="total-box">

            <h3>
                Total Amount :
                ₹<?php echo $order['total_amount']; ?>
            </h3>

        </div>

        <div class="d-flex gap-3 mt-4">

            <button
                onclick="printParcelReceipt()"
                class="btn btn-success flex-fill">

                <i class="bi bi-printer"></i>
                Print Parcel Receipt

            </button>

            <a href="orders.php"
                class="btn btn-dark flex-fill">

                Back to Orders

            </a>

        </div>

    </div>

    <div id="parcelReceipt" style="display:none;">

        <div style="
width:450px;
padding:20px;
font-family:Arial;
border:2px solid #000;
">

            <h1 style="
text-align:center;
margin:0;
">
                SHREEJI DECORATION
            </h1>

            <p style="
text-align:center;
margin:5px 0;
">
                Ahmedabad, Gujarat
            </p>

            <hr>

            <h2 style="text-align:center;">
                ORDER #<?php echo $order['id']; ?>
            </h2>

            <div style="text-align:center">

                <img
                    src="barcode.php?order=<?php echo $order['id']; ?>"
                    style="
width:100%;
height:80px;
">

            </div>

            <hr>

            <b>Customer Name :</b>

            <?php echo $order['customer_name']; ?>

            <br><br>

            <b>Phone :</b>

            <?php echo $order['phone']; ?>

            <br><br>

            <b>Email :</b>

            <?php echo $order['email']; ?>

            <br><br>

            <b>Address :</b>

            <?php echo $order['address']; ?>

            <br><br>

            <b>Payment Method :</b>

            <?php echo $order['payment_method']; ?>

            <hr>

            <h3>
                PRODUCT DETAILS
            </h3>

            <table
                border="1"
                width="100%"
                cellpadding="5"
                style="border-collapse:collapse;">

                <tr>

                    <th>Product</th>

                    <th>Qty</th>

                    <th>Amount</th>

                </tr>

                <?php

                if (!empty($cart)) {

                    foreach ($cart as $item) {

                ?>

                        <tr>

                            <td>
                                <?php echo $item['name']; ?>
                            </td>

                            <td>
                                <?php echo $item['qty']; ?>
                            </td>

                            <td>
                                ₹<?php echo number_format($item['price'] * $item['qty']); ?>
                            </td>

                        </tr>

                <?php

                    }
                }

                ?>

            </table>

            <hr>

            <h2>

                TOTAL BILL :
                ₹<?php echo number_format($order['total_amount']); ?>

            </h2>

            <hr>

            <b>Date :</b>

            <?php echo date('d M Y'); ?>

            <br>

            <b>Time :</b>

            <?php echo date('h:i A'); ?>

        </div>

    </div>
    <script>
        function printParcelReceipt() {

            let receipt =
                document.getElementById(
                    "parcelReceipt"
                ).innerHTML;

            let win =
                window.open(
                    "",
                    "",
                    "width=600,height=800"
                );

            win.document.write(`
    <html>
    <head>

    <title>Parcel Receipt</title>

    <style>

    body{
        font-family:Arial;
        padding:20px;
    }

    h2{
        text-align:center;
    }

    </style>

    </head>

    <body>

    ${receipt}

    </body>

    </html>
    `);

            win.document.close();

            win.print();
        }
    </script>

</body>

</html>