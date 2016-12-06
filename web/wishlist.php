<?php
session_start();

include 'functions.php';

if (empty($_SESSION['aid'])) {
	// Not logged in
	goToPage("myAccount.php");
	die;
}


$r = getDBConnection();

// define variables and set to empty values
$error = 0;
$message = $overallErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['addToWishlist'])) {
	// Add to Wishlist
	$upc = test_input($_POST["addToWishlist"]);

	$query = "SELECT sid FROM Suppliers WHERE sid = \"" . $_SESSION['aid'] . "\";";
	if (mysql_fetch_assoc(mysql_query($query))) {
		$error = 1;
		$overallErr = "Suppliers cannot have a wishlist.<br>";	
	}
	
	if ($error == 0) {
		// Add to database
		$commitMessage = array();
		
		$query = "INSERT INTO Wishes (uid, upc, w_date) VALUES 
				(\"" . $_SESSION['aid'] . "\", \"$upc\", \"" . date("Y-m-d") . "\");";
		$rs = mysql_query($query);
		checkError($rs, $commitMessage);
	}

} else if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['deleteFromWishlist'])) {
	// Add to Wishlist
	$upc = test_input($_POST["deleteFromWishlist"]);
	
	// Delete from database
	$commitMessage = array();
	
	$query = "DELETE FROM Wishes WHERE uid =\"" . $_SESSION['aid'] . "\" AND upc = \"$upc\";";
	$rs = mysql_query($query);
	checkError($rs, $commitMessage);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="en-us" http-equiv="Content-Language" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Lil' Bits - My Account</title>
<style type="text/css">
.auto-style1 {
	font-size: xx-large;
}
.auto-style2 {
	font-size: 40pt;
}
.auto-style3 {
	text-align: right;
}
.auto-style4 {
	font-size: x-large;
}
.auto-style5 {
	text-align: center;
}
.auto-style6 {
	text-align: center;
	text-decoration: underline;
}
.error {
	color: #FF0000;
}
</style>
</head>

<body bgcolor="#CCFFFF">

<?php 
	insertTopOfPage();

	echo '<p class="auto-style4">' . $_SESSION['name'] . '\'s Wishlist:</p>';

	$query = 'SELECT * FROM Wishes W, ItemDesc D WHERE W.uid = "' . $_SESSION['aid'] .  '" 
			AND W.upc = D.upc ORDER BY w_date DESC;';
	$rs = mysql_query($query);

	echo '<table style="width: 100%">
		<tr>
			<td class="auto-style6" width="75"><strong>Date</strong></td>
			<td class="auto-style6" width="100"><strong>UPC</strong></td>
			<td class="auto-style6" width="350"><strong>Product</strong></td>
			<td class="auto-style6"><strong>Description</strong></td>
			<td class="auto-style6" width="75"><strong></strong></td>
		</tr>';

	while ($row = mysql_fetch_assoc($rs)) {
		echo '<tr class="auto-style5"><td>' . date("n/j/y", strtotime($row['w_date'])) . "</td><td>";
		echo getSearchLink($row['upc'], $row['upc']) . '</td><td>' . $row['name'] . '</td><td>' . 
			$row['description'] . "</td>";
		echo '<td><form method="post" action="wishlist.php"><button type="submit" value="' . 
			$row['upc'] . '" name="deleteFromWishlist">Delete</button></form></td>';
		echo "</tr>";
	}
	echo "</table>";

	echo '<span class="error">' . "$overallErr </span><br><br>";
	echo '<span class="error">';
	foreach ($commitMessage as $message)
	    echo "$message<br>";
	echo '</span><br><br>';	

	echo '<br><br><br><a href="myAccount.php">Back to My Account</a>';

?>

</body>

</html>

