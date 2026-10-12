<?php

require_once "../config/db.php";

header("Content-Type: application/json");

if(!isset($_GET['id']))
{
    echo json_encode([
        "status"=>false,
        "message"=>"Product id missing"
    ]);
    exit;
}

$product_id = (int)$_GET['id'];

$productQuery = mysqli_query(
    $conn,
    "SELECT *
     FROM products
     WHERE id='$product_id'
     LIMIT 1"
);

if(mysqli_num_rows($productQuery)==0)
{
    echo json_encode([
        "status"=>false,
        "message"=>"Product not found"
    ]);
    exit;
}

$product = mysqli_fetch_assoc($productQuery);

$reviewQuery = mysqli_query(

    $conn,

    "SELECT
        rating,
        review,
        customer_name,
        DATE(created_at) AS review_date
     FROM product_reviews
     WHERE product_id='$product_id'
     AND status='Approved'
     ORDER BY id DESC"

);

$reviews=[];

$totalRating=0;

while($row=mysqli_fetch_assoc($reviewQuery))
{
    $reviews[]=$row;

    $totalRating+=$row['rating'];
}

$average=0;

if(count($reviews)>0)
{
    $average=round($totalRating/count($reviews),1);
}

echo json_encode([

    "status"=>true,

    "product"=>$product,

    "reviews"=>$reviews,

    "average"=>$average

]);