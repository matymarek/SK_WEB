<?php
require 'bin/functions.php';
require 'bin/dbconnect.php';
head();
echo '<title>Příspěvky</title>';
navbar();
echo'
    <h1>Co se u nás děje?</h1>
    <div class="contentContainer">
        <div class="content">';
$posts = getPosts();
for ($i = 0; $i < count($posts); $i++){
    post($posts[$i]);
}
echo'
                </div>
            </div>
        </body>
    </html>
';

// stejně dynamicky jako v galerii by šly přidat obrázky k příspěvkům
// -> obrázek do složky, např. 'postimgs', a do db příspěvků přidat 'post_img' a 'img_alt'