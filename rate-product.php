<?php

require_once 'config/db.php';

$order_id = (int)($_GET['order']);

$order =
    mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT * FROM orders
WHERE id='$order_id'"
        )
    );

if (!$order) {
    die("Order Not Found");
}

/* Get product id from order_items */

$orderNumber = $order['order_id'];

$orderItem = mysqli_fetch_assoc(

    mysqli_query(

        $conn,

        "SELECT *
         FROM order_items
         WHERE order_id='$orderNumber'
         LIMIT 1"

    )

);

if(!$orderItem){

    die("Product not found for this order.");

}

$product_id = (int)$orderItem['product_id'];

$already =
    mysqli_num_rows(
        mysqli_query(
            $conn,
            "SELECT id
             FROM product_reviews
             WHERE order_id='".$order['order_id']."'"
        )
    );


if (isset($_POST['submit'])) {

    $rating = (int)$_POST['rating'];

    $review =
        mysqli_real_escape_string(
            $conn,
            $_POST['review']
        );

   mysqli_query(
    $conn,
    "INSERT INTO product_reviews
(
order_id,
product_id,
customer_name,
phone,
rating,
review,
status
)
VALUES
(
'".$order['order_id']."',
'$product_id',
'" . $order['customer_name'] . "',
'" . $order['phone'] . "',
'$rating',
'$review',
'Approved'
)"
);

    header(
        "Location: my-orders.php?phone=" . $order['phone']
    );

    exit;
}

?>
<!DOCTYPE html>
<html>

<head>

    <title>Rate Product</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body style="background:#FDF8F0">

    <div class="container py-5">

        <div class="card border-0 shadow-lg p-5 rounded-4">

            <h2 class="fw-bold mb-4 text-center">

                ⭐ Rate Your Experience

            </h2>

            <p class="text-center text-muted">

                Tell us about your shopping experience

            </p>
            <?php if ($already > 0) { ?>

                <div class="alert alert-success">

                    You already submitted a review for this order.

                </div>

                <a
                    href="my-orders.php?phone=<?= $order['phone'] ?>"
                    class="btn btn-dark">

                    Back To Orders

                </a>

            <?php exit;
            } ?>
            <form method="POST">

                <label class="form-label">

                    Rating

                </label>

                <select
                    name="rating"
                    class="form-control"
                    required>

                    <option value="5">⭐⭐⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="1">⭐</option>

                </select>

                <br>

                <label class="form-label">

                    Review

                </label>

                <textarea
                    name="review"
                    class="form-control"
                    required>

</textarea>

                <br>

                <button
                    name="submit"
                    class="btn btn-dark">

                    Submit Review

                </button>

            </form>

        </div>

    </div>

</body>

</html>