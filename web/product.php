<?php
session_start();
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


</style>
</head>

<body bgcolor="#CCFFFF">

<?php
include 'functions.php';
$r = getDBConnection();
?>
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

<?php
$pid = getURLParameter('pid');

if ($pid == False){
	// No pid parameter in URL -> go to index page
	goToPage('index.php');
}

// Find the item (only if it is not bought already, not up for auction or currently being auctioned off)
$query = "SELECT * FROM Items WHERE pid = $pid AND 
	(bid_end = 0 OR (bid_end > NOW() AND bid_start <= NOW())) AND
	included_in = 1;";
$row = mysql_fetch_assoc(mysql_query($query));

if ($row == False){
	// No such item -> go to index page
	goToPage('index.php');
}

// Get product information
$location = $row['location'];
$upc = $row['upc'];
$listPrice = $row['list_price'];
$auctionPrice = $row['auction_price'];
$bidStart = $row['bid_start'];
$bidEnd = $row['bid_end'];

// Get item description and name
$query = "SELECT * FROM ItemDesc WHERE upc = \"$upc\";";
$row = mysql_fetch_assoc(mysql_query($query));

$itemName = $row['name'];
$description = $row['description'];

// Get owner
$query = "SELECT * FROM Owns WHERE pid = \"$pid\";";
$row = mysql_fetch_assoc(mysql_query($query));

$ownerID = $row['owner_id'];

// Get product location
$query = "SELECT * FROM Addresses WHERE address_id = \"$location\";";
$row = mysql_fetch_assoc(mysql_query($query));

$city = $row['city'];
$state = $row['state'];

// Show product
echo "<br><br>" . '<span class="auto-style7">' .
	"$itemName </span><br>
	UPC: $upc <br>
	Sold by <u>$ownerID</u> from $city, $state <br><br>
	$description <br><br><br>";

if ($listPrice > 0) {
	// Product can be bought directly
	echo "Buy now price: $" . $listPrice ;
	echo '<form action="?????" method="post">
		<input type="submit" name="Buy" value="Buy">
		</form><br><br>';
	// $$ Need to add ability to buy item
}

if ($auctionPrice > 0) {
	// Product is up for auction -> Get highest bid
	$query = "SELECT MAX(amount) AS amount FROM Bid WHERE pid = \"$pid\";";
	$row = mysql_fetch_assoc(mysql_query($query));
	$maxBid = max($row['amount'], $auctionPrice);
	echo "Current auction price: $" . $maxBid . "<br>
		Auction ends at $bidEnd <br>";
	
	// Bid form
	echo '<form action="?????" method="post">
		Bid: <input type="number" name="amount" min="' . ($maxBid + 1) . '" step="1">
		<input type="submit" name="Bid" value="Bid">
		</form><br><br>';
	// $$ Add bid ability
}
	
	
?>


</body>

</html>
