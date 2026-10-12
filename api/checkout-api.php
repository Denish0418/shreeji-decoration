<?php

require_once '../config/db.php';
include '../includes/session.php';

$user_id = $_SESSION['user_id'];

$total = 0;

$cart = mysqli_query($conn,

"SELECT * FROM cart
JOIN products
ON cart.product_id = products.id

WHERE cart.user_id='$user_id'"

);

while($item = mysqli_fetch_assoc($cart)){

    $total += $item['price'];

}

mysqli_query($conn,

"INSERT INTO orders(
user_id,
total,
status
)

VALUES(
'$user_id',
'$total',
'Pending'
)"

);

mysqli_query($conn,

"DELETE FROM cart
WHERE user_id='$user_id'"

);

header("Location: ../order-history.php");

?>