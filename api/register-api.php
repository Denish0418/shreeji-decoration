<?php

require_once '../config/db.php';
include '../includes/functions.php';

$name = cleanInput($_POST['name']);
$email = cleanInput($_POST['email']);
$phone = cleanInput($_POST['phone']);

$password = password_hash(
$_POST['password'],
PASSWORD_DEFAULT
);

$check = mysqli_query($conn,
"SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($check) > 0){
    die("Email Already Exists");
}

$query = "INSERT INTO users(
name,
email,
phone,
password
)

VALUES(
'$name',
'$email',
'$phone',
'$password'
)";

mysqli_query($conn, $query);

header("Location: ../login.php");

?>