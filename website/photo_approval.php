<?php

include 'firebase_include_js.php';

?>

<style>
  #photo{
    height: 500px;
  }

  #back-btn, #next-btn{
    float: left;
    width: 45%;
  }

  #img-container{
    text-align: center;
  }

  #nav-btn-container{
  }

  #img-info-container{
    margin-top: 85px;
  }

  .btn-container{
    text-align: center;
    align-items: center;
  }

  .big-btn{
    width: 95%;
    height: 5em;
  }

  .big-btn h2 {
      color: white;
      font-weight: bold;
      margin-top: 5px; /* Vertically centers */
  }

  .approve-green{
    background-color: green;
  }

  .approve-green:hover{
    background-color: darkgreen
  }

  .reject-red{
    background-color: red;
  }

  .reject-red:hover{
    background-color: darkred;
  }

  a.img-nav-btn {
      text-decoration: none;
      font-size: 30px;
      font-weight: bold;

      display: inline-block;
      margin-bottom: 50px;
      padding: 8px 16px;
      padding-top: 6px;
      padding-bottom: 10px;

      background-color: black;
      color: white;
  }

  a.img-nav-btn.prev{
    float: left;
  }

  a.img-nav-btn.next{
    float: right;
  }

  a.img-nav-btn:hover {
      background-color: #ddd;
      color: black;
  }

  .round {
      border-radius: 50%;
  }

</style>

<script src="admin_restriction.js"></script>
<script>
  enableAuthenticationRequirement("photo_approval", function() {});
</script>


