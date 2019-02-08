<?php 
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';
 ?>

<html lang="en">
<head>
	<title>Welcome to Stacktracks</title>
	<link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
</head>
<body>

	<?php
	  if(isset($_POST['register_button'])) {
	  	echo '
	  	<script>
	  		$(document).ready(function() {
	  			$("#first").hide();
	  			$("#second").show();
	  		});
	  	</script>
	  	';
	  }
	?>

	<div class="wrapper">
		<div class="login_box">
			<div class="login_header">
				<h1>stacktracks</h1>
				<h4>A new way to collaborate with musicians</h4>
				Login or sign up below
				<br>
			</div>

			<div id="first">
			<form action="register.php" method="POST">
				<input type="email" name="log_email" placeholder="Email" style="margin-top: 15px;" value="<?php if(isset($_SESSION['log_email'])) {
					echo $_SESSION['log_email'];
				}?>" required><br>

				<input type="password" name="log_password" placeholder="Password"><br>

				<?php if(in_array("Email or password was incorrect<br>", $error_array)) echo "Email or password was incorrect<br>"; ?>

				<input type="submit" name="login_button" value="Login"><br>
				<a href="#" id="signup" class="signup">Don't have an account? Register here!</a> <p><a href="enter_email.php" id="forgot">Forgot your password?</a></p>

			</form><br>
			</div>

			<div id="second">
			<form action="register.php" method="POST">
				<input type="text" name="reg_fname" placeholder="First Name" style="margin-top: 15px;" value="<?php if(isset($_SESSION['reg_fname'])) {
					echo $_SESSION['reg_fname'];
				}?>" required>
				<br>

				<?php
				if(in_array("Your first name must be between 2 and 25 characters<br>", $error_array)) echo "Your first name must be between 2 and 25 characters<br>"; ?>

				<input type="text" name="reg_lname" placeholder="Last Name" value="<?php if(isset($_SESSION['reg_lname'])) {
					echo $_SESSION['reg_lname'];
				}?>" required>
				<br>

				<?php
				if(in_array("Your last name must be between 2 and 25 characters<br>", $error_array)) echo "Your last name must be between 2 and 25 characters<br>"; ?>

				<input type="text" name="reg_user" placeholder="Artist Name" value="<?php if(isset($_SESSION['reg_user'])) {
					echo $_SESSION['reg_user'];
				}?>" required>
				<br>

				<?php
				if(in_array("Your Artist Name must be between 2 and 25 characters<br>", $error_array)) echo "Your Artist Name must be between 2 and 25 characters<br>"; 

				else if(in_array("Artist Name is already in use<br>", $error_array)) echo "Artist Name is already in use<br>"; ?>

				<input type="text" name="reg_email" placeholder="Email" style="display: inline; width: 35%;" value="<?php if(isset($_SESSION['reg_email'])) {
					echo $_SESSION['reg_email'];
				}?>" required>
				

				<input type="text" name="reg_email2" placeholder="Confirm Email" style="display: inline; width: 35%;" value="<?php if(isset($_SESSION['reg_email2'])) {
					echo $_SESSION['reg_email2'];
				}?>" required>
				<br>

				<?php
				if(in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
				
				else if(in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>"; 
				
				else if(in_array("Emails Don't Match<br>", $error_array)) echo "Emails Don't Match<br>"; ?>

				<input type="password" name="reg_password" placeholder="Password" style="display: inline; width: 35%;" required>
				

				<input type="password" name="reg_password2" placeholder="Confirm Password" style="display: inline; width: 35%;" required>
				<br>

				<?php
				if(in_array("Your passwords do not match<br>", $error_array)) echo "Your passwords do not match<br>"; 
				
				else if(in_array("Your password can only contain english characters or numbers<br>", $error_array)) echo "Your password can only contain english characters or numbers<br>";
				
				else if(in_array("Your password must be between 5 and 30 characters<br>", $error_array)) echo "Your password must be between 5 and 30 characters<br>"; ?>
				

				<input type="submit" name="register_button" value="Sign Up">
				<br>

				

				<a href="#" id="signin" class="signin">Have an account? Sign in here!</a>
				
			</form>
			</div>
		</div>
	</div>
	<!--
	<footer>
		<p>By clicking Sign Up, you agree to our Terms, Data Policy and Cookies Policy.</p>
		<p>Stacktracks &copy 2018</p>
	</footer>
	-->
</body>
</html>