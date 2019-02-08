<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src='../stacktracks/assets/js/audio.js'></script>
<script src='../stacktracks/assets/js/video.js'></script>
<?php
	
	class Post {

		private $user_obj;
		private $con;

		public function __construct($con, $user)
		{
			$this->con = $con;
			$this->user_obj = new User($con, $user);
		}

		public function submitPost($file, $user_to) {

				$file = $_POST['file'];
        		$name = $_POST['track_name'];
        		$name = strip_tags($name);
        		$file = strip_tags($file);

				//current date and time
				$date_added = date("Y-m-d H:i:s");
				//Get username
				$added_by = $this->user_obj->getUsername();

				//if user is not on own profile, user_to is 'none'
				if($user_to == $added_by) {
					$user_to = "none";
				}

				$fileName = $_FILES['file']['name'];
		        $fileTmpName = $_FILES['file']['tmp_name'];
		        $fileSize = $_FILES['file']['size'];
		        $fileError = $_FILES['file']['error'];
		        $fileType = $_FILES['file']['type'];

		        $fileExt = explode('.', $fileName);
		        $fileActualExt = strtolower(end($fileExt));

		        $audio_allowed = array('mp3', 'WAV');
		        $video_allowed = array('mp4', 'mov');

		        if (in_array($fileActualExt, $audio_allowed)) {
		          if ($fileError === 0) {
		            if ($fileSize < 5000000000) {
		              $fileNameNew = uniqid('', true).".".$fileActualExt;
		  
		              //insert post
		              $fileDestination = 'uploads/'.$fileNameNew;
		              move_uploaded_file($fileTmpName, $fileDestination);
		              $query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$fileDestination', '$name', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");

		              $returned_id = mysqli_insert_id($this->con);

		              //update post count for user
		              $num_posts = $this->user_obj->getNumPosts();
		              $num_posts++;
		              $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

		              header("Location: index.php?success");

		            } else {
		              echo "Your file is too big";
		            }
		          } else {
		            echo "There was an error uploading your file";
		          }
		        }
		         else {
		          echo "This is not allowed";
		        }

		        if (in_array($fileActualExt, $video_allowed)) {
		          if ($fileError === 0) {
		            if ($fileSize < 5000000000) {
		              $fileNameNew = uniqid('', true).".".$fileActualExt;
		  
		              //insert post
		              $fileDestination = 'uploads/'.$fileNameNew;
		              move_uploaded_file($fileTmpName, $fileDestination);
		              $query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$fileDestination', '$name', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");

		              $returned_id = mysqli_insert_id($this->con);

		              //update post count for user
		              $num_posts = $this->user_obj->getNumPosts();
		              $num_posts++;
		              $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

		              header("Location: index.php?success");

		            } else {
		              echo "Your file is too big";
		            }
		          } else {
		            echo "There was an error uploading your file";
		          }
		        }
		         else {
		          echo "This is not allowed";
		        }

				//Insert notification
				if($user_to != 'none') {
					$notification = new Notification($this->con, $added_by);
					$notification->insertNotification($returned_id, $user_to, "stack");
				}
		}

		public function loadPostsFriends($data, $limit) {

				$page = $data['page'];
				$userLoggedIn = $this->user_obj->getUsername();

				if($page = 1)
					$start = 0;
				else
					$start = ($page -1) * $limit;

				$str = "";
				$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

				if(mysqli_num_rows($data_query) > 0) {

					$num_iterations = 0;//Number of results checked
					$count = 1;


					while($row = mysqli_fetch_array($data_query)) {
						$id = $row['id'];
						$file = $row['file'];
						$name = $row['name'];
						$added_by = $row['added_by'];
						$date_time = $row['date_added'];

						$info = pathinfo($file);

						//Check if user who posted has their account closed
						$added_by_obj = new User($this->con, $added_by);
						if($added_by_obj->isClosed()) {
							continue;
						}

						if($num_iterations++ < $start)
							continue;

						//Once 10 posts have been loaded, break
						if($count > $limit) {
							break;
						}
						else {
							$count++;
						}

						if($userLoggedIn == $added_by)
							$delete_button = "<button class='delete_button' id='post$id'>X</button>";
						else
							$delete_button = "";

						$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM users WHERE username='$added_by'");
						$user_row = mysqli_fetch_array($user_details_query);
						$username = $user_row['username'];
						$profile_pic = $user_row['profile_pic'];

						?>
							<script>
								function toggle<?php echo $id;?>() {

									var target = $(event.target);
									if (!target.is("a")) {
										var element = document.getElementById("toggleComment<?php echo $id;?>");

								 		if(element.style.display == "block")
								 			element.style.display = "none";
								 		else
								 			element.style.display = "block";
									}

	 						}
							</script>
						<?php

						$comment_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
						$comment_check_num = mysqli_num_rows($comment_check);

						//Timeframe
						$date_time_now = date("Y-m-d H:i:s");
						$start_date = new DateTime($date_time);
						$end_date = new DateTime($date_time_now);
						$interval = $start_date->diff($end_date);
						if($interval->y >= 1) {
							if($interval == 1)
								$time_message = $interval->y . " year ago";
							else
								$time_message = $interval->y . " years ago";
						}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {
								$days = " ago";
							}
							else if($interval->d == 1) {
								$days = $interval->d . " day ago";
							}
							else {
								$days = $interval->d . " days ago";
							}

							if($interval->m == 1) {
								$time_message = $interval->m . " month ". $days;
							}
							else {
								$time_message = $interval->m . " months ". $days;
							}
						}
						else if($interval->d >= 1) {
							if($interval->d == 1) {
								$time_message = "Yesterday";
							}
							else {
								$time_message = $interval->d . " days ago";
							}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {
								$time_message = $interval->h . " hour ago";
							}
							else {
								$time_message = $interval->h . " hours ago";
							}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {
								$time_message = $interval->i . " minute ago";
							}
							else {
								$time_message = $interval->i . " minutes ago";
							}
						}
						else {
							if($interval->s < 30) {
								$time_message = "Just now";
							}
							else {
								$time_message = $interval->s . " seconds ago";
							}
						}


						if($info["extension"] == "mp3"){
						$str .= "
								<button type='submit' class='launch' data-toggle='modal' data-target='#post_form' id='stack' style='padding: 4px 12px; bottom: 0;'>
							        Stack
							    </button>
								<div class='status_post'>
									$delete_button
									<div class='post_profile_pic'>
										<img src='$profile_pic' >
									</div>

									<div class='posted_by' style='color: #ACACAC;'>
										<a href='$added_by'> $username </a>  <br>$time_message
									</div>
									
									<div id='post_file' Content-type: 'audio/mp3' Content-transfer-encoding:'binary' >
								
									<div class='firePlayer'>

								      <div class='controls'>

								      <audio src='$file'></audio>

								        <div class='button-wrap'>
								          <button class='audio-play'></button>
								        </div>

								        <div class='progress-wrap'>

								          <div class='audio-title'>$name</div>
								          <div class='audio-current-time'>0:00</div>
								          <div class='audio-seekbar' value='0' max='1'>
								            <div class='audio-slide'></div>
								          </div>
								          <div class='audio-length'>0:00</div>

								        </div>

								      </div>

								    </div>
									
								</div>

									<div class = 'newsfeedPostOptions'>
										<i class='far fa-comment' style='color:#000; padding-right: 4px;'></i><span id='message_click' onClick='javascript:toggle$id()'>Comments($comment_check_num)&nbsp;&nbsp;&nbsp;</span>
										<i class='far fa-thumbs-up' style='color:#000; padding-right: 4px;'></i><iframe src='like.php?post_id=$id' scrolling='no'></iframe>
									</div>

								</div>
								<br>
								<div class='post_comment' id='toggleComment$id' style='display:none; overflow:hidden;'>
									<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' style='overflow:auto;'></iframe>
								</div>
								<hr>";
						}
						else if($info["extension"] == "mp4") {

							$str .= "<div class='status_post'>
									$delete_button
									<div class='post_profile_pic'>
										<img src='$profile_pic' >
									</div>

									<div class='posted_by' style='color: #ACACAC;'>
										<a href='$added_by'> $username </a> $user_to <br>$time_message
									</div>
									
									<div id='post_file' Content-type: 'video/mp4' Content-transfer-encoding:'binary' >
										<video src=$file controls>
									</div>

									<div class = 'newsfeedPostOptions'>
										<span id='message_click' onClick='javascript:toggle$id()'>Comments($comment_check_num)&nbsp;&nbsp;&nbsp;</span>
										<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
									</div>

								</div>
								<br>
								<div class='post_comment' id='toggleComment$id' style='display:none; overflow:hidden;'>
									<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' style='overflow:auto;'></iframe>
								</div>
								$delete_button
								<hr>";

						}
						else {
							$str .= "";
						}


						?>

							<script>
								$(document).ready(function() {
									$('#post<?php echo $id; ?>').on('click', function() {
										bootbox.confirm("Are you sure you want to delete this post?", function(result) {
											$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
											if(result)
												location.reload();
										});
									});
								});

							</script>

						<?php

					}//end of while loop

				if($count > $limit)
					$str.= "<input type='hidden' class='nextPage' value='" . ($page +1) . "'><input type='hidden' class='noMorePosts' value='false'>";
				else
					$str.= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'>No more tracks to show!</p>";
			

				}

				echo $str;
				
		}

		public function loadProfilePosts($data, $limit) {

				$page = $data['page'];
				$profileUser = $data['profileUsername'];
				$userLoggedIn = $this->user_obj->getUsername();

				if($page = 1)
					$start = 0;
				else
					$start = ($page -1) * $limit;

				$str = "";
				$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY id DESC");

				if(mysqli_num_rows($data_query) > 0) {

					$num_iterations = 0;//Number of results checked
					$count = 1;


				while($row = mysqli_fetch_array($data_query)) {
					$id = $row['id'];
					$file = $row['file'];
					$name = $row['name'];
					$added_by = $row['added_by'];
					$date_time = $row['date_added'];

					$info = pathinfo($file);

					if($num_iterations++ < $start)
						continue;

					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>X</button>";
					else
						$delete_button = "";

					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
						<script>
							function toggle<?php echo $id;?>() {

								var target = $(event.target);
								if (!target.is("a")) {
									var element = document.getElementById("toggleComment<?php echo $id;?>");

							 		if(element.style.display == "block")
							 			element.style.display = "none";
							 		else
							 			element.style.display = "block";
								}

 						}
						</script>
					<?php

					$comment_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comment_check_num = mysqli_num_rows($comment_check);

					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time);
					$end_date = new DateTime($date_time_now);
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago";
						else
							$time_message = $interval->y . " years ago";
					}
					else if ($interval-> m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " days ago";
						}

						if($interval->m == 1) {
							$time_message = $interval->m . " month ". $days;
						}
						else {
							$time_message = $interval->m . " months ". $days;
						}
					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " days ago";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						}
						else {
							$time_message = $interval->h . " hours ago";
						}
					}
					else if($interval->i >= 1) {
						if($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						}
						else {
							$time_message = $interval->i . " minutes ago";
						}
					}
					else {
						if($interval->s < 30) {
							$time_message = "Just now";
						}
						else {
							$time_message = $interval->s . " seconds ago";
						}
					}

					if($info["extension"] == "mp3") {
					$str .= "

							<button type='submit' class='launch' data-toggle='modal' data-target='#post_form' id='stack' style='padding: 4px 12px;'>
						        Stack
						    </button>

							<div class='status_post'>
								
								<div class='post_profile_pic'>
									<img src='$profile_pic' >
								</div>

								<div class='posted_by' style='color: #ACACAC;'>
									<a href='$added_by'> $username </a> <br>$time_message
									
								</div>
								<div id='post_file' Content-type: 'audio/mp3' Content-transfer-encoding:'binary' >
								
									<div class='firePlayer'>

								      <div class='controls'>

								        <audio src='$file'></audio>
				

								        <div class='button-wrap'>
								          <button class='audio-play'></button>
								        </div>

								        <div class='progress-wrap'>

								          <div class='audio-title'>$name</div>
								          <div class='audio-current-time'>0:00</div>
								          <div class='audio-seekbar' value='0' max='1'>
								            <div class='audio-slide'></div>
								          </div>
								          <div class='audio-length'>0:00</div>

								        </div>

								      </div>

								    </div>
									
								</div>

								<div class = 'newsfeedPostOptions'>
									<span id='message_click' onClick='javascript:toggle$id()'>Comments($comment_check_num)&nbsp;&nbsp;&nbsp;</span>
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<br>
							<div class='post_comment' id='toggleComment$id' style='display:none; overflow:hidden;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' style='overflow:auto;'></iframe>
							</div>
							<hr>";
					}
					else if($info["extension"] == "mp4") {

						$str .= "<div class='status_post'>
								$delete_button
								<div class='post_profile_pic'>
									<img src='$profile_pic' >
								</div>

								<div class='posted_by' style='color: #ACACAC;'>
									<a href='$added_by'> $username </a> <br>$time_message
								</div>
								
								<div id='post_file' Content-type: 'video/mp4' Content-transfer-encoding:'binary' >
									<video src=$file controls>
								</div>

								<div class = 'newsfeedPostOptions'>
									<span id='message_click' onClick='javascript:toggle$id()'>Comments($comment_check_num)&nbsp;&nbsp;&nbsp;</span>
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<br>
							<div class='post_comment' id='toggleComment$id' style='display:none; overflow:hidden;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' style='overflow:auto;'></iframe>
							</div>
							<hr>";

					}
					else {
						$str .= "";
					}

					?>

						<script>
							$(document).ready(function() {
								$('#post<?php echo $id; ?>').on('click', function() {
									bootbox.confirm("Are you sure you want to delete this post?", function(result) {
										$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
										if(result)
											location.reload();
									});
								});
							});
						</script>

					<?php


				}//End while loop				

				if($count > $limit)
					$str.= "<input type='hidden' class='nextPage' value='" . ($page +1) . "'><input type='hidden' class='noMorePosts' value='false'>";
				else
					$str.= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center;'>No more tracks to show!</p>";
			
		    	}

		    	echo $str;

		}

		public function getSinglePost($post_id) {

				$userLoggedIn = $this->user_obj->getUsername();

				$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

				$str = "";
				$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");

				if(mysqli_num_rows($data_query) > 0) {

				$row = mysqli_fetch_array($data_query); 
					$id = $row['id'];
					$file = $row['file'];
					$name = $row['name'];
					$added_by = $row['added_by'];
					$date_time = $row['date_added'];

					//Prepare user_to string so it can be included if not posted to a user
					if($row['user_to'] == "none") {
						$user_to = "";
					}
					else {
						$user_to_obj = new User($this->con, $row['user_to']);
						$user_to_name = $user_to_obj->getUsername();
						$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
					}

					//Check if user who posted has their account closed
					$added_by_obj = new User($this->con, $added_by);
					if($added_by_obj->isClosed()) {
						return;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button btn-danger' id='post{$id}'>X</button>";
					else
						$delete_button = "";

					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
						<script>
							function toggle<?php echo $id;?>() {

								var target = $(event.target);
								if (!target.is("a")) {
									var element = document.getElementById("toggleComment<?php echo $id;?>");

							 		if(element.style.display == "block")
							 			element.style.display = "none";
							 		else
							 			element.style.display = "block";
								}

 							}
						</script>
					<?php

					$comment_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comment_check_num = mysqli_num_rows($comment_check);

					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time);
					$end_date = new DateTime($date_time_now);
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago";
						else
							$time_message = $interval->y . " years ago";
					}
					else if ($interval-> m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " days ago";
						}

						if($interval->m == 1) {
							$time_message = $interval->m . " month ". $days;
						}
						else {
							$time_message = $interval->m . " months ". $days;
						}
					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " days ago";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						}
						else {
							$time_message = $interval->h . " hours ago";
						}
					}
					else if($interval->i >= 1) {
						if($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						}
						else {
							$time_message = $interval->i . " minutes ago";
						}
					}
					else {
						if($interval->s < 30) {
							$time_message = "Just now";
						}
						else {
							$time_message = $interval->s . " seconds ago";
						}
					}

					$str .= "<div class='status_post'>
								<div class='post_profile_pic'>
									<img src='$profile_pic' >
								</div>

								<div class='posted_by' style='color: #ACACAC;'>
									<a href='$added_by'> $username </a> $user_to <br>$time_message
									
								</div>
								
								<div id='post_file' Content-type: 'audio/mp3' Content-transfer-encoding:'binary' >
								
									<div class='firePlayer'>

								      <div class='controls'>

								        <audio src='$file'></audio>
				

								        <div class='button-wrap'>
								          <button class='audio-play'></button>
								        </div>

								        <div class='progress-wrap'>

								          <div class='audio-title'>$name</div>
								          <div class='audio-current-time'>0:00</div>
								          <div class='audio-seekbar' value='0' max='1'>
								            <div class='audio-slide'></div>
								          </div>
								          <div class='audio-length'>0:00</div>

								        </div>

								      </div>

								    </div>
								</div>

								<div class = 'newsfeedPostOptions'>
									<span id='message_click' onClick='javascript:toggle$id()'>Comments($comment_check_num)&nbsp;&nbsp;&nbsp;</span>
									<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								</div>

							</div>
							<br>
							<div class='post_comment' id='toggleComment$id' style='display:none; overflow:hidden;'>
								<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0' style='overflow:auto;'></iframe>
							</div>
							<hr>";

					?>

						<script>
							$(document).ready(function() {
								$('#post<?php echo $id; ?>').on('click', function() {
									bootbox.confirm("Are you sure you want to delete this post?", function(result) {
										$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
										if(result)
											location.reload();
									});
								});
							});
						</script>

					<?php

				}//end of if statement
				else {
					echo "<p>No post found. If you clicked a link, it may be broken.</p>";
					return;
				}

				echo $str;

		}	

	} 

?>