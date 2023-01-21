<?php
require 'functions.php';
require 'dbconnect.php';
head();
echo '<title>Galerie</title>';
navbar();
echo'
<h1>Snímky obrazovky z aplikace</h1>
<div class="gallery">
    <img class="gallery" src="img/tracking.png" alt="Domovská stánka" title="Domovská stánka">
    <img class="gallery" src="img/trackdetail.png" alt="Detail trasy" title="Detail trasy">
    <img class="gallery" src="img/mytracks.png" alt="Mé trasy" title="Mé trasy">
    <img class="gallery" src="img/navbar.png" alt="Navigační menu" title="Navigační menu">
    <img class="gallery" src="img/settings.png" alt="Nastavení aplikace" title="Nastavení aplikace">
</div>
</body>
</html>
';