<?php
session_start();

include 'functions.php';

if (empty($_SESSION['aid'])) {
	// Not logged in
	goToPage("myAccount.php");
	die;
}


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

<?php 
	insertTopOfPage();

	echo '<p class="auto-style4">' . $_SESSION['name'] . '\'s Inbox:</p>';

	$query = 'SELECT sender, m_date, message FROM Message WHERE receiver = "' . $_SESSION['aid'] .  '" 
			ORDER BY m_date DESC;';
	$rs = mysql_query($query);

	echo '<table style="width: 100%">
		<tr>
			<td class="auto-style6" width="100"><strong>Date</strong></td>
			<td class="auto-style6" width="100"><strong>Sender</strong></td>
			<td class="auto-style6"><strong>Message</strong></td>
		</tr>';

	while ($row = mysql_fetch_assoc($rs)) {
		echo '<tr class="auto-style5"><td>' . date("n/j/y", strtotime($row['m_date'])) . "<br>" .
			date("g:i A", strtotime($row['m_date'])) . "</td><td>";
		echo userMessageLink($row['sender']) . '</td><td>' . $row['message'] . "</td></tr>";
	}
	echo "</table>";
	
	echo '<p class="auto-style4">' . $_SESSION['name'] . '\'s Outbox:</p>';

	$query = 'SELECT receiver, m_date, message FROM Message WHERE sender = "' . $_SESSION['aid'] .  '" 
			ORDER BY m_date DESC;';
	$rs = mysql_query($query);

	echo '<table style="width: 100%">
		<tr>
			<td class="auto-style6" width="100"><strong>Date</strong></td>
			<td class="auto-style6" width="100"><strong>Sent To</strong></td>
			<td class="auto-style6"><strong>Message</strong></td>
		</tr>';

	while ($row = mysql_fetch_assoc($rs)) {
		echo '<tr class="auto-style5"><td>' . date("n/j/y", strtotime($row['m_date'])) . "<br>" .
			date("g:i A", strtotime($row['m_date'])) . "</td><td>";
		echo userMessageLink($row['receiver']) . '</td><td>' . $row['message'] . "</td></tr>";
	}
	echo "</table>";
	

	echo '<br><br><a href="myAccount.php">Back to My Account</a>';

?>

</body>

</html>

