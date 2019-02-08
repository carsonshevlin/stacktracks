<?php 

include("includes/header.php");

if(isset($_POST['submit'])) {
    $post = new Post($con, $userLoggedIn);
    $post->submitPost($fileDestination, 'none');
}

?>

<div class="modal fade" id="audioModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<h3 class="text-center">Audio Upload</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload">
        <input type="file" name="file" id="file" class="inputfile" required />
        <label for="file">Choose Your Track</label>
        <p>Title</p>
        <input type="text" name="track_name" id="track_name" autocomplete="off"><br>
        <p>Genre</p>
        <input type="text" name="genre_name" id="track_name" autocomplete="off"><br>
        <button type="submit" name="submit" class="upload_button">Upload</button>
        <button type="submit" onClientClick="Cancel();" class="upload_button">Cancel</button>
      </form>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="recordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="text-center">Video Record</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="text-center record_box">
        <video onclick="snapshot(this);" width="250" height="250" id="video" controls autoplay></video><br>
        <button onclick="startWebcam();" id="record_button">Start WebCam</button>
        <button onclick="stopWebcam();" id="record_button">Stop WebCam</button>
        <button onclick="snapshot();" id="record_button">Record</button>
      </div> 
      <div class="modal-footer">
      <form action="upload.php" method="POST" enctype="multipart/form-data" class="text-center">
      <button type="submit" name="submit">Upload</button>
      <button type="submit" onClientClick="Cancel();">Cancel</button>
    </form>
      </div>
    </div>
  </div>
</div>

<div class="container background">
	<div class="text-center upload_box">
		<h2>Lets Start Stacking</h2>
		<button type="button" data-toggle="modal" data-target="#audioModal">
  			Upload Your Track
		</button>
    <button type="button" data-toggle="modal" data-target="#recordModal">
        Record Your Track
    </button>
	</div>

  <div class="upload_info">
    <p>What types of files can I upload? You can upload AIFF, WAVE (WAV), FLAC, ALAC, OGG, MP2, MP3, AAC, AMR, and WMA files. The maximum file size is 5GB.</p>
    <p>Important: By sharing and stacking, you agree to not infringe on anyone else's work.</p>
  </div>

</div>

</div>
</body>
</html>

<script>
	function Cancel(){
     <%= uploadProgress.ClientID %>_obj.CancelRequest();
</script>