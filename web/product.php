<?php
$NUMBER_OF_BIDS_TO_SHOW = 5;

session_start();
include 'functions.php';
$r = getDBConnection();

$pid = getURLParameter('pid');
$query = "SELECT owner_id FROM Owns WHERE pid = \"$pid\";";
$row = mysql_fetch_assoc(mysql_query($query));
$owner = $row['owner_id'];
$isOwner = ($owner == $_SESSION['aid']);

// define variables and set to empty values
$error = 0;
$bidErr = $buyErr = $overallErr = "";

// Bid/Buy Product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$location = test_input($_POST["location"]);
	$creditCard = test_input($_POST["creditCard"]);
	$bidAmount = test_input($_POST["amount"]);

	// Bid product
	if (!empty($_POST['Bid']) && empty($_POST["amount"])) {
		$error = 1;
		$bidErr = "You must bid an amount higher than the current auction price.<br>";
	}

	if ($isOwner) {
		$error = 1;
		$overallErr = "You cannot bid on/buy your own product.<br>";
	}

	$query = "SELECT sid FROM Suppliers WHERE sid = \"" . $_SESSION['aid'] . "\";";
	if (mysql_fetch_assoc(mysql_query($query))) {
		$error = 1;
		$overallErr = $overallErr . "Suppliers cannot bid on/buy products.<br>";	
	}

	if (empty($_SESSION['aid'])) {
		$error = 1;
		$overallErr = $overallErr . "You must be logged in to bid on/ buy products.<br>";
	}
		
	
	$commitMessage = array();
	if ($error == 0 && !empty($_POST['Bid'])) {
		// Bid product
		$query = "INSERT INTO Bid (uid, pid, b_date, amount, pay_with, ship_to)
			VALUES (\"" . $_SESSION['aid'] . "\", \"$pid\", \"" . date("Y-m-d") .
			"\", $bidAmount, \"$creditCard\", $location);";
		$rs = mysql_query($query);
		checkError($rs, $commitMessage);
	} else if ($error == 0 && !empty($_POST['Buy'])) {
		// Buy product
		$query = "SELECT I.location, O.owner_id FROM Items I, Owns O 
			WHERE I.pid = $pid AND O.pid = I.pid;";
		$row = mysql_fetch_assoc(mysql_query($query));
		$shipFrom = $row['location'];
		$seller = $row['owner_id'];

		$trackingNum = rand();
		$dateOfSale = date("Y-m-d");
		$buyer = $_SESSION['aid'];

		// Add to database
		beginTransaction();
		$rollback = 0;
		
		$query = "INSERT INTO Transactions (category, tracking_number, date_of_sale,
				seller, buyer, paid_with, ships_to, ships_from) VALUES 
				(\"s\", \"$trackingNum\", \"$dateOfSale\", \"$seller\", \"$buyer\", 
				\"$creditCard\", \"$location\", \"$shipFrom\");";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);

		// Get the transaction id
		$tid = mysql_insert_id();

		$query = "UPDATE Items SET included_in = \"$tid\" WHERE pid = \"$pid\";";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);

			
		if ($rollback == 0) {
			commitTransaction();
			goToPage("myAccount.php");
			die;
		} else {
			rollbackTransaction();
		}
	}

}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="en-us" http-equiv="Content-Language" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Lil' Bits Computer Hardware</title>
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
.auto-style7 {
	font-size: large;
	text-decoration: underline;
	
}
.auto-style8 {
	font-size: large;
	text-decoration: bold;
	
}
.error {
	color: #FF0000;
}


</style>
</head>

<body bgcolor="#CCFFFF">

<?php

insertTopOfPage();

if ($pid == False){
	// No pid parameter in URL -> go to index page
	goToPage('index.php');
}

// Find the item (only if it is not bought already, not up for auction or currently being auctioned off)
$query = "SELECT * FROM Items WHERE pid = $pid AND 
	(bid_end = 0 OR (bid_end > NOW() AND bid_start <= NOW()) OR (list_price > 0 AND included_in = 1)) AND
	included_in = 1;";
$row = mysql_fetch_assoc(mysql_query($query));

