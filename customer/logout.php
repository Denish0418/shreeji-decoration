<?php

require_once '../includes/session.php';
$customerId = $_SESSION['customer_id'] ?? 0;

session_unset();
$_SESSION['clear_cart'] = $customerId;
session_destroy();

header("Location: ../index.php");

exit;

?>