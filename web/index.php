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
</style>
</head>

<body bgcolor="#CCFFFF">

<?php

$host = "localhost";
$user = "teamsanchez";
$pass = "password";
$db   = "sanchez";

$r = mysql_connect($host, $user, $pass);

if (!$r) {
	echo "Could not connect to server\n";
	trigger_error(mysql_error(), E_USER_ERROR);
}

mysql_select_db($db);

?>
<p>
<meta charset="utf-8" />
<b id="docs-internal-guid-6a6da0ae-035a-24a6-c41b-9923ab67532f" style="font-weight: normal;">
<img height="75" src="Pk7WXlrPofElIk0cA-XDTvkxe-b_tX0wCZUbj6x34tUhzOsDjoQ5zDS6mEE8TRWQchg3y-oXdIN3e4UMZ80W9VRf-J0WM0mUe8G4Jh5Dy2FkOjKIwx5ZXQPG7aDmLIUk7HNrw1S2Lco.png" width="75" /><span class="auto-style1">
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
<p class="auto-style4">All Items:</p>
<p class="auto-style4">&nbsp;</p>
<table style="width: 100%">
	<tr>
		<td class="auto-style6" width="200"><strong>Name</strong></td>
		<td class="auto-style6"><strong>Description</strong></td>
		<td class="auto-style6" width="100"><strong>List Price</strong></td>
		<td class="auto-style6" width="100"><strong>Auction Price</strong></td>
		<td class="auto-style6" width="100"><strong>Buy Now</strong></td>
		<td class="auto-style6" width="100"><strong>Bid</strong></td>
	</tr>
	<?php 

		$query = "SELECT D.name, D.description, I.list_price, I.auction_price FROM Items I, ItemDesc D WHERE I.upc = D.upc AND (I.bid_end = NULL OR I.bid_end > NOW())";

		$rs = mysql_query($query);

		while ($row = mysql_fetch_assoc($rs)) {
			echo "<tr>";
			echo "<td class=\"auto-style5\">" . $row['name'] . "</td>" .
				"<td class=\"auto-style5\">" . $row['description'] . "</td><td>";
				
			if (is_null($row['list_price'])) {
				echo "Auction only";
			} else {
				echo "$" . $row['list_price'];
			}

			echo "</td><td class=\"auto-style5\">";

			if (is_null($row['auction_price'])) {
				echo "Buy only";
			} else {
				echo "$" . $row['auction_price'];
			}

			echo "</td><td class=\"auto-style5\">";

			if (!is_null($row['list_price'])) {
				echo "Buy";
			}

			echo "</td><td class=\"auto-style5\">";

			if (!is_null($row['auction_price'])) {
				echo "Bid";
			}

			echo "</td></tr>";

		}

	?>
</table>

</body>

</html>
