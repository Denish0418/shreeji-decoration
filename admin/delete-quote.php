<?php

require_once '../config/db.php';

$id = (int)$_GET['id'];

mysqli_query(
$conn,
"DELETE FROM quote_requests
WHERE id=$id"
);

header(
"Location:quotes.php"
);