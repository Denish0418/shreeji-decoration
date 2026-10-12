<?php

session_start();

require_once '../config/db.php';

if(isset($_POST['email']) && isset($_POST['password'])){

    $email = mysqli_real_escape_string($conn,$_POST['email']);

    $password = $_POST['password'];

    $query = mysqli_query($conn,

    "SELECT * FROM users
    WHERE email='$email'
    AND role='admin'"

    );

    if(mysqli_num_rows($query) > 0){

        $admin = mysqli_fetch_assoc($query);

        // VERIFY HASH PASSWORD

        if(password_verify($password, $admin['password'])){

            $_SESSION['admin_id'] = $admin['id'];

            $_SESSION['admin_name'] = $admin['name'];

            header("Location: dashboard.php");

            exit();

        }else{

            echo "
            <script>
            alert('Wrong Password');
            window.location='login.php';
            </script>
            ";

        }

    }else{

        echo "
        <script>
        alert('Admin Not Found');
        window.location='login.php';
        </script>
        ";

    }

}else{

    header('Location: login.php');

}
?>