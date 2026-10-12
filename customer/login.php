<?php

require_once '../includes/session.php';

header("Content-Type: application/json");

if(empty($_POST['mobile'])){

    echo json_encode([
        "success"=>false,
        "message"=>"Mobile missing"
    ]);

    exit;
}

$_SESSION['customer_mobile']=$_POST['mobile'];

echo json_encode([
    "success"=>true
]);