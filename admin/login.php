<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Admin Login
</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body{
background:#F5F7FB;
height:100vh;
display:flex;
align-items:center;
justify-content:center;
font-family:Poppins,sans-serif;
}

.login-card{
width:420px;
background:#fff;
padding:40px;
border-radius:24px;
box-shadow:0 20px 60px rgba(0,0,0,.08);
}

.logo{
font-size:1.6rem;
font-weight:700;
margin-bottom:30px;
text-align:center;
}

.form-control{
height:52px;
border-radius:14px;
margin-bottom:18px;
}

.login-btn{
width:100%;
height:52px;
border:none;
border-radius:14px;
background:linear-gradient(135deg,#C9A063,#B8863F);
font-weight:700;
}

</style>

</head>

<body>

<div class="login-card">

<div class="logo">

<i class="bi bi-flower1"></i>

Admin Panel

</div>

<form action="login-api.php"
method="POST">

<input type="email"
name="email"
class="form-control"
placeholder="Admin Email"
required>

<input type="password"
name="password"
class="form-control"
placeholder="Password"
required>

<button type="submit"
class="login-btn">

Login

</button>

</form>

</div>

</body>
</html>