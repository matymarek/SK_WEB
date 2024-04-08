<?php
require 'bin/autoload.php';
require 'bin/dbconnect.php';
$f = new Functions();
$f->head("Příspěvky");
$f->navbar();

$f->posts();


// stejně dynamicky jako v galerii by šly přidat obrázky k příspěvkům
// -> obrázek do složky, např. 'postimgs', a do db příspěvků přidat 'post_img' a 'img_alt'