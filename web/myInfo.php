<?php
session_start();

include 'functions.php';

if (empty($_SESSION['aid'])) {
	// Not logged in
	goToPage("myAccount.php");
	die;
}


$r = getDBConnection();

$aid = $_SESSION['aid'];
$commitMessage = array();

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['deleteAddress'])) {
	$addressID = test_input($_POST["deleteAddress"]);
	$error = 0;

	$query = "SELECT * FROM CreditCards C, OwnsCC O WHERE C.card_number = O.card_number AND
			O.uid = \"$aid\" AND C.bills_to = \"$addressID\";";
	$rs = mysql_query($query);

	if ($row = mysql_fetch_assoc($rs)) {
		$error = 1;
		$overallErr = "You cannot delete an address that's linked to a credit card.<br>";
	}

	$query = "SELECT * FROM Items I, Owns O WHERE I.pid = O.pid AND
			O.owner_id = \"$aid\" AND I.location = \"$addressID\";";
	$rs = mysql_query($query);

	if ($row = mysql_fetch_assoc($rs)) {
		$error = 1;
		$overallErr = $overallErr . "You cannot delete an address that's linked to an item.<br>";
	}

	$query = "SELECT * FROM Bid B WHERE B.uid = \"$aid\" AND B.ship_to = \"$addressID\";";
	$rs = mysql_query($query);

	if ($row = mysql_fetch_assoc($rs)) {
		$error = 1;
		$overallErr = $overallErr . "You cannot delete an address that's linked to a bid.<br>";
	}


	$query = "SELECT * FROM Transactions WHERE ship_to = \"$addressID\" OR ship_from = \"$addressID\";";
	$rs = mysql_query($query);

	if ($row = mysql_fetch_assoc($rs)) {
		$error = 1;
		$overallErr = $overallErr . "You cannot delete an address that's linked to a transaction.<br>";
	}

	if ($error == 0) {
		// Delete address
		$query = "DELETE FROM HasAddress WHERE address_id = \"$addressID\" AND aid = \"$aid\";";
		$rs = mysql_query($query);
		checkError($rs, $commitMessage);

		if (sizeof($commitMessage) == 0) {
			// No errors -> see if we can delete the address
			$query = "SELECT * FROM HasAddress WHERE address_id = \"$addressID\";";
			$rs = mysql_query($query);

			if(mysql_num_rows($rs) == 0) {
				// No one else has the address -> delete it
				beginTransaction();
				$rollback = 0;

				$query = "DELETE FROM Addresses WHERE address_id = \"$addressID\";";
				$rs = mysql_query($query);
				$rollback = checkError($rs, $commitMessage);

				if ($rollback == 0) {
					commitTransaction();
				} else {
					rollbackTransaction();
					$overallErr = "Error deleting address $addressID.";
				}
			}
		} else {
			$overallErr = "Error deleting address $addressID.";
		}
	}

} else if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['deleteCreditCard'])) {
	$creditCardID = test_input($_POST["deleteCreditCard"]);
	$error = 0;


	$query = "SELECT * FROM Bid B WHERE B.uid = \"$aid\" AND B.pay_with = \"$creditCardID\";";
	$rs = mysql_query($query);

	if ($row = mysql_fetch_assoc($rs)) {
		$error = 1;
		$overallErr = $overallErr . "You cannot delete a credit card that's linked to a bid.<br>";
	}

	$query = "SELECT * FROM Transactions WHERE paid_with = \"$creditCardID\";";
	$rs = mysql_query($query);

	if ($row = mysql_fetch_assoc($rs)) {
		$error = 1;
		$overallErr = $overallErr . "You cannot delete a credit card that's linked to a transaction.<br>";
	}
	
	if ($error == 0) {
		// Delete credit card
		beginTransaction();
		$rollback = 0;

		$query = "DELETE FROM OwnsCC WHERE card_number = \"$creditCardID\" AND uid = \"$aid\";";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);

		$query = "DELETE FROM Addresses WHERE address_id = \"$addressID\";";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);


		if ($rollback == 0) {
			commitTransaction();
		} else {
			rollbackTransaction();
			$overallErr = "Error deleting credit card $creditCardID.";
		}
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
					<td class="auto-style6" width="75"></td>
					<td class="auto-style6" width="75"></td>
				</tr>';
		}
		echo '<tr class="auto-style5"><td>' . "$count</td><td>";
		echo $row['street'] . '</td><td>' . $row['city'] . "</td><td>" . $row['state'] . 
			"</td><td>" . $row['zip'] . '</td><td>
			<form method="post" action="changeMyInfo.php"><button type="submit" value="' . 				$row['address_id'] . '"name="addressChange">Edit
			</button></form></td><td>
			<form method="post" action="myInfo.php"><button type="submit" value="' . 				$row['address_id'] . '"name="deleteAddress">Delete
			</button></form></td></tr>';
		$addressArray[$row['address_id']] = $count;
		$count++;
	}

	if ($count == 1) {
		echo '<span class="error">Error retrieving your addresses</span><br><br>';
	} else {
		echo '</table>';
	}
	echo '<form method="post" action="changeMyInfo.php"><button type="submit" value="-1"
		name="addressChange">Add Address</button></form><br><br>';

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
					<td class="auto-style6" width="75"></td>
					<td class="auto-style6" width="75"></td>
				</tr>';
		}

		echo '<tr class="auto-style5"><td>' . "$count</td><td>";
		echo $row['card_number'] . '</td><td>' . $row['name_on_card'] . 
			"</td><td>" . date("m/Y", strtotime($row['expiration'])) . 
			"</td><td>" . $row['three_digit_code'] . 
			"</td><td>" . $addressArray[$row['bills_to']] . '</td><td>
			<form method="post" action="changeMyInfo.php"><button type="submit" value="' . 				$row['card_number'] . '"name="creditCardChange">Edit
			</button></form></td><td>
			<form method="post" action="myInfo.php"><button type="submit" value="' . 				$row['card_number'] . '"name="deleteCreditCard">Delete
			</button></form></td></tr>';
		$count++;
	}

	if ($count == 1) {
		echo '<span class="error">No credit cards found</span><br><br>';
	} else {
		echo '</table>';
	}
	echo '<form method="post" action="changeMyInfo.php"><button type="submit" value="-1"
		name="creditCardChange">Add Credit Card</button></form><br><br>';

	echo '<span class="error">' . $overallErr . "<br><br>";
	foreach ($commitMessage as $message)
	    echo "$message<br>";
	echo '</span><br><br>';	

	echo '<br><br><a href="myAccount.php">Back to My Account</a>';

?>

</body>

</html>

