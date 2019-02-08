<?php
	
	require '../../config/config.php';

	if(isset($_GET['post_id']))
		$post_id = $_GET['post_id'];
	if(isset($_POST['result'])) {
		if($_POST['result'] == 'true')
			$query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
			$user_obj = new User($con, $userLoggedIn);
			$added_by = $user_obj->getUsername();
			$update_query = mysqli_query($con, "UPDATE users SET num_posts=num_posts - 1 WHERE username='$added_by'");
	}

?>