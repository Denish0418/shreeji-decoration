<?php

require_once 'config/db.php';

$newPassword = password_hash(
'admin123',
PASSWORD_DEFAULT
);

mysqli_query($conn,

"UPDATE users SET

password='$newPassword',

role='admin'

WHERE email='admin@gmail.com'"

);

echo "Admin Password Reset Success";

?>