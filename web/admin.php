<?php
session_start();

include 'functions.php';
$r = getDBConnection();


// define variables and set to empty values
$error = 0;
$aidErr = "";
$passwordErr = "";
$aid = $password = "";

// Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['login'])) {
	if (empty($_POST["username"]) || empty($_POST["password"])) {
		$loginErr = "Username and password is required";
	}

	$username = test_input($_POST["username"]);
	$password = test_input($_POST["password"]);
	if ($username != "admin") {
		$loginErr = "Username entered is not the admin.";
	}
	else {
		$query = "SELECT * FROM Accounts A WHERE A.aid = \"admin\"";
		$rs = mysql_query($query);
		$row = mysql_fetch_assoc($rs);

		if (empty($row) || strcmp($row['pass'], $password) <> 0) {
			$loginErr = "Password was incorrect. Please try again.";
		}
	}
	
	if (empty($loginErr)) {
		$_SESSION['aid'] = $_POST["username"];
		$_SESSION['name'] = $row['full_name'];
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
<p>
<meta charset="utf-8" />
<b id="docs-internal-guid-6a6da0ae-035a-24a6-c41b-9923ab67532f" style="font-weight: normal;">
<a href="index.php"><img height="75" src="Pk7WXlrPofElIk0cA-XDTvkxe-b_tX0wCZUbj6x34tUhzOsDjoQ5zDS6mEE8TRWQchg3y-oXdIN3e4UMZ80W9VRf-J0WM0mUe8G4Jh5Dy2FkOjKIwx5ZXQPG7aDmLIUk7HNrw1S2Lco.png" width="75" /></a><span class="auto-style1">
</span><span class="auto-style2">Lil' Bits Computer Hardware</span></b></p>
<p>&nbsp;</p>
<table style="width: 100%">
	<tr>
		<td style="width: 100px"><a href="index.php">Shop</a></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="auto-style3" style="width: 150px"><a href="admin.php">Admin Console</a></td>
	</tr>
</table>
<p>&nbsp;</p>


<?php
	// Not logged in
	if (empty($_SESSION['aid'])) {
		echo "Login below";
		echo '	<form action="admin.php" method="post">
			Username: <input type="text" name="username" value = "';
		echo $username . '"><br>
			Password: <input type="password" name="password"><br>';
		echo '<span class="error">' . $loginErr . '</span><br>
			<input type="submit" name="login" value="Login">

			</form><br><br>';
		echo '<span class="error">';
		foreach ($commitMessage as $message)
		    echo "$message<br>";
		echo '</span>';

	} else if ($_SESSION['aid'] == "admin") {
		echo "Welcome To Admin Console.<br><br>";

		echo '<p class="auto-style4">All Transactions:</p>
			<table style="width: 100%">
				<tr>
					<td class="auto-style6" width="50"><strong>Item UPC</strong></td>
					<td class="auto-style6" width="50"><strong>Seller</strong></td>
					<td class="auto-style6" width="50"><strong>Tracking</strong></td>
					<td class="auto-style6" width="50"><strong>Sale Date</strong></td>
					<td class="auto-style6" width="50"><strong>Paid With</strong></td>
					<td class="auto-style6" width="150"><strong>Ships To</strong></td>
					<td class="auto-style6" width="150"><strong>Ships From</strong></td>
				</tr>';
		$query2 = "SELECT I.upc, T.seller, T.buyer, T.tracking_number, T.date_of_sale, T.paid_with, A1.street street_to, A1.city city_to, A1.state state_to, A1.zip zip_to, A2.street street_from, A2.city city_from, A2.state state_from, A2.zip zip_from FROM Transactions T, Addresses A1, Addresses A2, Items I WHERE T.ships_to = A1.address_id AND T.ships_from = A2.address_id AND I.included_in = T.tid";

		$rs2 = mysql_query($query2);

		while ($row2 = mysql_fetch_assoc($rs2)) {
			echo "<tr>";
			echo "<td class=\"auto-style5\">" . $row2['upc'] . "</td>" .
				"<td class=\"auto-style5\">" . $row2['seller'] . "</td>" .
				"<td class=\"auto-style5\">" . $row2['buyer'] . "</td>" .
				"<td class=\"auto-style5\">" . $row2['tracking_number'] . "</td>" .
				"<td class=\"auto-style5\">" . $row2['date_of_sale'] . "</td>" .
				"<td class=\"auto-style5\">" . $row2['paid_with'] . "</td>" .
				"<td class=\"auto-style5\">" . $row2['street_to'] . ", " . $row2['city_to'] . ", " . $row2['state_to'] . " " . $row2['zip_to'] . "</td>" .
				"<td class=\"auto-style5\">" . $row2['street_from'] . ", " . $row2['city_from'] . ", " . $row2['state_from'] . " " . $row2['zip_from'] . "</td>";
			echo "</tr>";
		}
		echo "</table><br><br>";
		
		echo '<a href="logout.php">Sign out</a>';
	} else {
		echo "You are not authorized to view the admin console.<br>";
		echo '<a href="myAccount.php">My Account</a>';
		
	}

?>

</body>

</html>

