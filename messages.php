<?php
include("includes/header.php");

$message_obj = new Message($con, $userLoggedIn);

if(isset($_GET['u']))
	$user_to = $_GET['u'];
else {
	$user_to = $message_obj->getMostRecentUser();
	if($user_to == false)
		$user_to = 'new';
}

if($user_to != "new")
	$user_to_obj = new User($con, $user_to);

if(isset($_POST['post_message'])) {

	if(isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($user_to, $body, $date);
	}
}

?>

<div class="big_message">
<div class="row well conbox">
	<div class="col-lg-9">
	<?php
	if($user_to != "new") {
		echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getTheUsername() . "</a></h4><hr><br>";
		echo "<div class='loaded_messages well' id='scroll_messages' style='overflow: scroll; background-color: #fff;'>";
			echo $message_obj->getMessages($user_to);
		echo "</div>";
	}
	else {
		echo "<h2 class='text-center'>Messages</h2>";
	}
	?>

	<div class="message_post">
		<form action="" method="POST">
			<?php
			if($user_to == "new") {
				echo "<p class='text-center' style='font-size: 18px;'>Select the friend you would like to message<p> <br><br>";
				?>
				<input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='seach_text_input'>
				<?php
				echo "<div class='results'></div>";
			}
			else {
				echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message..'></textarea>";
				echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
			}
			?>
		</form>
	</div>

	<script>
	    var div = document.getElementById("scroll_messages");
	    if(div != null) {
	        div.scrollTop = div.scrollHeight;
	    }
	</script>

	</div>
	<div class="col-lg-3 row user_details" id="conversations">
		<h4 class="text-center">Conversations</h4>

		<div class="loaded_conversations well" style="background-color: #fff;">
			<?php echo $message_obj->getConvos(); ?>
		</div>
		<br>
		<a href="messages.php?u=new" id="newmes" class="text-center">New Message</a>
	</div>
</div>
</div>