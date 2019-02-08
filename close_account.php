<?php
include("includes/header.php");

if(isset($_POST['cancel'])) {
	header("Location: settings.php");
}

if(isset($_POST['close_account'])) {
	$close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
	session_destroy();
	header("Location: register.php");

}

?>

<div class="text-center well" style="width: 50%; position: relative; left: 25%; top: 50%;">
	<h4>Close Account</h4>

	<p>Are you sure you want to close your account?</p><br><br>
	<p>Closing your account will hide your profile and all your activity from other users</p><br>
	<p>You can re-open your account at any time by simply logging in.</p><br><br>

	<form action="close_account.php" method="POST">

		<input type="submit" name="close_account" id="close_account" value="Close">
		<input type="submit" name="cancel" id="update_details" value="Cancel">
		
	</form>
</div>