<?php
require_once '../config/db.php';

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$product = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Product</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f8f5f1;
            font-family:Poppins;
            padding:40px;
        }

        .card{
            max-width:700px;
            margin:auto;
            border:none;
            border-radius:20px;
            padding:30px;
        }

        img{
            width:100%;
            height:350px;
            object-fit:cover;
            border-radius:15px;
        }

    </style>

</head>

<body>

<div class="card shadow">

    <img src="../uploads/products/<?php echo $product['image']; ?>">

    <h2 class="mt-4">
        <?php echo $product['name']; ?>
    </h2>

    <h4 class="text-success">
        ₹<?php echo $product['price']; ?>
    </h4>

    <p>
        <?php echo $product['description']; ?>
    </p>

    <p>
        <b>Category:</b>
        <?php echo $product['category']; ?>
    </p>

    <a href="products.php" class="btn btn-dark">
        Back
    </a>

</div>

</body>
</html>