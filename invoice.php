<?php

require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid Invoice");
}

$order =
    mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT * FROM orders
WHERE id='$id'"
        )
    );
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Invoice</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #FDF8F0;
            font-family: 'Poppins', sans-serif;
        }

        .invoice-card {
            background: white;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .08);
        }

        .logo-title {
            font-size: 32px;
            font-weight: 700;
            color: #A67C52;
        }

        .invoice-label {
            font-weight: 600;
            color: #555;
        }

        .total-box {
            background: #FFF5E6;
            padding: 20px;
            border-radius: 15px;
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="invoice-card">

            <div class="text-center mb-4">

                <div class="logo-title">

                    🎉 Shreeji Decoration

                </div>

                <p>

                    Premium Event Decoration Services

                </p>

            </div>

            <hr>

            <div class="row">

                <div class="col-md-6">

                    <p>

                        <span class="invoice-label">
                            Invoice No:
                        </span>

                        <?= $order['order_id']; ?>

                    </p>

                    <p>

                        <span class="invoice-label">
                            Customer:
                        </span>

                        <?= $order['customer_name']; ?>

                    </p>

                    <p>

                        <span class="invoice-label">
                            Phone:
                        </span>

                        <?= $order['phone']; ?>

                    </p>

                </div>

                <div class="col-md-6 text-md-end">

                    <p>

                        <span class="invoice-label">
                            Order Date:
                        </span>

                        <?= $order['order_date']; ?>

                    </p>

                    <p>

                        <span class="invoice-label">
                            Status:
                        </span>

                        <?= $order['status']; ?>

                    </p>

                </div>

            </div>

            <hr>

            <h5 class="mb-3">

                Ordered Products

            </h5>

            <?php

            $products =
                json_decode(
                    $order['products'],
                    true
                );

            ?>

            <table class="table">

                <thead>

                    <tr>

                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($products as $item) { ?>

                        <tr>

                            <td>
                                <?= $item['name']; ?>
                            </td>

                            <td>
                                <?= $item['qty']; ?>
                            </td>

                            <td>
                                ₹<?= $item['price']; ?>
                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

            <hr>

            <div class="total-box">

                <p>

                    Delivery Charge :
                    ₹<?= $order['delivery_charge']; ?>

                </p>

                <h3>

                    Total :
                    ₹<?= number_format($order['total_amount']); ?>

                </h3>

            </div>

            <div class="mt-4 text-center">

                <button
                    onclick="window.print()"
                    class="btn btn-dark">

                    🖨 Print Invoice

                </button>

            </div>

        </div>

    </div>

</body>

</html>