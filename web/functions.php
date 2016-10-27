<?php
session_start();

function getDBConnection() {
	if (empty($_SESSION['dbConnect'])){
		$host = "localhost";
		$user = "teamsanchez";
		$pass = "password";
		$db   = "sanchez";

		$r = mysql_connect($host, $user, $pass);

		if (!$r) {
			echo "Could not connect to server\n";
			trigger_error(mysql_error(), E_USER_ERROR);
			return NULL;
		}

		$_SESSION['dbConnect'] = &$r;
		mysql_select_db($db);
	}

	return $_SESSION['dbConnect'];
}
?>
