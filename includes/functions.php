<?php

function cleanInput($data){
    return htmlspecialchars(trim($data));
}

function redirect($url){
    header("Location: $url");
    exit();
}

?>