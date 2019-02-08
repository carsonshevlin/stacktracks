<?php

include("includes/header.php");

if(isset($_GET['id'])) {
	$id = $_GET['id'];
}
else {
	$id = 0;
}

?>

<div class="container text-center whole">    
  <div class="row">
    <div class="col-lg-3 well">
      <div class="well" id="profile_name">
        <p><strong><a href="<?php echo $userLoggedIn; ?>">
        <?php echo $user['username'];  
        ?></a></strong><br>
			 </p>
        <img id="profile" src="<?php echo $user['profile_pic']; ?>">
      </div>
      <div class= "well" id="trend_art">
        <h4>Trending</h4>
        <?php

        $trends_query = mysqli_query($con, "SELECT * FROM posts WHERE deleted='no' AND likes > 3");

        while($trends_row = mysqli_fetch_array($trends_query)) {

            $added_by = $trends_row['added_by'];
            $trend_users = mysqli_query($con, "SELECT username, profile_pic FROM users WHERE username='$added_by'");
            $trends_users = mysqli_fetch_array($trend_users);

              echo "
              <div class='trend_post'>
                <div class='trend_profile_pic'>
                  <img src='" . $trends_users['profile_pic'] . "' >
                </div>

                <div class='posted_by' style='color: #ACACAC;'>
                  <a href='$added_by' style='font-size: 15px;'>" . $trends_users['username'] . " </a>
                </div>

                <div class='trend_file' Content-type: 'audio/mp3' Content-transfer-encoding:'binary'>
                  <audio src='" . $trends_row['file'] . "' style='display:block; width:200px; transform:translateX(-15px);' controls></audio>
                </div>
              </div>";
            } 

        ?>
      </div>
    </div>

    <div class="col-lg-9">
    
      <div class="row view_choice">
        <div class="col-lg-12">
          <div class="panel panel-default text-left">
            <div class="panel-body" id="top_choice">
              	<a href="index.php">Home</a>
            </div>
          </div>
        </div>
      </div>

        <div class="row">
        <div class="col-lg-12">
          <div class=" people">
            <div class="posts_area">
            <?php 
      				$post = new Post($con, $userLoggedIn);
      				$post->getSinglePost($id);
			       ?>
            </div>
          </div>
        </div>
     </div>
  </div>
    
  </div>