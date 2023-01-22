<?php
require 'bin/functions.php';
require_once 'bin/dbconnect.php';
head();
navbar();

const STATE_FORM_REQUESTED = 1;
const STATE_FORM_SENT = 2;
const STATE_PROCESSED = 3;
const RESULT_SUCCESS = 1;

session_start();
$ui = isset($_SESSION['ui']);
login(getState(isset($_SESSION['logged'])));
if($ui){






    //insert admin page here :D







}
echo '
</body>
</html>
';