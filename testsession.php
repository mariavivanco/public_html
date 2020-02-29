<html><body>

<?php
session_start();

foreach($_SESSION as $name => $value) {
	echo "SESSION $name: $value<br>";
}
?>
</body></html>
