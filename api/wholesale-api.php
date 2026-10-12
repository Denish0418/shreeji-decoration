<?php

require_once '../config/db.php';

$business_name = mysqli_real_escape_string($conn,$_POST['business_name']);
$owner_name = mysqli_real_escape_string($conn,$_POST['owner_name']);
$phone = mysqli_real_escape_string($conn,$_POST['phone']);
$email = mysqli_real_escape_string($conn,$_POST['email']);
$city = mysqli_real_escape_string($conn,$_POST['city']);
$message = mysqli_real_escape_string($conn,$_POST['message']);

mysqli_query(
$conn,
"INSERT INTO wholesale_requests
(
business_name,
owner_name,
phone,
email,
city,
message
)
VALUES
(
'$business_name',
'$owner_name',
'$phone',
'$email',
'$city',
'$message'
)"
);

header("Location: ../wholesale.php?success=1");
exit;