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

function itemLink($pid) {
	return '<a href="' . getPageURL("product.php") . "?pid=$pid"  . '">' . $pid . '</a>';
}

function userMessageLink($aid) {
	return '<a href="' . getPageURL("sendMessage.php") . "?sendTo=$aid"  . '">' . $aid . '</a>';
}


function countdownTimer($date, $endMessage) {
	echo '<script language="JavaScript">
		TargetDate = "' . $date . '";
		BackColor = "palegreen";
		ForeColor = "navy";
		CountActive = true;
		CountStepper = -1;
		LeadingZero = true;
		DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
		FinishMessage = "' . $endMessage . '";
		</script>
		<script language="JavaScript" src="//scripts.hashemian.com/js/countdown.js"></script>';
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

	return sizeof($commitErr);
}

function insertTopOfPage() {
	echo '<p>
	<meta charset="utf-8" />
	<b id="docs-internal-guid-6a6da0ae-035a-24a6-c41b-9923ab67532f" style="font-weight: normal;">
	<a href="index.php"><img height="75" src="Pk7WXlrPofElIk0cA-XDTvkxe-b_tX0wCZUbj6x34tUhzOsDjoQ5zDS6mEE8TRWQchg3y-oXdIN3e4UMZ80W9VRf-J0WM0mUe8G4Jh5Dy2FkOjKIwx5ZXQPG7aDmLIUk7HNrw1S2Lco.png" width="75" /></a><span class="auto-style1">
	</span><span class="auto-style2">Lil\' Bits Computer Hardware</span></b></p>
	<p>&nbsp;</p>
	<table style="width: 100%">
		<tr>
			<td style="width: 100px"><a href="index.php">Shop</a></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="auto-style3" style="width: 150px"><a href="messages.php">My Messages</a></td>
			<td class="auto-style3" style="width: 150px"><a href="myAccount.php">My Account</a></td>
		</tr>
	</table>
	<p>&nbsp;</p>';
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

function addUserCCDropdown($pid, $selectedCC) {
	$query = 'SELECT * FROM CreditCards C, OwnsCC O WHERE O.uid = "' . $pid . '" 
			AND O.card_number = C.card_number;';
	$rs = mysql_query($query);

	echo '<select name="creditCard">';

	while($row = mysql_fetch_assoc($rs)) {
		echo '<option value="' . $row['card_number'] . '"';
		if ($selectedCC == $row['card_number']) {
			echo ' selected="selected"';
		}
		echo '>' . $row['card_number'] . ', Exp: ' . 
			date("m/y", strtotime($row['expiration'])) . '</option>';
	}

	echo '</select>';
}


function addUserAddressesDropDown($pid, $selectedLocation) {
	$query = 'SELECT * FROM Addresses A, HasAddress H WHERE H.aid = "' . $pid . '" 
			AND A.address_id = H.address_id;';
	$rs = mysql_query($query);

	echo '<select name="location">';

	while($row = mysql_fetch_assoc($rs)) {
		echo '<option value="' . $row['address_id'] . '"';
		if ($selectedLocation == $row['address_id']) {
			echo ' selected="selected"';
		}
		echo '>' . $row['street'] . ', ' . $row['city'] . ", " . $row['state'] . 
			" " . $row['zip'] . '</option>';
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

function getUserAddress($pid) {
	$r = getDBConnection();
	$query = "SELECT A.address_id FROM Addresses A, HasAddress H
		WHERE A.address_id = H.address_id AND H.aid = \"$pid\";";
	$ret = mysql_fetch_assoc(mysql_query($query));
	return $ret['address_id'];
}

function getUserCC($pid) {
	$r = getDBConnection();
	$query = "SELECT card_number FROM OwnsCC
		WHERE uid = \"$pid\";";
	$ret = mysql_fetch_assoc(mysql_query($query));
	return $ret['card_number'];
}

function endAuctions() {
	// Get items whose auction ended with a winner
	$r = getDBConnection();
	$query = "SELECT * FROM Items I, Owns O, Bid B WHERE I.pid = B.pid AND I.pid = O.pid AND 
			I.bid_end <= NOW() AND I.included_in = 1 AND
			B.amount = (SELECT MAX(B.amount) FROM Bid B2 WHERE B2.pid = I.pid)
			AND B.amount > I.reserve_price ;";
	$rs = mysql_query($query);

	// Loop through such items
	while ($row = mysql_fetch_assoc($rs)) {
		$pid = $row['pid'];
		$trackingNum = rand();
		$dateOfSale = date("Y-m-d");
		$seller = $row['owner_id'];
		$buyer = $row['uid'];
		$paidWith = getUserCC($buyer);
		$shipTo = getUserAddress($buyer);
		$shipFrom = $row['location'];

		// Add to database
		beginTransaction();
		$rollback = 0;
		$commitMessage = array();
		
		$query = "INSERT INTO Transactions (category, tracking_number, date_of_sale,
				seller, buyer, paid_with, ships_to, ships_from) VALUES 
				(\"b\", \"$trackingNum\", \"$dateOfSale\", \"$seller\", \"$buyer\", 
				\"$paidWith\", \"$shipTo\", \"$shipFrom\");";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);

		// Get the transaction id
		$tid = mysql_insert_id();

		$query = "UPDATE Items SET included_in = \"$tid\" WHERE pid = \"$pid\";";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);

			
		if ($rollback == 0) {
			commitTransaction();
		} else {
			rollbackTransaction();
			echo '<span class="error">' . "$pid: <br>";
			foreach ($commitMessage as $message)
			    echo "$message<br>";
			echo '<br></span>';
		}
	}
}

?>
