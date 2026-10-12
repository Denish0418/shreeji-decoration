<?php

require_once '../config/db.php';

$id = $_GET['id'];

$query = mysqli_query($conn,
"SELECT * FROM gallery WHERE id='$id'");

$data = mysqli_fetch_assoc($query);

/* UPDATE */

if(isset($_POST['update'])){

    $title = $_POST['title'];

    $category = $_POST['category'];

    /* IMAGE */

    if($_FILES['image']['name'] != ""){

        $old = $data['image'];

        unlink("../uploads/gallery/".$old);

        $image = time()."_".$_FILES['image']['name'];

        $tmp = $_FILES['image']['tmp_name'];

        move_uploaded_file(
            $tmp,
            "../uploads/gallery/".$image
        );

    }else{

        $image = $data['image'];

    }

    mysqli_query($conn,

    "UPDATE gallery SET

    title='$title',
    category='$category',
    image='$image'

    WHERE id='$id'

    ");

    header("Location: gallery.php");
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Gallery</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f8f5f2;
padding:40px;
font-family:Poppins,sans-serif;
}

.card-box{
background:#fff;
padding:40px;
border-radius:25px;
max-width:700px;
margin:auto;
}

.form-control{
height:55px;
border-radius:14px;
}

.btn-save{
background:#2d140a;
color:#fff;
height:55px;
border:none;
border-radius:14px;
padding:0 30px;
}

.preview{
width:120px;
height:120px;
object-fit:cover;
border-radius:15px;
margin-bottom:20px;
}

</style>

</head>

<body>

<div class="card-box">

<h2 class="fw-bold mb-4">
Edit Gallery Image
</h2>

<form method="POST"
enctype="multipart/form-data">

<img src="../uploads/gallery/<?php echo $data['image']; ?>"
class="preview">

<div class="mb-4">

<label class="mb-2">
Title
</label>

<input type="text"
name="title"
value="<?php echo $data['title']; ?>"
class="form-control">

</div>

<div class="mb-4">

<label class="mb-2">
Category
</label>

<input type="text"
name="category"
value="<?php echo $data['category']; ?>"
class="form-control">

</div>

<div class="mb-4">

<label class="mb-2">
Change Image
</label>

<input type="file"
name="image"
class="form-control">

</div>

<button class="btn-save"
name="update">

Update Image

</button>

</form>

</div>

</body>
</html>