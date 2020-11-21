<?php
$connection =
mysqli_connect('localhost','root','root','auction');
// See the "errors" folder for details...
if (!$connection) {
echo "Can't establish connection to the database";
}

?>
