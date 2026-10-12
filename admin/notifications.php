<?php

session_start();
require_once '../config/db.php';

if(!isset($_SESSION['admin_id'])){
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Notifications</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

    <h2 class="mb-4">Notifications</h2>

    <div class="list-group">

        <?php

        $orders = mysqli_query(
            $conn,
            "SELECT id
            FROM orders
            WHERE admin_seen='0'
            ORDER BY id DESC"
        );

        while($row = mysqli_fetch_assoc($orders)){
        ?>

        <a href="orders.php"
           class="list-group-item list-group-item-action">

            🛒 New Order #<?= $row['id']; ?>

        </a>

        <?php } ?>

        <?php

        $wholesale = mysqli_query(
            $conn,
            "SELECT id,business_name
            FROM wholesale_requests
            WHERE admin_seen='0'
            ORDER BY id DESC"
        );

        while($row = mysqli_fetch_assoc($wholesale)){
        ?>

        <a href="wholesale.php"
           class="list-group-item list-group-item-action">

            🏢 New Wholesale Request

        </a>

        <?php } ?>

        <?php

        $quotes = mysqli_query(
            $conn,
            "SELECT id,name
            FROM quote_requests
            WHERE admin_seen='0'
            ORDER BY id DESC"
        );

        while($row = mysqli_fetch_assoc($quotes)){
        ?>

        <a href="quotes.php"
           class="list-group-item list-group-item-action">

            💬 New Quote Request

        </a>

        <?php } ?>

    </div>

</div>

</body>
</html>