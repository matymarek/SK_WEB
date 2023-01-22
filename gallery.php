<?php
require 'bin/functions.php';
head();
echo '<title>Galerie</title>';
navbar();
echo'
<h1>Snímky obrazovky z aplikace</h1>
<div class="gallery">
    <img class="gallery" src="img/1.png" alt="Něco" title="Něco">
    <img class="gallery" src="img/2.png" alt="Něco" title="Něco">
    <img class="gallery" src="img/3.png" alt="Zase něco" title="Zase něco">
    <img class="gallery" src="img/4.png" alt="A znovu" title="A znovu">
    <img class="gallery" src="img/5.png" alt="A naposled" title="A naposled">
</div>
</body>
</html>
';

//až budou obrázky tak to splácám