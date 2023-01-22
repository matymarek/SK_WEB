<?php
require 'bin/functions.php';
require_once 'bin/dbconnect.php';
head();
navbar();

const STATE_FORM_REQUESTED = 1;
const STATE_FORM_SENT = 2;
const STATE_PROCESSED = 3;
const RESULT_SUCCESS = 1;

const STATE_FORM_NEWPOST_SENT = 7;
const STATE_FORM_NEWPHOTO_SENT = 8;
const STATE_FORM_CHNGPASS_SENT = 9;

session_start();
$ui = isset($_SESSION['ui']);
login(getState(isset($_SESSION['logged'])));
if($ui){
   editWeb(getForm());
}
echo '
</body>
</html>
';