<?php

require_once 'config/db.php';

$order_id = $_GET['order'];

$order =
    mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT *
        FROM orders
        WHERE id='$order_id'"
        )
    );

$settings =
    mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT *
        FROM settings
        LIMIT 1"
        )
    );

$days =
    $settings['return_policy_days'];

$order_time =
    strtotime(
        $order['order_date']
    );

$today =
    time();

$diff =
    ($today - $order_time)
    / (60 * 60 * 24);

if ($diff > $days) {
    die("Return period expired.");
}

if (isset($_POST['submit'])) {
    $order_id = $_POST['order_id'];
    $customer_name =
        $order['customer_name'];

    $product_name =
        $order['products'];
    $reason = $_POST['reason'];

    $check =
        mysqli_query(
            $conn,
            "SELECT id
    FROM return_requests
    WHERE order_id='$order_id'"
        );

    if (mysqli_num_rows($check) > 0) {
        die("Return request already submitted.");
    }
    mysqli_query($conn, "
        INSERT INTO return_requests
        (
            order_id,
            customer_name,
            product_name,
            reason
        )
        VALUES
        (
            '$order_id',
            '$customer_name',
            '$product_name',
            '$reason'
        )
    ");

    mysqli_query(
        $conn,
        "UPDATE orders
     SET status='Return Requested'
     WHERE id='$order_id'"
    );

    echo "<script>
    alert('Return Request Submitted');
    window.location='retail.php';
    </script>";
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Return Product</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <style>
        body {
            background: #FDF8F0;
            font-family: 'Poppins', sans-serif;
        }

        .return-wrapper {
            max-width: 800px;
            margin: 60px auto;
        }

        .return-card {
            background: #fff;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .page-title {
            text-align: center;
            font-weight: 700;
            color: #4A2200;
            margin-bottom: 30px;
        }

        .order-info {
            background: #FFF6E8;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .order-info p {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .reason-box {
            min-height: 140px;
            resize: none;
            border-radius: 15px;
        }

        .submit-btn {
            background: #4A2200;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
        }

        .submit-btn:hover {
            background: #6a3000;
        }

        .policy-box {
            background: #EAFBF0;
            color: #198754;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="return-wrapper">

            <div class="return-card">

                <h2 class="page-title">

                    <i class="bi bi-arrow-counterclockwise"></i>
                    Return Request

                </h2>

                <div class="policy-box">

                    Return policy active for
                    <strong>
                        <?= $days ?>
                    </strong>
                    days from order date.

                </div>

                <div class="order-info">

                    <p>
                        <strong>Order ID :</strong>
                        #<?= $order['id']; ?>
                    </p>

                    <p>
                        <strong>Customer :</strong>
                        <?= $order['customer_name']; ?>
                    </p>

                    <p>
                        <strong>Phone :</strong>
                        <?= $order['phone']; ?>
                    </p>

                    <p>
                        <strong>Order Date :</strong>
                        <?= date('d M Y', strtotime($order['order_date'])) ?>
                    </p>

                </div>

                <form method="POST">

                    <input
                        type="hidden"
                        name="order_id"
                        value="<?= $order['id']; ?>">

                    <div class="mb-3">

                        <label class="form-label">

                            Reason for Return

                        </label>

                        <textarea
                            name="reason"
                            class="form-control reason-box"
                            placeholder="Explain why you want to return this order..."
                            required></textarea>

                    </div>

                    <button
                        type="submit"
                        name="submit"
                        class="submit-btn">

                        <i class="bi bi-send"></i>
                        Submit Return Request

                    </button>

                    <a href="javascript:history.back()"
                        class="btn btn-outline-secondary ms-2">

                        Cancel

                    </a>

                </form>

            </div>

        </div>

    </div>

</body>

</html>