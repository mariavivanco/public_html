<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'goddard');
define('DB_PASSWORD', '030620');
define('DB_NAME', 'userInfoSB');

$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
?>