<div class="content-wrapper clearfix restricted">
  <div class="container">

    <div class="sixteen columns">
      <div class="page-title clearfix">
        <h1>Approve Photos</h1>
      </div>
    </div>

    <div class="clear"></div>

    <h1 id="num-approved"></h1>

    <h4><input id="photo-checkbox" type="checkbox" onclick="boxClicked()">Edit photos which have already been rated</input></h4>

    <!--p>
      <button id="back-btn" class="big-btn" onclick="toPrevPhoto()"><h2>Prev</h2></button>
      <button id="next-btn" class="big-btn" onclick="toNextPhoto()"><h2>Next</h2></butto>
      <a href="#" class="img-nav-btn previous round">&#8249;</a>
      <a href="#" class="img-nav-btn next round">&#8250;</a>
    </p-->

    <div id="img-container">
      <img id="photo" align="middle">
    </div>
    <div id="nav-btn-container">
      <a href="#" class="img-nav-btn prev round" onclick="toPrevPhoto()">&#8249;</a>
      <a href="#" class="img-nav-btn next round" onclick="toNextPhoto()">&#8250;</a>
    </div>
    <div id="img-info-container">
      <h4 id="photo-count"></h4>
      <h4 id="caption"></h4>
      <h4 id="approval-status"></h4>
    </div>

    <div class="btn-container">
      <button class="big-btn approve-green" type="button" name="accept" onclick="acceptPhoto()"><h2>Accept</h2></button>
    </div>
    <div class="btn-container">
      <button class="big-btn reject-red" type="button" name="reject" onclick="rejectPhoto()"><h2>Reject</h2></button>
    </div>

    <div class="clear"></div>

    <script>

      //init firebase storage
      var storageRef = firebase.storage().ref();

      var photoIdx = 0, totalPhotos = 0, ratedPhotos = 0;
      var includingAlreadyRated = false;

      function getPhotos(includeAlreadyRated){
        list = [];

        totalPhotos = 0;
        ratedPhotos = 0;

        <?php
          $values = json_decode($firebase->get(DEFAULT_PATH.'/images/'));
          foreach ($values as $key => $photo) {
        ?>

          totalPhotos++;

          console.log("photo: " + ("<?php if( property_exists($photo, 'rated') ) { echo 'true'; } else { echo 'false'; } ?>" === "true"));

          if("<?php if( property_exists($photo, 'rated') ) { echo 'true'; } else { echo 'false'; } ?>" === "true"){
            ratedPhotos++;
          }

          if(includeAlreadyRated || ("<?php if( property_exists($photo, 'rated') ) { echo 'true'; } else { echo 'false'; } ?>" !== "true")){
            list.push({
              key: "<?php echo $key ?>",
              uid: "<?php echo $photo->uid ?>",
              approved: "<?php
                if (!property_exists($photo, 'rated')) echo 'PENDING';
                else echo ($photo->approved)?'TRUE':'FALSE'; ?>",
              caption: "<?php if (property_exists($photo, 'caption')) { echo $photo->caption; } else { echo '`No Caption`'; } ?>",
              path: "<?php if (property_exists($photo, 'path')) { echo $photo->path; } else { echo '#'; } ?>"
            });
          }

        <?php
          }
        ?>

        updatePhotoCounts();

        return list;
      }

      function setPhoto(idx){
        if(photos.length != 0) {
            $("#photo").attr("src", "#");
            if (photos[idx].path != undefined && photos[idx].path != "#") {
                storageRef.child(photos[idx].path).getDownloadURL().then(function(url){
                    $("#photo").attr("src", url);
                    $("#caption").html("<u>Caption:</u> " + photos[idx].caption);
                    $("#approval-status").html("<u>Approval Status:</u> " + photos[idx].approved);
                });
            } else {
                $("#caption").html("No file was found.");
                $("#approval-status").html("<u>Approval Status:</u> " + photos[idx].approved);
            }
        }

        updatePhotoCounts();
      }

      $(document).ready(function(){
        inclidingAlreadyRated = false;
        photos = getPhotos(includingAlreadyRated);
        setPhoto(photoIdx);
      });

      function toNextPhoto(){
        if(photoIdx < (photos.length-1)){
          photoIdx++;
        }
        else{
          photoIdx = 0;
        }

        setPhoto(photoIdx);
      }

      function toPrevPhoto(){
        if(photoIdx > 0){
          photoIdx--;
        }
        else{
          photoIdx = photos.length-1;
        }

        setPhoto(photoIdx);
      }

      function acceptPhoto() {
          setApprovalStatus(photos[photoIdx].key, true);
          toNextPhoto();

          updatePhotoCounts();
      }

      function rejectPhoto(){
        setApprovalStatus(photos[photoIdx].key, false);
        toNextPhoto();

        updatePhotoCounts();
      }

      function boxClicked(){
        includingAlreadyRated = document.getElementById("photo-checkbox").checked;
        photos = getPhotos(includingAlreadyRated);
        photoIdx = 0;
        setPhoto(photoIdx);
      }

      function setApprovalStatus(key, isApproved) {
          firebase.database().ref("release/images/" + key).update({
              approved: isApproved,
              rated: true
          });
          photos[photoIdx].approved = isApproved;

          if(!photos[photoIdx].hasOwnProperty("rated")){
            photos[photoIdx].rated = true;
            ratedPhotos++;
          }

          $("#approval-status").html("<u>Approval Status:</u> " + photos[photoIdx].approved);

          updatePhotoCounts();
      }

      function updatePhotoCounts(){
        document.getElementById("num-approved").innerHTML = ratedPhotos + " of " + totalPhotos + " photos have been rated";

        if(totalPhotos != ratedPhotos || includingAlreadyRated){
          document.getElementById("photo-count").innerHTML =
            "Photo: " + (photoIdx+1) + "/" +
            ((document.getElementById("photo-checkbox").checked) ? totalPhotos : (totalPhotos-ratedPhotos));
        }
        else{
          document.getElementById("num-approved").innerHTML = "All " + totalPhotos + " photos have been rated at this time";
          document.getElementById("photo-count").innerHTML = "";
          document.getElementById("caption").innerHTML = "";
          document.getElementById("approval-status").innerHTML = "";

          $("#photo").attr("src", "images/check-mark.png");
        }
      }

    </script>

  </div>
</div>
