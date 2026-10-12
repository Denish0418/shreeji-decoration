<?php

session_start();

require_once "../config/db.php";

if(!isset($_SESSION['admin_id'])){

header("Location: index.php");

exit;

}
if(isset($_POST['save'])){

$title=mysqli_real_escape_string($conn,$_POST['title']);

$subtitle=mysqli_real_escape_string($conn,$_POST['subtitle']);

$content=mysqli_real_escape_string($conn,$_POST['content']);

$since=mysqli_real_escape_string($conn,$_POST['since_year']);

$image="";

$image2="";

if(!empty($_FILES['image']['name'])){

$image=time()."_".$_FILES['image']['name'];

move_uploaded_file(

$_FILES['image']['tmp_name'],

"../uploads/site-content/".$image

);

mysqli_query(

$conn,

"UPDATE site_content
SET image='$image'
WHERE page_name='about'"

);

}

if(!empty($_FILES['image2']['name'])){

$image2=time()."_2_".$_FILES['image2']['name'];

move_uploaded_file(

$_FILES['image2']['tmp_name'],

"../uploads/site-content/".$image2

);

mysqli_query(

$conn,

"UPDATE site_content
SET image2='$image2'
WHERE page_name='about'"

);

}

mysqli_query(

$conn,

"UPDATE site_content

SET

title='$title',

subtitle='$subtitle',

content='$content',

since_year='$since'

WHERE page_name='about'"

);

header("Location: about-content.php");

exit;

}
$query=mysqli_query(

$conn,

"SELECT * FROM site_content
WHERE page_name='about'
LIMIT 1"

);

$about=mysqli_fetch_assoc($query);

?>
<!DOCTYPE html>

<html>

<head>

<title>

About Us CMS

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    body {
        background: #f8f5f2;
        font-family: Poppins, sans-serif;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 270px;
        height: 100vh;
        background: #2d140a;
        padding: 30px;
        color: #fff;
        overflow-y: auto;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin-top: 40px;
    }

    .sidebar ul li {
        margin-bottom: 10px;
    }

    .sidebar ul li a {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: #fff;
        padding: 14px 18px;
        border-radius: 14px;
    }

    .sidebar ul li a:hover {
        background: #7A2E0D;
    }

    .sidebar ul li a.active {
        background: #7A2E0D;
    }

    .main-content {
        margin-left: 270px;
        padding: 40px;
    }
</style>

</head>

<body>

<?php include "includes/sidebar.php"; ?>

<div
class="main-content p-4">

<h2 class="mb-4">

About Us Content

</h2>

<form
method="POST"
enctype="multipart/form-data">

<div class="card">

<div class="card-body">

<div class="mb-3">

<label>

Main Heading

</label>

<input
type="text"
name="title"
class="form-control"
value="<?= htmlspecialchars($about['title']) ?>">

</div>

<div class="mb-3">

<label>

Highlight Heading

</label>

<textarea
name="subtitle"
class="form-control"
rows="3"><?= htmlspecialchars($about['subtitle']) ?></textarea>

</div>

<div class="mb-3">

<label>

Description

</label>

<textarea
name="content"
rows="8"
class="form-control"><?= htmlspecialchars($about['content']) ?></textarea>

</div>

<div class="mb-3">

<label>

Since Year

</label>

<input
type="text"
name="since_year"
class="form-control"
value="<?= htmlspecialchars($about['since_year']) ?>">

</div>

<div class="mb-3">

<label>

Main Image

</label>

<input
type="file"
name="image"
class="form-control">

</div>

<div class="mb-3">

<label>

Second Image

</label>

<input
type="file"
name="image2"
class="form-control">

</div>

<button
class="btn btn-success"
name="save">

Save Changes

</button>

</div>

</div>

</form>

</div>

</body>

</html>