if ($row == False && !$isOwner){
	// No such item -> go to index page
	goToPage('index.php');
} else if ($row == False) {
	// owner is viewing page -> show all item information
	$query = "SELECT * FROM Items WHERE pid = $pid;";
	$row = mysql_fetch_assoc(mysql_query($query));
}

// Get product information
$location = $row['location'];
$upc = $row['upc'];
$listPrice = $row['list_price'];
$auctionPrice = $row['auction_price'];
$bidStart = $row['bid_start'];
$bidEnd = $row['bid_end'];
$includedIn = $row['included_in'];

// Get item description and name
$query = "SELECT * FROM ItemDesc WHERE upc = \"$upc\";";
$row = mysql_fetch_assoc(mysql_query($query));

$itemName = $row['name'];
$description = $row['description'];


// Get product location
$query = "SELECT * FROM Addresses WHERE address_id = \"$location\";";
$row = mysql_fetch_assoc(mysql_query($query));

$city = $row['city'];
$state = $row['state'];

// Show product
echo "<br><br>" . '<span class="auto-style7">' .
	"$itemName </span><br>
	UPC: $upc <br>
	Sold by " . userMessageLink($owner) . " from $city, $state <br><br>
	$description <br><br><br>";

if ($includedIn > 1) {
	// Already sold
	echo '<span class="auto-style7"><span class="error">' . 
		"Item sold in transaction $includedIn<br><br></span></span>";
}

if ($listPrice > 0) {
	// Product can be bought directly
	echo '<span class="auto-style7">Buy now price: $' . $listPrice ;
	echo '</span><form method="post">Ship to: ';
	addUserAddressesDropDown($_SESSION['aid'], $location);
	echo "<br>Buy with: ";
	addUserCCDropdown($_SESSION['aid'], $creditCard);
	echo '<br><input type="submit" name="Buy" value="Buy">
		</form><br>';
	echo '<span class="error">' . "$buyErr </span><br><br>";
}

if ($auctionPrice > 0 && (time() < strtotime($bidEnd) || $isOwner)) {
	echo '<table style="width: 75%"><tr style="vertical-align: top"><td>';
	// Product is up for auction -> Get highest bid
	$query = "SELECT MAX(amount) AS amount FROM Bid WHERE pid = \"$pid\";";
	$row = mysql_fetch_assoc(mysql_query($query));
	$maxBid = $row['amount'];

	$query = "SELECT COUNT(*) AS count FROM Bid WHERE pid = \"$pid\";";
	$row = mysql_fetch_assoc(mysql_query($query));
	$bidCount = $row['count'];
	echo '<span class="auto-style7">Current auction price: $' . 
		max($maxBid, $auctionPrice) . "<br>
		Number of bids: $bidCount <br>
		Auction ends at $bidEnd </span><br>";
	countdownTimer($bidEnd, "Auction has ended");
	
	// Bid form
	echo '<form method="post">
		Bid: <input type="number" name="amount" min="' . 
		max($maxBid + 1, $auctionPrice) . '" step="1"><br>Ship to: ';
	addUserAddressesDropDown($_SESSION['aid'], $location);
	echo "<br>Buy with: ";
	addUserCCDropdown($_SESSION['aid'], $creditCard);
	echo '<br><input type="submit" name="Bid" value="Bid">
		</form><br>';
	echo '<span class="error">' . "$bidErr </span><br><br></td>";
	echo '<td><span class="auto-style7">Previous Bids:</span><br>';

	$query = "SELECT * FROM Bid WHERE pid = \"$pid\" ORDER BY amount DESC;";
	$rs = mysql_query($query);
	$count = 0;

	
	// Show bids (all if the owner is viewing the page)
	echo '<table style="width: 50%">';
	while (($count < $NUMBER_OF_BIDS_TO_SHOW || $isOwner) && $row = mysql_fetch_assoc($rs)) {
		$count = $count + 1;
		echo "<tr><td>$count)</td><td>" . userMessageLink($row['uid']) . 
			':</td><td style="align: center">$' . $row['amount'] . "</td></tr>";
		
	}
	echo "</table></td></tr></table>";
	
}


echo '<span class="error">' . "$overallErr </span><br>";
echo '<span class="error">';
foreach ($commitMessage as $message)
    echo "$message<br>";
echo '</span>';

?>


</body>

</html>
