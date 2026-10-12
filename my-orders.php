<?php

require_once 'includes/session.php';

if (!isset($_SESSION['customer_logged_in'])) {

    header("Location: retail.php");

    exit;
}

require_once 'config/db.php';

$customer_id = (int)$_SESSION['customer_id'];

$orders =
    mysqli_query(
        $conn,
        "SELECT *
        FROM orders
        WHERE customer_id='$customer_id'
        ORDER BY id DESC"
    );
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>My Orders</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: #FDF8F0;
        }

        .order-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            margin-bottom: 25px;
        }

        .order-header {
            background: #2B1600;
            color: white;
            padding: 15px 20px;
        }

        .order-body {
            padding: 25px;
        }

        .status-badge {
            padding: 8px 14px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1.5px solid #eadfd2;
            color: #2B1600;
            text-decoration: none;
            font-size: .88rem;
            font-weight: 700;
            padding: 10px 20px 10px 16px;
            border-radius: 50px;
            margin-bottom: 22px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .05);
            transition: all .25s ease;
        }

        .back-btn:hover {
            background: linear-gradient(135deg, #C9A063, #B8863F);
            border-color: transparent;
            color: #fff;
            transform: translateX(-3px);
            box-shadow: 0 8px 22px rgba(232, 119, 46, .3);
        }

        .back-btn i {
            font-size: 1rem;
            transition: transform .25s ease;
        }

        .back-btn:hover i {
            transform: translateX(-3px);
        }
    </style>

</head>

<body style="background:#FDF8F0">

    <div class="container py-5">

        <a href="index.php" id="ordersBackBtn" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back
        </a>

        <h1 class="mb-4 text-center">

            My Orders

        </h1>
        <?php

        if (mysqli_num_rows($orders) == 0) {

            echo '

<div class="alert alert-danger">

No orders have been placed yet.

</div>';
        }

        ?>
        <?php while ($row = mysqli_fetch_assoc($orders)) { ?>
            

            <?php

$reviewExists = mysqli_num_rows(

    mysqli_query(

        $conn,

        "SELECT id
         FROM product_reviews
         WHERE order_id='".$row['order_id']."'
         LIMIT 1"

    )

);


            ?>

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h5>

                                Order #
                                <?= $row['order_id']; ?>

                            </h5>

                            <p class="mb-1">

                                Customer :
                                <?= $row['customer_name']; ?>

                            </p>

                            <p class="mb-1">

                                Phone :
                                <?= $row['phone']; ?>

                            </p>

                            <p class="mb-1">

                                Amount :
                                ₹<?= number_format($row['total_amount']); ?>

                            </p>

                        </div>

                        <div>

                            <?php

                            $statusColor = 'secondary';

                            if ($row['status'] == 'Pending')
                                $statusColor = 'warning';

                            if ($row['status'] == 'Delivered')
                                $statusColor = 'success';

                            if ($row['status'] == 'Cancelled')
                                $statusColor = 'danger';

                            ?>

                            <span class="badge bg-<?= $statusColor ?>">

                                <?= $row['status']; ?>

                            </span>

                        </div>

                    </div>

                    <hr>

                    <a href="invoice.php?id=<?= $row['id'] ?>"
                        class="btn btn-dark">

                        Invoice

                    </a>

                    <?php

                    $status = strtolower(trim($row['status']));

                    if (
                        $status == 'completed' ||
                        $status == 'delivered'
                    ) {
                    ?>

                        <a href="return-product.php?order=<?= $row['id'] ?>"
                            class="btn btn-warning">

                            ↩ Return Product

                        </a>
                        <?php if ($reviewExists == 0) { ?>

                            <a
                                href="rate-product.php?order=<?= $row['id'] ?>"
                                class="btn btn-success">

                                ⭐ Rate Product

                            </a>

                        <?php } else { ?>

                            <button
                                class="btn btn-secondary"
                                disabled>

                                ✅ Review Submitted

                            </button>

                        <?php } ?>

                    <?php } else { ?>

                        <span class="btn btn-secondary">
                            Order Not Completed
                        </span>

                    <?php } ?>

                </div>

            </div>

        <?php } ?>
    </div>

    <script>
        document.getElementById('ordersBackBtn').addEventListener('click', function(e) {
            let cameFromSameSite =
                document.referrer && document.referrer.indexOf(window.location.origin) === 0;

            if (cameFromSameSite && window.history.length > 1) {
                e.preventDefault();
                window.history.back();
            }
            // otherwise, let the normal href="index.php" take over
        });
    </script>

</body>

</html>