<?php
session_start();

include 'functions.php';

if (empty($_SESSION['aid'])) {
	// Not logged in
	goToPage("myAccount.php");
	die;
}


$sendTo = getURLParameter('sendTo');

if ($sendTo == False){
	// No sendTo parameter in URL -> go to index page
	goToPage('index.php');
}

$r = getDBConnection();

// define variables and set to empty values
$error = 0;
$message = $sendErr = "";

// Send Message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$message = test_input($_POST["message"]);

	// No message
	if (empty($_POST["message"])) {
		$error = 1;
		$sendErr = "You cannot send a blank message.<br>";
	}

	if ($sendTo == $_SESSION['aid']) {
		$error = 1;
		$sendErr = $sendErr . "You cannot send yourself a message.<br>";
	}
		
	
	if ($error == 0 && !empty($_POST['Send'])) {
		// Send message
		$commitMessage = array();
		
		$query = "INSERT INTO Message (sender, receiver, m_date, message) VALUES 
				(\"" . $_SESSION['aid'] . "\", \"$sendTo\", \"" . date("Y-m-d H:i:s") . 
				"\", \"$message\");";
		$rs = mysql_query($query);
		$rollback = checkError($rs, $commitMessage);
			
		if ($rollback == 0) {
			goToPage("messages.php");
			die;
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
	echo '<span class="auto-style7">Send to: ' . $sendTo ;
	echo '</span><br><form method="post">Message: <br>';
	echo '<textarea name="message" rows="10" cols="50">' . $message . '</textarea>';
	echo '<br><input type="submit" name="Send" value="Send">
		</form><br>';
	echo '<span class="error">' . "$sendErr </span><br><br>";
	echo '<span class="error">';
	foreach ($commitMessage as $message)
	    echo "$message<br>";
	echo '</span><br><br>';

	echo '<br><br><a href="messages.php">Back to My Messages</a>';

?>

</body>

</html>

