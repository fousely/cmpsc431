<?php
session_start();

include 'functions.php';
$r = getDBConnection();

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
		<td class="auto-style3" style="width: 150px"><a href="myAccount.php">My Account</a></td>
	</tr>
</table>
<p>&nbsp;</p>



<?php 
	echo '<p class="auto-style4">' . $_SESSION['name'] . '\'s info:</p>';
	echo "Email: ";
	$query = 'SELECT E.email FROM Emails E
		WHERE E.aid = "' . $_SESSION['aid'] .  '";';
	$rs = mysql_query($query);	
	if ($row = mysql_fetch_assoc($rs)) {
		echo $row['email'] . "<br><br>";
	} else {
		echo '<span class="error">Error retrieving your email</span><br><br>';
	}

	echo "Phone number(s): ";
	$query = 'SELECT P.phone_number FROM HasPhoneNumber P
		WHERE P.aid = "' . $_SESSION['aid'] .  '";';
	$rs = mysql_query($query);
	$count = 1;	
	while ($row = mysql_fetch_assoc($rs)) {
		if ($count > 1) echo ", ";
		echo $row['phone_number'];
		$count++;
	}

	if ($count == 1) {
		echo '<span class="error">Error retrieving your phone number(s)</span>';
	}

	echo "<br><br>Your addresses: <br>";
	$query = 'SELECT A.address_id, A.street, A.city, A.state, A.zip FROM Addresses A, HasAddress H 
		WHERE H.aid = "' . $_SESSION['aid'] .  '" AND H.address_id = A.address_id;';
	$rs = mysql_query($query);
	$count = 1;
	$addressArray = array();

	while ($row = mysql_fetch_assoc($rs)) {
		if ($count == 1) {
			echo '<table >
				<tr>
					<td class="auto-style6" width="50"><strong>ID</strong></td>
					<td class="auto-style6" width="250"><strong>Street</strong></td>
					<td class="auto-style6" width="150"><strong>City</strong></td>
					<td class="auto-style6" width="75"><strong>State</strong></td>
					<td class="auto-style6" width="75"><strong>Zip</strong></td>
				</tr>';
		}
		echo '<tr class="auto-style5"><td>' . "$count</td><td>";
		echo $row['street'] . '</td><td>' . $row['city'] . "</td><td>" . $row['state'] . 
			"</td><td>" . $row['zip'] . "</td></tr>";
		$addressArray[$row['address_id']] = $count;
		$count++;
	}

	if ($count == 1) {
		echo '<span class="error">Error retrieving your addresses</span><br><br>';
	} else {
		echo '</table>';
	}


	echo "<br><br>Your credit cards: <br>";
	$query = 'SELECT C.card_number, C.name_on_card, C.expiration, C.three_digit_code, C.bills_to
		FROM CreditCards C, OwnsCC O
		WHERE O.uid = "' . $_SESSION['aid'] .  '" AND O.card_number = C.card_number;';
	$rs = mysql_query($query);
	$count = 1;
	while ($row = mysql_fetch_assoc($rs)) {
		if ($count == 1) {
			echo '<table >
				<tr>
					<td class="auto-style6" width="50"><strong>ID</strong></td>
					<td class="auto-style6" width="150"><strong>Number</strong></td>
					<td class="auto-style6" width="200"><strong>Name on Card</strong></td>
					<td class="auto-style6" width="75"><strong>Expiration</strong></td>
					<td class="auto-style6" width="75"><strong>CVV</strong></td>
					<td class="auto-style6" width="75"><strong>Bills to Address</strong></td>
				</tr>';
		}

		echo '<tr class="auto-style5"><td>' . "$count</td><td>";
		echo $row['card_number'] . '</td><td>' . $row['name_on_card'] . 
			"</td><td>" . date("m/Y", strtotime($row['expiration'])) . 
			"</td><td>" . $row['three_digit_code'] . 
			"</td><td>" . $addressArray[$row['bills_to']] . "</td></tr>";
		$count++;
	}

	if ($count == 1) {
		echo '<span class="error">No credit cards found</span><br><br>';
	} else {
		echo '</table>';
	}

	echo '<br><br><a href="myAccount.php">Back to My Account</a>';

?>

</body>

</html>

