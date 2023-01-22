<?php
require 'bin/functions.php';
require_once 'bin/dbconnect.php';

head();
echo '<title>Galerie</title>';
navbar();
echo'
<h1>Fotogalerie</h1>
<div class="gallery">';

$photos = getPhotos();
for ($i = 0; $i < count($photos); $i++){
    photo($photos[$i]);
}

echo '
</div>
</body>
</html>
';

//až budou obrázky tak to doplácám