<?php
	include("includes/header.php");

  if(isset($_GET['profile_username'])) {
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details_query);
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
        <h4>Trending<h4>
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
    
      <div class="row">
        <div class="col-lg-12">
          <div class="text-center">
            <div class="panel-body" id="top_choice">
                <a href="#">Home</a>
              	<a href="index.php">Audio</a>
				        <a href="video.php">Video</a>
            </div>
          </div>
        </div>
      </div>

        <div class="row">
        <div class="col-lg-12">
          <div class=" people">
            <div class="posts_area"></div>
            <img class="text-center" id="loading" src="assets/images/icons/load.gif">
          </div>
        </div>
     </div>
  </div>
    
  </div>

  <!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-center" id="exampleModalLongTitle">Lets make a stack!</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h5 class="text-center">This will appear on the user's profile page and also their newsfeed</h5>

        <form class="profile_post text-center" action="" method="POST" enctype="multipart/form-data">
          <div class="form_group">
            <input type="file" name="file" id="file" class="inputfile">
            <label for="file">Choose Your Track</label><br>

            <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
            <input type="hidden" name="user_to" value="<?php echo $username; ?>">

            <button type="submit" class="btn btn-primary text-center" name="submit_post" id="submit_profile_post">Post</button>
          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php

    if(isset($_POST['submit_post'])) {

      $post = new Post($con, $_POST['user_from']);
      $post->submitPost($fileDestination, $_POST['user_to']);
      
    }

?>

  <script>
    $(function(){
 
       var userLoggedIn = '<?php echo $userLoggedIn; ?>';
       var inProgress = false;
 
       loadPosts(); //Load first posts
 
       $(window).scroll(function() {
           var bottomElement = $(".status_post").last();
           var noMorePosts = $('.posts_area').find('.noMorePosts').val();
 
           // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
           if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
               loadPosts();
           }
       });
 
       function loadPosts() {
           if(inProgress) { //If it is already in the process of loading some posts, just return
               return;
           }
          
           inProgress = true;
           $('#loading').show();
 
           var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
 
           $.ajax({
               url: "includes/handlers/ajax_load_posts.php",
               type: "POST",
               data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
               cache:false,
 
               success: function(response) {
                   $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                   $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
                   $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage
 
                   $('#loading').hide();
                   $(".posts_area").append(response);
 
                   inProgress = false;
               }
           });
       }
 
       //Check if the element is in view
       function isElementInView (el) {
             if(el == null) {
                return;
            }
 
           var rect = el.getBoundingClientRect();
 
           return (
               rect.top >= 0 &&
               rect.left >= 0 &&
               rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
               rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
           );
       }
   });

  </script>
</div>

</div>
</body>
</html>