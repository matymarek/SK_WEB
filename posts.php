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
    post($posts[$i]);
    post($posts[$i]);
    post($posts[$i]);
    post($posts[$i]);
    post($posts[$i]);
    post($posts[$i]);

    //čistě pro prezentaci, pak bude stačit jedno volání :D
}
echo'
                </div>
            </div>
        </body>
    </html>
';