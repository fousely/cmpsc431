<?php
session_start();

// define variables and set to empty values
$aidErr = $emailErr = $full_nameErr = $genderErr = $dobErr = "";
$streetErr = $cityErr = $zipErr = $phoneNumberErr = $passwordErr = "";
$aid = $email = $full_name = $gender = $dob = $phoneNumber = $street = $city = $zip = $state = $password = "";

// Sign up
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['signup'])) {
	// Check aid is unique SELECT count(*) FROM Accounts WHERE aid = aid; = 0
	if (empty($_POST["aid"])) {
		$aidErr = "Username is required";
	} else {
		$aid = test_input($_POST["aid"]);
	}
    
	// Check email is unique SELECT count(*) FROM HasEmail WHERE email = email; = 0
	if (empty($_POST["email"])) {
		$emailErr = "Email is required";
	} else {
		$email = test_input($_POST["email"]);
	}
    
	if (empty($_POST["full_name"])) {
		$full_nameErr = "Full name is required";
	} else {
		$full_name = test_input($_POST["full_name"]);
	}
    
	if (empty($_POST["gender"])) {
		$genderErr = "Gender is required";
	} else {
		$gender = test_input($_POST["gender"]);
	}
    
	if (empty($_POST["dob"])) {
		$dobErr = "Date of birth is required";
	} else {
		$dob = test_input($_POST["dob"]);
	}
    
	if (empty($_POST["phoneNumber"])) {
		$phoneNumberErr = "Phone number is required";
	} else {
		$phoneNumber = test_input($_POST["phoneNumber"]);
	}
    
	if (empty($_POST["street"])) {
		$streetErr = "Street is required";
	} else {
		$street = test_input($_POST["street"]);
	}

	if (empty($_POST["city"])) {
		$cityErr = "City is required";
	} else {
		$city = test_input($_POST["city"]);
	}

	if (empty($_POST["zip"])) {
		$zipErr = "Zip is required";
	} else {
		$zip = test_input($_POST["zip"]);
	}
    
	$state = test_input($_POST["state"]);
    
	if (empty($_POST["password"]) || empty($_POST["password2"]) ||
        strcmp($_POST["password"], $_POST["password2"]) <> 0) {
		$passwordErr = "Password doesn't match";
	} else {
		$password = test_input($_POST["password"]);
	}

	// If no errors, add to database and login
	//   Add session variable aid

	/*
	START TRANSACTION;
	INSERT INTO Accounts (aid, full_name, pass) VALUES (username, full_name, pass);
	INSERT INTO Emails (email, aid) VALUES (email, username);
	INSERT INTO Users (uid, gender, dob) VALUES (username, gender, dob);
	INSERT INTO HasPhoneNumber (aid, phone_number) VALUES (username, phone);

	Check if address exists (SELECT COUNT(*)...)-> if not
	INSERT INTO Addresses (street, city, state, zip) VALUES (street, city, state, zip);
	$id = mysql_insert_id

	If address does exist
	$id = $row['address_id'];

	Either way for address
	INSERT INTO HasAddress (aid, address_id) VALUES (username, id);

	After each result:
	if (!$result) {
		$rollback = true;
	}


	Finally:
	if ($rollback) {
		ROLLBACK
	} else {
		COMMIT
	}

	*/
}

// Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['login'])) {
	if (empty($_POST["aid"])) {
		$loginErr = "Username is required";
	} else {
		$aid = test_input($_POST["username"]);
	}
    
	$password = test_input($_POST["password"]);

    	// Check database for a match
	// If exists, set $_SESSION['aid'] = $aid
	// SELECT COUNT(*) FROM Accounts WHERE aid = username AND password = password;
	// If returns 1 row, good username/password combo
}



function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
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
</style>
</head>

<body bgcolor="#CCFFFF">
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


