<?php
	//Declaring variables to prevent errors
	$fname = ""; //First name
	$lname = ""; //Last name
	$username = "";//Username
	$em = ""; //email
	$em2 = ""; //email 2
	$password = ""; //Password
	$password2 = ""; //Password 2
	$date = ""; //Sign up date
	$error_array = array(); //Holds error messages

	if(isset($_POST['register_button'])){
		//Register form values

		//First name
		$fname = strip_tags($_POST['reg_fname']);//Remove html tags
		$fname = str_replace(' ', '', $fname);//Remove spaces
		$fname = ucfirst(strtolower($fname));//Uppercase first letter
		$_SESSION['reg_fname'] = $fname;//Stores first name into session variable

		//Last name
		$lname = strip_tags($_POST['reg_lname']);//Remove html tage
		$lname = str_replace(' ', '', $lname);//Remove spaces
		$lname = ucfirst(strtolower($lname));//Uppercase first letter
		$_SESSION['reg_lname'] = $lname;//Stores last name into session variable

		$username = strip_tags($_POST['reg_user']);//Remove hmtl tags
		$username = str_replace(' ', '', $username);
		$_SESSION['reg_user'] = $username;//Stores first name into session variable

		//Email
		$em = strip_tags($_POST['reg_email']);//Remove html tags
		$em = str_replace(' ', '', $em);//Remove spaces
		$em = ucfirst(strtolower($em));//Uppercase first letter
		$_SESSION['reg_email'] = $em;//Stores Email into session variable

		//Email 2
		$em2 = strip_tags($_POST['reg_email2']);//Remove html tags
		$em2 = str_replace(' ', '', $em2);//Remove spaces
		$em2 = ucfirst(strtolower($em2));//Uppercase first letter
		$_SESSION['reg_email2'] = $em2;//Stores Email 2 into session variable

		//Password
		$password = strip_tags($_POST['reg_password']);//Remove html tags

		//Password 2
		$password2 = strip_tags($_POST['reg_password2']);//Remove html tags

		$date = date("Y-m-d");//Current date

		if($em == $em2) {
			//Check if email is in valid format
			if(filter_var($em, FILTER_VALIDATE_EMAIL)) {
				$em = filter_var($em, FILTER_VALIDATE_EMAIL);

				//Check if email already exists
				$e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

				//Count number of rows returned
				$num_rows = mysqli_num_rows($e_check);

				if($num_rows > 0) {
					array_push($error_array, "Email already in use<br>");
				}
			}
			else {
				array_push($error_array, "Invalid email format<br>");
			}

		}
		else {
			array_push($error_array, "Emails Don't Match<br>");
		}

		if(strlen($fname) > 25 || strlen($fname) < 2) {
			array_push($error_array, "Your first name must be between 2 and 25 character<br>s");
		}

		if(strlen($lname) > 25 || strlen($lname) < 2) {
			array_push($error_array, "Your last name must be between 2 and 25 characters<br>");
		}

		if($password != $password2) {
			array_push($error_array, "Your passwords do not match<br>");
		}
		else {
			if(preg_match('/[^A-Za-z0-9]/', $password)) {
				array_push($error_array, "Your password can only contain english characters or numbers<br>");
			}
		}

		if(strlen($password > 30 || strlen($password) < 5)) {
			array_push($error_array, "Your password must be between 5 and 30 characters<br>");
		}

		if(strlen($username > 25 || strlen($username) < 2)) {
			array_push($error_array, "Your Artist Name must be between 2 and 25 characters<br>");
		}
		else {
			$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
			$num_row = mysqli_num_rows($check_username_query);
			if($num_row > 0) {
				array_push($error_array, "Artist Name is already in use<br>");
			}
		}

		if(empty($error_array)) {
			$password = md5($password); //Encrypts the password

			//Profile picture assignment
			$profile_pic = "assets/images/profile_pics/defaults/profile_pic.jpg";
			$banner_pic = "assets/images/banner_pics/default/cover.jpg";

			$query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");
			$info_query = mysqli_query($con, "INSERT INTO user_info VALUES('', '$username', '', '', '', '$banner_pic')");


			//Clear session variables
			$_SESSION['reg_fname'] = "";
			$_SESSION['reg_lname'] = "";
			$_SESSION['reg_user'] = "";
			$_SESSION['reg_email'] = "";
			$_SESSION['reg_email2'] = "";

			$_SESSION['username'] = $username;
			header("Location: index.php");
		}

	}
?>