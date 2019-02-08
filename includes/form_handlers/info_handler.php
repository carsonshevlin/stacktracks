<?php

if(isset($_POST['submit_info'])) {

	$hometown = strip_tags($_POST['hometown']);
	$instrument = strip_tags($_POST['instrument']);
	$genre = strip_tags($_POST['genre']);

	$info_query = mysqli_query($con, "UPDATE user_info SET hometown='$hometown', instrument='$instrument', genre='$genre' WHERE user='$userLoggedIn'");
}

?>