<?php
	// Not logged in
	if (empty($_SESSION['aid'])) {
		echo "Please login below or create an account";
		echo '	<form action="myAccount.php" method="post">
			Full Name: <input type="text" name="full_name" value = "';
		echo $full_name . '"> <span class="error">*';
		echo $full_nameErr . '</span><br>
			E-mail: <input type="email" name="email" value = "';
		echo $email . '"> <span class="error">*';

		echo $emailErr . '</span><br>
			Username: <input type="text" name="aid" value = "';
		echo $aid . '">			<span class="error">*';
		
		echo $aidErr . '</span><br>
			Gender:
			<input type="radio" name="gender"';
		if (isset($gender) && $gender=="F") echo " checked ";
		echo ' value="F">Female
			<input type="radio" name="gender"';
		if (isset($gender) && $gender=="M") echo " checked ";
		echo ' value="M">Male
			<span class="error">*';

		echo $genderErr . '</span><br>
			Date of Birth: <input type="date" name="dob" value = "';
		echo $dob . '"> <span class="error">*';
		
		echo $dobErr . '</span><br>
			Phone Number: <input type="tel" name="phonenumber" value = "';
		echo $phonenumber . '"> <span class="error">*';
		
		echo $phoneNumberErr . '</span><br>
			Address - Street: <input type="text" name="street" value = "';
		echo $street . '"> <span class="error">*';
		
		echo $streetErr . '</span><br>
			Address - City: <input type="text" name="city" value = "';
		echo $city . '"> <span class="error">*';
		
		echo $cityErr . '</span><br>
			Address - Zip: <input type="text" name="zip">
    			<span class="error">*';
		
		echo $zipErr . '</span><br>
			Address - State: 
				<select name="state">
					<option value="AL">Alabama</option>
					<option value="AK">Alaska</option>
					<option value="AZ">Arizona</option>
					<option value="AR">Arkansas</option>
					<option value="CA">California</option>
					<option value="CO">Colorado</option>
					<option value="CT">Connecticut</option>
					<option value="DE">Delaware</option>
					<option value="DC">District Of Columbia</option>
					<option value="FL">Florida</option>
					<option value="GA">Georgia</option>
					<option value="HI">Hawaii</option>
					<option value="ID">Idaho</option>
					<option value="IL">Illinois</option>
					<option value="IN">Indiana</option>
					<option value="IA">Iowa</option>
					<option value="KS">Kansas</option>
					<option value="KY">Kentucky</option>
					<option value="LA">Louisiana</option>
					<option value="ME">Maine</option>
					<option value="MD">Maryland</option>
					<option value="MA">Massachusetts</option>
					<option value="MI">Michigan</option>
					<option value="MN">Minnesota</option>
					<option value="MS">Mississippi</option>
					<option value="MO">Missouri</option>
					<option value="MT">Montana</option>
					<option value="NE">Nebraska</option>
					<option value="NV">Nevada</option>
					<option value="NH">New Hampshire</option>
					<option value="NJ">New Jersey</option>
					<option value="NM">New Mexico</option>
					<option value="NY">New York</option>
					<option value="NC">North Carolina</option>
					<option value="ND">North Dakota</option>
					<option value="OH">Ohio</option>
					<option value="OK">Oklahoma</option>
					<option value="OR">Oregon</option>
					<option value="PA">Pennsylvania</option>
					<option value="RI">Rhode Island</option>
					<option value="SC">South Carolina</option>
					<option value="SD">South Dakota</option>
					<option value="TN">Tennessee</option>
					<option value="TX">Texas</option>
					<option value="UT">Utah</option>
					<option value="VT">Vermont</option>
					<option value="VA">Virginia</option>
					<option value="WA">Washington</option>
					<option value="WV">West Virginia</option>
					<option value="WI">Wisconsin</option>
					<option value="WY">Wyoming</option>
				</select><br>
			Password: <input type="password" name="password">
    			<span class="error">*';
		
			echo $passwordErr . '</span><br>
			Confirm Password: <input type="password" name="password2"><br>


			<input type="submit" name="signup" value="Sign up">

			</form><br><br>';

		echo "Login below";
		echo '	<form action="myAccount.php" method="post">
			Username: <input type="text" name="username" value = "';
		echo $username . '"><br>
			Password: <input type="password" name="password"><br>';
		echo $loginErr . '<br>
			<input type="submit" name="login" value="Login">

			</form><br><br>';

	} else {
		echo "Welcome back " . $_SESSION['aid'] . "!";
	}

?>

</body>

</html>

