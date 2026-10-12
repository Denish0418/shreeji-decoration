<?php

require_once '../config/db.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    echo json_encode([
        "success" => false,
        "message" => "Invalid Request"
    ]);

    exit;
}

$email = trim($_POST['email'] ?? '');
$otp   = trim($_POST['otp'] ?? '');

if ($email == '' || $otp == '') {

    echo json_encode([
        "success" => false,
        "message" => "Please enter OTP."
    ]);

    exit;
}

$result = mysqli_query(
    $conn,
    "SELECT * FROM customers
     WHERE email='".mysqli_real_escape_string($conn,$email)."'
     LIMIT 1"
);

if (mysqli_num_rows($result) == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Customer not found."
    ]);

    exit;
}

$customer = mysqli_fetch_assoc($result);

if ($customer['otp'] != $otp) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid OTP."
    ]);

    exit;
}

mysqli_query(
    $conn,
    "UPDATE customers
     SET
        otp=NULL,
        is_verified=1,
        last_login=NOW()
     WHERE id=".$customer['id']
);

$_SESSION['customer_logged_in'] = true;
$_SESSION['customer_id'] = $customer['id'];
$_SESSION['customer_name'] = $customer['name'];
$_SESSION['customer_phone'] = $customer['phone'];
$_SESSION['customer_email'] = $customer['email'];

echo json_encode([
    "success" => true,
    "message" => "Login Successful."
]);

?>