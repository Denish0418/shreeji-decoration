<?php

require_once '../config/db.php';

$id = $_GET['id'];

$get = mysqli_query($conn,
"SELECT * FROM gallery WHERE id='$id'");

$data = mysqli_fetch_assoc($get);

$image = $data['image'];

unlink("../uploads/gallery/".$image);

mysqli_query($conn,
"DELETE FROM gallery WHERE id='$id'");

header("Location: gallery.php");

?>