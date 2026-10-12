<?php

require_once '../config/db.php';

$name = mysqli_real_escape_string($conn,$_POST['name']);
$phone = mysqli_real_escape_string($conn,$_POST['phone']);
$email = mysqli_real_escape_string($conn,$_POST['email']);
$inquiry = mysqli_real_escape_string($conn,$_POST['inquiry_type']);
$occasion = mysqli_real_escape_string($conn,$_POST['occasion']);
$message = mysqli_real_escape_string($conn,$_POST['message']);

mysqli_query(
$conn,
"INSERT INTO quote_requests
(
name,
phone,
email,
inquiry_type,
occasion,
message
)
VALUES
(
'$name',
'$phone',
'$email',
'$inquiry',
'$occasion',
'$message'
)"
);

header("Location: ../index.php?quote=success#contact");
exit;