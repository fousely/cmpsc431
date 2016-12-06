<?php
session_start();

include 'functions.php';

if (empty($_SESSION['aid'])) {
	// Not logged in
	goToPage("myAccount.php");
	die;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
	// Users should only get to this page via a POST operation
	goToPage("myAccount.php");
	die;
}

$rateItem = $rateUser = False;

if (!empty($_POST['startItemRating'])) {
	// Show form for an item rating
	$rateItem = True;
	$upc = test_input($_POST["startItemRating"]);
} else if (!empty($_POST['startUserRating'])) {
	// Show form for an user rating
	$rateUser = True;
	$ratee = test_input($_POST["startUserRating"]);
}

$r = getDBConnection();

// define variables and set to empty values
$error = 0;
$rating = $ratingMessage = $overallErr = "";

if (!empty($_POST['rateItem'])) {
	// Rate Item
	$ratingMessage = test_input($_POST["ratingMessage"]);
	$rating = test_input($_POST["rating"]);
	$upc = test_input($_POST["rateItem"]);

	if (empty($_POST["rating"])) {
		$error = 1;
		$overallErr = "You must choose a rating.<br>";
	}

	if (empty($_POST["ratingMessage"])) {
		$error = 1;
		$overallErr = $overallErr . "You must have a rating message.<br>";
	}

	if (hasRatedItem($_SESSION['aid'], $upc)) {
		// User has already rated the item -> shouldn't be on this page
		$error = 1;
		$overallErr = $overallErr . "You cannot rate an item twice.<br>";
	}
		
	
	if ($error == 0) {
		// Rate item
		$commitMessage = array();
		
		$query = "INSERT INTO RateItem (rater, upc, stars, description, r_date) VALUES 
				(\"" . $_SESSION['aid'] . "\", \"$upc\", \"$rating\",
				\"$ratingMessage\", \"" . date("Y-m-d") . "\");";

		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);
			
		if ($rollback == 0) {
			goToPage("myAccount.php");
			die;
		}
	}

	if ($error != 0 || $rollback != 0) {
		// Show form to correct errors
		$rateItem = True;
	}

} else if (!empty($_POST['rateUser'])) {
	// Rate User
	$ratingMessage = test_input($_POST["ratingMessage"]);
	$rating = test_input($_POST["rating"]);
	$ratee = test_input($_POST["rateUser"]);

	if (empty($_POST["rating"])) {
		$error = 1;
		$overallErr = "You must choose a rating.<br>";
	}

	if (empty($_POST["ratingMessage"])) {
		$error = 1;
		$overallErr = $overallErr . "You must have a rating message.<br>";
	}

	if (hasRatedUser($_SESSION['aid'], $ratee)) {
		// User has already rated the user -> shouldn't be on this page
		$error = 1;
		$overallErr = $overallErr . "You cannot rate a user twice.<br>";
	}
		
	
	if ($error == 0) {
		// Rate item
		$commitMessage = array();
		
		$query = "INSERT INTO Rating (rater, ratee, stars, description, r_date) VALUES 
				(\"" . $_SESSION['aid'] . "\", \"$ratee\", \"$rating\",
				\"$ratingMessage\", \"" . date("Y-m-d") . "\");";

		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);
			
		if ($rollback == 0) {
			goToPage("myAccount.php");
			die;
		}
	}

	if ($error != 0 || $rollback != 0) {
		// Show form to correct errors
		$rateUser = True;
	}

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


	if ($rateItem) {
		// Get product info
		$query = "SELECT * FROM ItemDesc WHERE upc = \"$upc\";";
		$row = mysql_fetch_assoc(mysql_query($query));
		$description = $row['description'];

		echo '<span class="auto-style7"><u>Item UPC:</u> ' . $upc ;
		echo '</span><br>';
		echo "<u>Description:</u> $$description<br><br>";
	} else if ($rateUser) {
		// Show user transactions
		echo '<span class="auto-style4"><u>User:</u> ' . $ratee ;
		echo '</span><br><br>';

		echo 'Your Purchase History from ' . $ratee . ':<br>
			<table>
				<tr class="auto-style6" style="center">
					<td width="125"><strong>Date of Sale</strong></td>
					<td width="50"><strong>PID</strong></td>
					<td width="150"><strong>Item UPC</strong></td>
					<td width="200"><strong>Tracking Number</strong></td>
				</tr>';
		$query = "SELECT I.pid, I.upc, T.date_of_sale, T.tracking_number FROM Transactions T, Items I
			WHERE T.buyer = \"" . $_SESSION['aid'] . "\"" . " 
				AND T.seller = \"$ratee\" AND I.included_in = T.tid";

		$rs = mysql_query($query);

		while ($row = mysql_fetch_assoc($rs)) {
			echo "<tr class=\"auto-style5\">";
			echo "<td>" . $row['date_of_sale'] . "</td>" .
				"<td>" . $row['pid'] . "</td>" .
				"<td>" . $row['upc'] . "</td>" .
				"<td>" . $row['tracking_number'] . "</td>";
			echo "</tr>";
		}
		echo "</table><br><br>";
	}

	// Show rating form
	echo '<form method="post">';
	echo 'Rating: <input type="number" name="rating" min="1" max="5" step="1" value = "';
	echo $rating . '"><br>Message: <br>';
	echo '<textarea name="ratingMessage" rows="10" cols="50">' . $ratingMessage . '</textarea>';

	if ($rateItem) {
		echo '<br><button type="submit" name="rateItem" value="' . $upc . '">Rate Item';
	} else if ($rateUser) {
		echo '<br><button type="submit" name="rateUser" value="' . $ratee . '">Rate User';
	}

	echo '</button></form><br><span class="error">' . "$overallErr </span><br><br>";
	echo '<span class="error">';
	foreach ($commitMessage as $message)
	    echo "$message<br>";
	echo '</span><br><br>';

	echo '<br><a href="myAccount.php">Back to My Account</a>';

?>

</body>

</html>

