<?php
include("includes/header.php");
?>

<div class="text-center well request_box">
	<h3 class="no_request_title">Friend Requests</h3>

	<?php

		$query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
		if(mysqli_num_rows($query) == 0)
			 echo "You have no friend requests at this time.<br><br><br>";
		else {
			while($row = mysqli_fetch_array($query)) {
				$user_from = $row['user_from'];
				$user_from_obj = new User($con, $user_from);

				$user_from_friend_array = $user_from_obj->getFriendArray();

				if(isset($_POST['accept_request' . $user_from])) {
					$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
					$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

					$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
					echo "You are now friends!";
					header("Location: requests.php");
				}

				if(isset($_POST['ignore_request' . $user_from])) {
					$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
					echo "Request ignored";
					header("Location: requests.php");
				}

				$user_details_query = mysqli_query($con, "SELECT username, profile_pic FROM users WHERE username='$user_from'");
  				$user_row = mysqli_fetch_array($user_details_query);
  				$profile_pic = $user_row['profile_pic'];

				?>

				<div class="friend_req text-center">
					<img src="<?php echo $profile_pic; ?>"><a href="<?php $user_from_obj->getUsername(); ?> "><p><?php echo $user_from_obj->getTheUsername(); ?></p></a>
				<form action="requests.php" method="POST">
					<input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore">
					<input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept">
				</form><br><br><hr style="border: solid 1px #fff;">
				</div>

				<?php

			}
		}

	?>
</div>