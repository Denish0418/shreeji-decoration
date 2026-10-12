<?php

require_once '../config/db.php';

$id = $_GET['id'];

$get = mysqli_query($conn,
"SELECT * FROM products WHERE id='$id'");

$product = mysqli_fetch_assoc($get);

$image = $product['image'];

unlink("../uploads/".$image);

mysqli_query($conn,
"DELETE FROM products WHERE id='$id'");

header("Location: products.php");

?>