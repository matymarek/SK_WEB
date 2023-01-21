<?php
function head(){
    echo('
<!doctype html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
</head>');
}
function navbar(){
    echo'<nav>
        <ul class="navbar">
            <li><a class="navbar" href="index.php">Domů</a></li>
            <li><a class="navbar" href="downloads.php">Příspěvky</a></li>
            <li><a class="navbar" href="gallery.php">Fotogalerie</a></li>
            <li><a class="navbar" href="about.php">O nás</a></li>
            <li><a class="navbar" href="login.php">Přihlásit</a></li>
        </ul>
    </nav>';
}