<?php

require_once 'includes/session.php';

require_once 'config/db.php';

$name = $_POST['name'] ;

$phone = $_POST['phone'];

$email = $_POST['email'];

$address = $_POST['address'];

$payment = $_POST['payment_method'];

$cart = json_decode(
    $_COOKIE['shoppingCart'],
    true
);

if (!$cart) {

    echo "Cart Empty";

    exit;
}

$total = 0;

foreach ($cart as $item) {

    $total += $item['price'] * $item['qty'];
}
$order_id =
    'ORD' .
    date('Ymd') .
    rand(1000, 9999);

$order_date =
    date('Y-m-d H:i:s');

$delivery_charge =
    $_POST['delivery_charge'] ;

$products = json_encode($cart);

mysqli_query(
    $conn,

    "INSERT INTO orders(

order_id,
customer_name,
phone,
email,
address,
payment_method,
products,
total_amount,
delivery_charge,
order_date,
status

)

VALUES(

'$order_id',
'$name',
'$phone',
'$email',
'$address',
'$payment',
'$products',
'$total',
'$delivery_charge',
'$order_date',
'Pending'

)"

);

// NEW ORDER NOTIFICATION COUNTER
// NEW ORDER NOTIFICATION COUNTER

if (!isset($_SESSION['new_order_count'])) {

    $_SESSION['new_order_count'] = 0;
}

$_SESSION['new_order_count']++;


// SHOW ORDER NOTIFICATION
$_SESSION['orders_seen'] = false;
setcookie(
    "shoppingCart",
    "",
    time() - 3600,
    "/"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Order Success</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #FDF8F0;
            font-family: Poppins;
        }

        .success-box {
            background: white;
            padding: 60px;
            border-radius: 30px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .08);
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-6">

                <div class="success-box">

                    <h1 class="mb-4">
                        🎉 Order Placed Successfully
                        <p class="mb-4">

                            Your Order ID:

                            <strong>

                                <?= $order_id ?>

                            </strong>

                        </p>
                    </h1>

                    <p class="mb-4">

                        Thank you for shopping with
                        Shreeji Decoration.

                    </p>

                    <a href="retail.php"
                        class="btn btn-dark px-4 py-2 rounded-pill">

                        Continue Shopping

                    </a>

                </div>

            </div>

        </div>

    </div>

</body>

</html>