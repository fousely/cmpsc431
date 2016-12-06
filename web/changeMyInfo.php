<?php
session_start();

include 'functions.php';
if (empty($_SESSION['aid'])) {
	// Not logged in
	goToPage("myAccount.php");
	die;
}

$aid = $_SESSION['aid'];
$full_name = $_SESSION['name'];

$r = getDBConnection();


// define variables and set to empty values
$error = 0;
$street = $city = $zip = $state = "";
$streetErr = $cityErr = $zipErr = "";
$ccNum = $ccExp = $cc3 = "";
$ccNumErr = $ccExpErr = $cc3Err = $billsTo = "";
$overallErr = "";
$creditCardForm = $addressForm = False;


// Setup form
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['addressChange'])) {
	$addressID = test_input($_POST["addressChange"]);
	
	if ($addressID != -1) {
		// Edit old address
		$query = "SELECT * FROM Addresses A WHERE A.address_id = \"$addressID\";";
		$rs = mysql_query($query);
		if ($row = mysql_fetch_assoc($rs)) {
			$street = $row['street'];
			$city = $row['city'];
			$state = $row['state'];
			$zip = $row['zip'];
		} else {
			$overallErr = "Address ID $addressID does not exist.";
		}
	}

	// Show address form	
	$addressForm = True;

} else if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['creditCardChange'])) {
	$creditCardID = test_input($_POST["creditCardChange"]);
	
	if ($creditCardID != -1) {
		// Edit old credit card
		$query = "SELECT * FROM CreditCards WHERE card_number = \"$creditCardID\";";
		$rs = mysql_query($query);
		if ($row = mysql_fetch_assoc($rs)) {
			$ccNum = $row['card_number'];
			$ccExp = $row['expiration'];
			$cc3 = $row['three_digit_code'];
			$billsTo = $row['bills_to'];
		} else {
			$overallErr = "Credit Card $creditCardID does not exist.";
		}
	}

	// Show credit card form	
	$creditCardForm = True;
}




// Add/Edit Address
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['creditCard'])) {
	$creditCardID = test_input($_POST["creditCard"]);

	if (empty($_POST["ccNum"])) {
		$ccNumErr = "Credit card number is required";
		$error = 1;
	} else {
		$ccNum = test_input($_POST["ccNum"]);
	}

	if (empty($_POST["ccExp"])) {
		$ccExpErr = "Credit card expiration is required";
		$error = 1;
	} else {
		$ccExp = test_input($_POST["ccExp"]);
	}

	if (empty($_POST["cc3"])) {
		$cc3Err = "Credit card 3 digit code is required";
		$error = 1;
	} else {
		$cc3 = test_input($_POST["cc3"]);
	}
    
	$billsTo = test_input($_POST["location"]);

	
	if ($error == 0 && $creditCardID == -1) {
		// Add new credit card to database
		beginTransaction();
		$rollback = 0;
		$commitMessage = array();
		
		$query = "INSERT INTO CreditCards (card_number, name_on_card, expiration, 
			three_digit_code, bills_to) VALUES (\"$ccNum\", \"$full_name\", \"" .
			date("Y-m-d", strtotime($ccExp)) . "\", \"$cc3\", \"$billsTo\");";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);
			
		$query = "INSERT INTO OwnsCC (uid, card_number) VALUES (\"$aid\", \"$ccNum\");";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);
		
			
		if ($rollback == 0) {
			commitTransaction();
			goToPage("myInfo.php");
			die;
		} else {
			rollbackTransaction();
			$creditCardForm = True;
		}
	} else 	if ($error == 0 && $creditCardID != -1) {
		// Edit old creditCard
		$rollback = 0;
		$commitMessage = array();

		$query = "UPDATE CreditCards 
			SET expiration = \"" . date("Y-m-d", strtotime($ccExp)) . "\", 
				three_digit_code = \"$cc3\", bills_to =\"$billsTo\"
			WHERE card_number = \"$creditCardID\";";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);
		
			
		if ($rollback == 0) {
			goToPage("myInfo.php");
			die;
		} else {
			$creditCardForm = True;
		}
	} else {
		$creditCardForm = True;
	}

}

