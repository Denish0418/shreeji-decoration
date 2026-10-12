<?php

require_once 'config/db.php';
require_once 'includes/session.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$orders = mysqli_query($conn,

"SELECT * FROM orders
WHERE user_id='$user_id'

ORDER BY id DESC"

);

?>

<!DOCTYPE html>
<html>
<head>

<title>Order History</title>

<link rel="stylesheet" href="style.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<h2 class="mb-4">

My Orders

</h2>

<?php while($order = mysqli_fetch_assoc($orders)){ ?>

<div class="card p-4 shadow-sm border-0 mb-3">

<h5>

Order #<?php echo $order['id']; ?>

</h5>

<p>

Total:
₹<?php echo $order['total_amount']; ?>

</p>

<p>

Status:
<span class="badge bg-warning">

<?php echo $order['status']; ?>

</span>

</p>

</div>

<?php } ?>

</div>

</body>
</html>