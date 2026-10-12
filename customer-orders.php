<?php

require_once 'includes/session.php';

if (!isset($_SESSION['customer_logged_in'])) {

    header("Location: retail.php");

    exit;

}

header("Location: my-orders.php?customer_id=" . $_SESSION['customer_id']);

exit;

?>