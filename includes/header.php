<?php 
	require 'config/config.php';
	include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Message.php");
    include("includes/classes/Notification.php");

	if (isset($_SESSION['username'])) {
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: register.php");
	}
 ?>

<html>
<head>
	<title>stacktracks</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../stacktracks/assets/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../stacktracks/assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="../stacktracks/assets/css/profile.css">
	<link rel="stylesheet" type="text/css" href="../stacktracks/assets/css/upload_style.css">
	<link rel="stylesheet" href="../stacktracks/assets/css/jquery.Jcrop.css" type="text/css" />
	<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.2.0/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="../stacktracks/assets/js/bootstrap.js"></script>
	<script src="../stacktracks/assets/js/bootbox.min.js"></script>
	<script src="../stacktracks/assets/js/jquery.jcrop.js"></script>
	<script src="../stacktracks/assets/js/jcrop_bits.js"></script>
	<script src="../stacktracks/assets/js/video.js"></script>
	<script src="../stacktracks/assets/js/main.js"></script>

</head>
<body class="thebody">

	<div class="top-bar">
		<div class="stacktracks">
			<a id="name" href="index.php">stacktracks</a>
			<a href="upload.php" id="upload">Upload</a>
		</div>

		<div class="search">
			
			<form action="search.php" method="GET" name="search_form">
				<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search artists..." autocomplete="off" id="search_text_input">

				<div class="button_holder">
					<img src="assets/images/icons/magnifying_glass.png">
				</div>

			</form>

			<div class="search_results"></div>

			<div class="search_results_footer_empty">
			</div>

		</div>

		<nav class="navv">

			<?php 

			//Unread messages
			$messages = new Message($con, $userLoggedIn);
			$num_messages = $messages->getUnreadNumber();

			//Unread notifications
			$notifications = new Notification($con, $userLoggedIn);
			$num_notifications = $notifications->getUnreadNumber();

			//Unread friend requests
			$user_obj = new User($con, $userLoggedIn);
			$num_requests = $user_obj->getNumberOfFriendRequests();

			?>

			<a class="artist" href="<?php echo $userLoggedIn; ?>">
				<i class="fas fa-user"></i>
				<?php 
				echo $user['username'];
				?>
			</a>
			<a href="requests.php">
				<i class="fas fa-users"></i> Requests
				<?php
				if($num_requests > 0)
				echo '<span class="notification_badge" id="unread_request">' . $num_requests . '</span>';
				?>
			</a>
			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')"><i class="fas fa-comments"></i>
				<?php
				if($num_messages > 0)
				echo '<span class="notification_badge" id="unread_message">' . $num_messages . '</span>';
				?>
			</a>
			<a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
				<i class="fas fa-bell"></i>
				<?php
				if($num_notifications > 0)
				echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
				?>
			</a>
			<a href="settings.php"><i class="fas fa-cog"></i></a>
			<a href="includes/handlers/logout.php"><i class="fas fa-sign-out-alt"></i></a>
		</nav>

		<div class="dropdown_data_window" style="height: 0px; border: none;"></div>
		<input type="hidden" id="dropdown_data_type" value="">

	</div>

	<script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';

    $(document).ready(function() {

      $('.dropdown_data_window').scroll(function() {
        var inner_height = $('.dropdown_data_window').inner_Height(); //div containing data
        var scroll_top = $('.dropdown_data_window').scrollTop();
        var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
        var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

        if((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

        	var pageName; //Holds name of page to ajax request to
        	var type = $('#dropdown_data_type').val();

        	if(type = 'notification')
        		pageName = "ajax_load_notification.php";
        	else if(type = 'message')
        		pageName = "ajax_load_messages.php";

          var ajaxReg = $.ajax({
            url: "includes/handlers/" + pageName,
            type: "POST",
            data: "page=" + page + "&userLoggedIn" + userLoggedIn,
            cache:false,

            success: function(response) {
              $('.dropdown_data_window').find(".nextPageDropdownData").remove(); //Removes current page
              $('.dropdown_data_window').find(".noMoreDropdownData").remove();
              $('.dropdown_data_window').append(response);
            }
          });

        } //End if

        return false;

      }); //End  $(window).scroll(function() 

    });

  </script>

	<div class="wrapper">
		

	
			