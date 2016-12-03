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

function goToPage($page) {
	header("Location: " . getPageURL($page));
}

function getPageURL($page) {
	$host  = $_SERVER['HTTP_HOST'];
	$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = $page;
	return "http://$host$uri/$extra";
}

function getItemURL($pid) {
	return getPageURL("product.php") . "?pid=$pid";
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function beginTransaction(){
    mysql_query("BEGIN");
}

function commitTransaction(){
    mysql_query("COMMIT");
}

function rollbackTransaction(){
    mysql_query("ROLLBACK");
}

function checkError(&$rs, &$commitErr) {
	if (!$rs) {
		array_push($commitErr, mysql_error());
		return 1;
	}

	return 0;
}

function addStatesDropdown($selected) {
	$states = array(
		"AL" => "Alabama", "AK" => "Alaska", "AZ" => "Arizona", 
		"AR" => "Arkansas", "CA" => "California", "CO" => "Colorado",
		"CT" => "Connecticut", "DE" => "Delaware", "DC" => "District Of Columbia", 
		"FL" => "Florida", "GA" => "Georgia", "HI" => "Hawaii", 
		"ID" => "Idaho", "IL" => "Illinois", "IN" => "Indiana", 
		"IA" => "Iowa", "KS" => "Kansas", "KY" => "Kentucky", 
		"LA" => "Louisiana", "ME" => "Maine", "MD" => "Maryland", 
		"MA" => "Massachusetts", "MI" => "Michigan", "MN" => "Minnesota", 
		"MS" => "Mississippi", "MO" => "Missouri", "MT" => "Montana",
		"NE" => "Nebraska", "NV" => "Nevada", "NH" => "New Hampshire",
		"NJ" => "New Jersey", "NM" => "New Mexico", "NY" => "New York",
		"NC" => "North Carolina", "ND" => "North Dakota", "OH" => "Ohio",
		"OK" => "Oklahoma", "OR" => "Oregon", "PA" => "Pennsylvania",
		"RI" => "Rhode Island", "SC" => "South Carolina", "SD" => "South Dakota", 
		"TN" => "Tennessee", "TX" => "Texas", "UT" => "Utah", 
		"VT" => "Vermont", "VA" => "Virginia", "WA" => "Washington",
		"WV" => "West Virginia", "WI" => "Wisconsin", "WY" => "Wyoming"
	 );

	echo '<select name="state">';
	foreach ($states as $abbr => $name) {
		echo '<option value="' . $abbr . '"';
		if (!empty($selected) && strcmp($selected, $abbr) == 0) {
			echo ' selected="selected"';
		}
		echo '>' . $name . '</option>';
	}
	echo '</select>';
}

function getURLParameter($name) {
	$val = filter_input( INPUT_GET, $name, FILTER_SANITIZE_URL );
	if ($val == NULL || $val == False) {
		$val == NULL;
	}

	return $val;
}

?>
