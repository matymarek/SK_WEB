<?php
require 'bin/autoload.php';
require_once 'bin/dbconnect.php';
$f = new Functions();
$f->head("Galerie");
$f->navbar();

$f->photos();


//až budou obrázky tak to doplácám