// Add/Edit Address
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['address'])) {
	$addressID = test_input($_POST["address"]);

	if (empty($_POST["street"])) {
		$streetErr = "Street is required";
		$error = 1;
	} else {
		$street = test_input($_POST["street"]);
	}

	if (empty($_POST["city"])) {
		$cityErr = "City is required";
		$error = 1;
	} else {
		$city = test_input($_POST["city"]);
	}

	if (empty($_POST["zip"])) {
		$zipErr = "Zip is required";
		$error = 1;
	} else {
		$zip = test_input($_POST["zip"]);
	}
    
	$state = test_input($_POST["state"]);

	$query = "SELECT * FROM HasAddress WHERE address_id = \"$addressID\";";
	$rs = mysql_query($query);

	if ($error == 0 && ($addressID == -1 || mysql_num_rows($rs) > 0)) {
		// Add new address to database (or someone else has that address -> add new one)
		beginTransaction();
		$rollback = 0;
		$commitMessage = array();

		if (mysql_num_rows($rs) > 0) {
			// Delete HasAddress entry
			$query = "DELETE FROM HasAddress WHERE address_id = \"$addressID\" AND aid = \"$aid\";";
			$rs = mysql_query($query);
			checkError($rs, $commitMessage);
		}
		
		$query = "SELECT * FROM Addresses A WHERE A.street = \"$street\"
			AND A.city = \"$city\" AND A.state = \"$state\" AND A.zip = \"$zip\";";
		$rs = mysql_query($query);
		if(mysql_num_rows($rs) == 0) {
			$query = "INSERT INTO Addresses (street, city, state, zip) VALUES (\"$street\", \"$city\",
			 	\"$state\", \"$zip\");";
			$rs = mysql_query($query);
			$rollback = checkError($rs, $commitMessage);

			$addressID = mysql_insert_id();
			$query = "INSERT INTO HasAddress (aid, address_id) VALUES (\"$aid\", \"$addressID\");";
			$rs = mysql_query($query);
			$rollback = checkError($rs, $commitMessage);
		}
		else {
			$row = mysql_fetch_assoc($rs);
			$addressID = $row['address_id'];
			$query = "INSERT INTO HasAddress (aid, address_id) VALUES (\"$aid\", \"$addressID\");";
			$rs = mysql_query($query);
			$rollback = checkError($rs, $commitMessage);
		}
		
			
		if ($rollback == 0) {
			commitTransaction();
			goToPage("myInfo.php");
			die;
		} else {
			rollbackTransaction();
			$addressForm = True;
		}
	} else 	if ($error == 0 && $addressID != -1) {
		// Edit old address
		$rollback = 0;
		$commitMessage = array();
		
		$query = "UPDATE Addresses 
			SET street = \"$street\", city = \"$city\", state = \"$state\", zip = \"$zip\"
			WHERE address_id = \"$addressID\";";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);
		
			
		if ($rollback == 0) {
			goToPage("myInfo.php");
			die;
		} else {
			$addressForm = True;
		}
	} else {
		$addressForm = True;
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

	if ($addressForm) {
		echo '<u>Add/edit address information below:</u><br>';
		echo '	<form action="changeMyInfo.php" method="post">';
		echo 'Address - Street: <input type="text" name="street" value = "';
		echo $street . '"> <span class="error">*';
		
		echo $streetErr . '</span><br>
			Address - City: <input type="text" name="city" value = "';
		echo $city . '"> <span class="error">*';
		
		echo $cityErr . '</span><br>
			Address - Zip: <input type="text" name="zip" value = "';
		echo $zip . '">
    			<span class="error">*';
		
		echo $zipErr . '</span><br>
			Address - State: ';
		addStatesDropdown($state);
		echo '<br><button type="submit" name="address" value="' . $addressID . '">Submit</button>

			</form><br><br>';

	} else 	if ($creditCardForm) {
		echo '<u>Add/edit credit card information below:</u><br>';
		echo '	<form action="changeMyInfo.php" method="post">';

		echo '<br>
			Credit Card Number: <input type="text" name="ccNum" value = "';
		echo $ccNum . '">
    			<span class="error">*';
		
		echo $ccNumErr . '</span><br>
			Credit Card Expiration: <input type="date" name="ccExp" value = "';
		echo $ccExp . '">
    			<span class="error">*';
		
		echo $ccExpErr . '</span><br>
			Credit Card 3 Digit Code: <input type="text" name="cc3" value = "';
		echo $cc3 . '">
    			<span class="error">*';
		
		echo $cc3Err . '</span><br>';
		addUserAddressesDropDown($_SESSION['aid'], $billsTo);
		echo '<br><button type="submit" name="creditCard" value="' . $creditCardID . '">Submit</button>';
			'</form><br><br>';
	}

	echo '<span class="error">' . $overallErr . "<br><br>";
	foreach ($commitMessage as $message)
	    echo "$message<br>";
	echo '</span>';

	echo '<br><br><br><a href="myInfo.php">Back to My Info</a>';

?>

</body>

</html>

