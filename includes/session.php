<?php

/*
|--------------------------------------------------------------------------
| Shreeji Decoration
|--------------------------------------------------------------------------
| Common Session File
| Include this file in every customer/admin page
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}