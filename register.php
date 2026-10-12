<!DOCTYPE html>
<html>
<head>

<title>Register</title>

<link rel="stylesheet" href="style.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card p-4 shadow-lg border-0 rounded-4">

<h2 class="text-center mb-4">

Register

</h2>

<form action="api/register-api.php"
method="POST">

<div class="mb-3">

<label>Name</label>

<input type="text"
name="name"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Email</label>

<input type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Phone</label>

<input type="text"
name="phone"
class="form-control">

</div>

<div class="mb-3">

<label>Password</label>

<input type="password"
name="password"
class="form-control"
required>

</div>

<button class="btn btn-dark w-100">

Create Account

</button>

</form>

</div>

</div>

</div>

</div>

</body>
</html>