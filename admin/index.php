<?php
session_start();

require_once '../config/db.php';


$error = "";

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);

    $query = mysqli_query($conn,
    "SELECT * FROM users
    WHERE email='$email'
    AND role='admin'");

    if(mysqli_num_rows($query) > 0){

        $admin = mysqli_fetch_assoc($query);

        if(password_verify($password, $admin['password'])){

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: dashboard.php");
            exit();

        }else{
            $error = "Invalid Password";
        }

    }else{
        $error = "Admin Not Found";
    }

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Login</title>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body{
    background:#f8f5f2;
    font-family:Poppins,sans-serif;
}

.login-card{
    border:none;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 10px 40px rgba(0,0,0,0.08);
}

.left-side{
    background:linear-gradient(135deg,#7A2E0D,#A14A1C);
    color:#fff;
    padding:60px;
}

.right-side{
    padding:60px;
}

.admin-logo{
    width:90px;
    height:90px;
    border-radius:50%;
    background:rgba(255,255,255,0.1);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:38px;
    margin-bottom:25px;
}

.form-control{
    height:55px;
    border-radius:14px;
    border:1px solid #ddd;
}

.login-btn{
    width:100%;
    height:55px;
    border:none;
    border-radius:14px;
    background:#7A2E0D;
    color:#fff;
    font-weight:600;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="card login-card">

<div class="row g-0">

<div class="col-lg-6">

<div class="left-side h-100 d-flex flex-column justify-content-center">

<div class="admin-logo">
<i class="bi bi-flower1"></i>
</div>

<h1 class="fw-bold mb-3">
Shreeji Decoration
</h1>

<h4 class="mb-4">
Admin Panel
</h4>

<p class="text-light opacity-75">

Manage products, orders,
wholesale inquiries,
users and website content.

</p>

</div>

</div>

<div class="col-lg-6">

<div class="right-side">

<h2 class="fw-bold mb-4">
Admin Login
</h2>

<?php if($error != ""){ ?>

<div class="alert alert-danger">
<?php echo $error; ?>
</div>

<?php } ?>

<form method="POST">

<div class="mb-4">

<label class="mb-2">
Email Address
</label>

<input type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-4">

<label class="mb-2">
Password
</label>

<input type="password"
name="password"
class="form-control"
required>

</div>

<button type="submit"
name="login"
class="login-btn">

Login to Dashboard

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

</body>
</html>