<?php
	include("includes/header.php");
  include("includes/form_handlers/info_handler.php");

  $message_obj = new Message($con, $userLoggedIn);

  if(isset($_GET['profile_username'])) {
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
  }

  if(isset($_POST['remove_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->removeFriend($username);
  }

  if(isset($_POST['add_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->sendRequest($username);
  }

  if(isset($_POST['respond_request'])) {
    header("Location: requests.php");
  }

  if(isset($_POST['post_message'])) {
    if(isset($_POST['message_body'])) {
      $body = mysqli_real_escape_string($con, $_POST['message_body']);
      $date = date("Y-m-d H-i-s");
      $message_obj->sendMessage($username, $body, $date);
    }

    $link = '#profileTabs a[href="#messages_div"]';
    echo "
    <script>

      $(function() {
          $('". $link ."').tab('show');
        });

    </script>";

  }

  $user_details_query = mysqli_query($con, "SELECT username, profile_pic FROM users WHERE username='$username'");
  $user_row = mysqli_fetch_array($user_details_query);
  $profile_pic = $user_row['profile_pic'];

  $user_data_query = mysqli_query($con, "SELECT * FROM user_info WHERE user='$username'");
  $info_row = mysqli_fetch_array($user_data_query);

  $hometown = $info_row['hometown'];
  $instrument = $info_row['instrument'];
  $genre = $info_row['genre'];
  $banner_pic = $info_row['banner_pic'];
        
?>

	<div class="container text-center" style="padding-bottom: 20px;">
		<div class="row">
			<div class="col-lg-12">
				<div id="profile_page">
          <img class='banner_pic' src="<?php echo $banner_pic; ?>">
          <?php
            if($userLoggedIn == $username) {
              echo '<a id="banner_icon" href="upload_banner_pic.php"><i class="fas fa-camera"></i></a>';
            }
            else {
              echo "";
            }
          ?>
					<img id='profile_pic' src="<?php echo $profile_pic; ?>">
          <br>
					<strong><p style="text-decoration: none; "><?php echo $username ?></p></strong>
          <form action="<?php echo $username; ?>" method="POST">
            <div class="profile_line">
              <?php 
                $profile_user_obj = new User($con, $username); 

                if($profile_user_obj->isClosed()) {
                  header("Location: user_closed.php");
                }

                $logged_in_user_obj = new User($con, $userLoggedIn);

                if($userLoggedIn != $username) {
                  if($logged_in_user_obj->isFriend($username)) {
                    echo '<input class="friend_button" type="submit" name="remove_friend" value="Remove Friend"<br>';
                  }
                  else if($logged_in_user_obj->didReceiveRequest($username)) {
                    echo '<input class="friend_button" type="submit" name="respond_request" value="Respond to Request"<br>';
                  }
                  else if($logged_in_user_obj->didSendRequest($username)) {
                    echo '<input class="friend_button" type="submit" name=""  value="Request Sent"<br>';
                  }
                  else {
                    echo '<input class="friend_button" type="submit" name="add_friend" value="Add Friend"<br>';
                  }
                } 

              ?>
              </div>
            </form>

            <div class="profile_line">
              <?php
                if($userLoggedIn != $username) {
                  echo "<a href='messages.php?u=" . $username . "' class='friend_button' id='profile_message'>Message</a>";
                }
              ?>
            </div>

				</div>
			</div>
		</div>

    <?php

      if($userLoggedIn != $username) {
        $message_tab = "<li role='presentation'><a href='#messages_div' aria-controls='messages_div' role='tab' data-toggle='tab'>Messages</a></li>";
      }
      else {
        $message_tab = NULL;
      }

    ?>

		<div class="row">
        <div class="col-lg-9">
          <div class="panel panel-default text-left" id="main">
            <ul class="nav nav-tabs profiletab" style="padding-left: 5px; padding-top: 20px;">
              <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Tracks <?php echo "(".$user_array['num_posts'].")"; ?></a></li>
              <li role="presentation"><a href="#stacks_div" aria-controls="stacks_div" role="tab" data-toggle="tab">Stacks</a></li>
              <li role="presentation"><a href="#about_div" aria-controls="about_div" role="tab" data-toggle="tab">Friends <?php echo "($num_friends)"; ?></a></li>
              <?php echo $message_tab; ?>
            </ul>

            <div class="tab-content">
              
              <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
                <div class="panel-body">
                  <div class="posts_area"></div>
                  <?php

                    if($user_array['num_posts'] == 0) {
                      if($userLoggedIn == $username) {
                        echo "<h4>When you upload tracks, they will go here.</h4><br>
                        <a href='upload.php' id='profile_upload_button'><i class='far fa-arrow-alt-circle-up'></i><br><p>Upload</p></a>";
                      }
                      else {
                        echo "<h4>This user has not uploaded anything yet.</h4>";
                      }
                      $load_icon = "";
                    }
                    else {
                      $load_icon = "<img id='loading' src='assets/images/icons/load.gif'>";
                    }

                    echo $load_icon;

                  ?>
                  
                </div>
              </div>

              <div role="tabpanel" class="tab-pane fade" id="stacks_div">
                <div class="panel-body">
                  <img id="loading" src="assets/images/icons/load.gif">
                </div>
              </div>

              <div role="tabpanel" class="tab-pane fade" id="about_div">

                <?php
  
                 $user = $_SESSION['username'];
                 $user = new User($con, $username);
                 $rez = array();
                 $rez = $user->getFriendArray();
                 $friend_array_string = trim($rez, ",");
                 
                 if ($friend_array_string != "") {
                 
                   $no_commas = explode(",", $friend_array_string);
                   
                   foreach ($no_commas as $key => $value) {
                 
                     $friend = mysqli_query($con, "SELECT * FROM users WHERE username='$value'");
                     $row = mysqli_fetch_assoc($friend);

                     if($userLoggedIn == $username) {
                        $button = "<input type='submit' name='" . $row['username'] . "' class='friends_button' value='Remove Friend'>";
                     }
                     else {
                      $button = "";
                     }

                     if($userLoggedIn != $username) {
                      $mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";
                     }
                     else {
                      $mutual_friends = "";
                     } 

                     if(isset($_POST[$row['username']])) {
                      $user->removeFriend($row['username']);
                      header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                     }

                     echo "<div class='search_result'>
                            <div class='searchPageFriendButtons'>
                              <form action='' method='POST'>
                                " . $button . "
                              </form>
                            </div>

                            <div class='result_profile_pic'>
                              <a href='" . $row['username'] . "'><img src='" . $row['profile_pic'] . "' style='height: 100px;'></a>
                            </div>

                              <a href='" . $row['username'] . "' style='color: #000; float: left;'> " . $row['first_name'] . " " . $row['last_name'] . "
                              <br><p id='grey'>" . $row['username'] . "</p>
                              </a>
                              <br><br>
                              <p style='float:right;' id='grey'>" . $mutual_friends . "</p><br>
                          </div>
                          <hr id='search_hr'>";
                   
                  }

                }
                 
                else {
                 
                 if($userLoggedIn == $username)
                  echo "<br><p>You have no friends at this.</p>";
                 else
                  echo "<br><p>This user has no friends at this time. Maybe you could be the first!</p>";
                 
                } 

                ?>

              </div>

              <div role="tabpanel" class="tab-pane fade" id="messages_div">
                
                <?php

                  echo "<h4 style='color:#000;'>You and  " . $username . " </h4><hr><br>";
                  echo "<div class='loaded_messages' id='scroll_messages' style='overflow: hidden;'>";
                    echo $message_obj->getMessages($username);
                  echo "</div>";
                ?>

                <div class="message_post">
                  <form action="" method="POST" style="margin-right: 125px;">
                       <textarea name='message_body' id='message_textarea' placeholder='Write your message..' style="color: #000;"></textarea>
                      <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
                  </form>
                </div>

                <script>
                    var div = document.getElementById("scroll_messages");
                    if(div != null) {
                        div.scrollTop = div.scrollHeight;
                    }
                </script>

              </div>

            </div>

          </div>
        </div>

          <!-- Modal -->
        <div class="modal fade" id="info_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-center" id="exampleModalLongTitle">Personal Info</h4>
              <h5 class="text-center">Details you add here will be public</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              
              <form class="profile_info text-center" action="" method="POST">
                <div class="form_group">

                  Hometown: <input type="text" name="hometown" autocomplete="off" value="<?php echo $hometown; ?>"><br>
                  Instrument of choice: <input type="text" name="instrument" autocomplete="off" value="<?php echo $instrument; ?>"><br>
                  Music genre of choice: <input type="text" name="genre" autocomplete="off" value="<?php echo $genre; ?>"><br>

                </div>
              </form>

            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" name="submit_info" id="submit_profile_post" style="top: 1px;">Save</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <?php

      if($username == $userLoggedIn)
        $info_button = "<button id='info_button' data-toggle='modal' data-target='#info_form'><i class='fas fa-pencil-alt'></i> Edit</button>";
      else
        $info_button = "";

      ?>

        <div class="col-lg-3">
          <div class="panel panel-default text-left profile_info_box">
          	<div class="panel-body profile_info">
              <h4 class="text-center">Basic Info <?php echo $info_button ?></h4><hr>
              <i class="fas fa-home "></i><p>Hometown: <?php echo $hometown; ?></p><br>
              <i class="fas fa-music "></i><p>Instrument of Choice: <?php echo $instrument; ?></p><br>
              <i class="fas fa-music "></i><p>Favorite Genre: <?php echo $genre; ?></p><br> 
          	</div>

          </div>
        </div>
      </div>

	</div>

    <script>
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $username;?>';

    $(document).ready(function() {

      $('#loading').show();

      //original ajax request for loading first posts
      $.ajax({
        url: "includes/handlers/ajax_load_profile_posts.php",
        type: "POST",
        data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
        cache:false,

        success: function(data) {
          $('#loading').hide();
          $('.posts_area').html(data);
        }
      });

      $(window).scroll(function() {
        var height = $('.posts_area').height(); //div containing posts
        var scroll_top = $(this).scrollTop();
        var page = $('.posts_area').find('.nextPage').val();
        var noMorePosts = $('.posts_area').find('.noMorePosts').val();

        if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
          $('#loading').show();

          var ajaxReg = $.ajax({
            url: "includes/handlers/ajax_load_profile_posts.php",
            type: "POST",
            data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
            cache:false,

            success: function(response) {
              $('.posts_area').find('.nextPage').remove(); //Removes current page
              $('.posts_area').find('.noMorePosts').remove();
              $('#loading').hide();
              $('.posts_area').append(response);
            }
          });

        } //End if

        return false;

      }); //End  $(window).scroll(function() 

    });
  </script>

</div>
</body>
</html>