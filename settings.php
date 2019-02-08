<?php
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>

<div class="row settings_page">
	<div class="col-md-12 well settings_contents">
		<h3 class="text-center" style="margin-top: 0;">Account Settings</h3>
		<?php
		echo "<img src='" . $user['profile_pic'] . "' id='small_profile_pic'>";
		?>
		<br>
		<a href="upload_pic.php">Upload New Profile Picture</a><br><br>

		<?php
		$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
		$row = mysqli_fetch_array($user_data_query);

		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		$email = $row['email'];
		?>

		<h4>Change Personal Info</h4>
		<form action="settings.php" method="POST" class="settings_input">
			First Name: <input type="text" name="first_name" value="<?php echo $first_name; ?>"><br>
			Last Name: <input type="text" name="last_name" value="<?php echo $last_name; ?>"><br>
			Email: <input type="text" name="email" value="<?php echo $email; ?>"><br>

			<?php echo $message;?>

			<input type="submit" name="update_details" id="save_details" value="Update Details"><br>
		</form>

		<h4>Change Password</h4>
		<form action="settings.php" method="POST" class="settings_input">
			Old Password: <input type="password" name="old_password"><br>
			New Password: <input type="password" name="new_password_1"><br>
			New Password Again: <input type="password" name="new_password_2"><br>

			<?php echo $password_message;?>

			<input type="submit" name="update_password" id="save_details" value="Update Password"><br>
		</form>

		<h4>Close Account</h4>
		<form action="settings.php" method="POST">
			<input type="submit" name="close_account" id="close_account" value="Close Account">
		</form>

	</div>
</div>