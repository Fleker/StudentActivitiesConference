<?php 
include 'firebase_include_js.php';
include 'firebase_include.php';
?>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>T-Shirt Manager</h1>
				<h2>Check the status of your t-shirt</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <div id='loading'>Loading data...</div>
        
        <div id='conf_status' style='display:none'>
            <div id='conf_status_label'></div>
            <a id='conf_download'>View your submission</a><br>
            <button id='resubmit' onclick='resubmitFile()'>Resubmit</button>
        </div>
        <div id='conf_upload' style='display:none'>
            <p>Before submitting, please check your t-shirt to make sure it adheres to <a href='docs/rules/2017T-ShirtRules.pdf' target="_blank">competition rules</a>. Failure to adhere to these rules can result in disqualification.</p>
            
            <p>Please select a valid image file.</p>
            <input type='file' id='paper_upload'><br><br>
            <button onclick="uploadFile()">Submit</button><br><br>
            <div id='upload_status'></div>
        </div>
        
        <script>
            firebase.auth().onAuthStateChanged(function(user) {
                if (user != null) {
                    // Get user id
                    var id = user.uid
                    // Query this user to see if they're registered for T-Shirt
                    firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/" + id).once('value').then(function(snapshot) {
                        var userdata = snapshot.val();
                        console.log(userdata);
                        if (userdata == null) {
                            $('#loading').html("<div class='message error'><div class='icon'><p>You cannot submit a t-shirt if you are not registered for the competition.</p></div></div>");
                        } else if (userdata.tshirt_competition || userdata.tshirt_competition == "true") {
                            // User is registered
                            // Query this user to see if they've already submitted
                            firebase.database().ref("<?php echo DEFAULT_PATH; ?>/tshirts/" + id).once('value').then(function(snapshot) {
                                 if (snapshot.val() != null) {
                                     // User has already submitted
                                     var confStatus = snapshot.val();
                                     $('#conf_status').show(100);
                                     // What to show?
                                     $('#conf_status_label').html("Your t-shirt has been submitted.");
                                     $('#conf_download').attr('href', confStatus.downloadUrl);
                                 } else {
                                     // Allow user to submit
                                     $('#conf_upload').show(100);
                                 }
                                 $('#loading').hide(100);
                            });
                        } else {
                            $('#loading').html("<div class='message error'><div class='icon'><p>You cannot submit a t-shirt if you are not registered for the competition.</p></div></div>");
                        }
                    });
                } else {
                    $('#loading').html("<div class='message error'><div class='icon'><p>You cannot submit a t-shirt if you are not logged in.</p></div></div>");   
                }
            });
            
            function uploadFile() {
                // Get the file
                var file = $('#paper_upload')[0].files[0];
                // Validate type
                if (file.name.substr(-3) != "jpg" && file.name.substr(-3) != "png" && file.name.substr(-3) != "bmp") {
                    $('#loading').html("<div class='message error'><div class='icon'><p>This t-shirt must be a valid image (jpg, png, or bmp).</p></div></div>");
                    $('#loading').show(100);
                    return;
                }
                $('#loading').hide(100);
                
                var storage = firebase.storage();
                var storageRef = firebase.storage().ref();
                var metadata = {
                    customMetadata: {
                        "authorName": firebase.auth().currentUser.name,  
                        "authorEmail": firebase.auth().currentUser.email,  
                        "authorUid": firebase.auth().currentUser.uid  
                    }
                };
                // Save paper to the `tshirts/` folder with a unique name
                var firebaseName = 'tshirts/' + file.name + "_" + Math.random() + file.name.substr(-3);
                var uploadTask = storageRef.child(firebaseName).put(file, metadata);
                uploadTask.on('state_changed', function(snapshot){
                  // Observe state change events such as progress, pause, and resume
                  // Get task progress, including the number of bytes uploaded and the total number of bytes to be uploaded
                    var progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                    console.log('Upload is ' + progress + '% done');
                    $('#upload_status').html(progress.toFixed() + "% done");
                }, function(error) {
                    // Handle unsuccessful uploads
                }, function() {
                    var downloadURL = uploadTask.snapshot.downloadURL;
                    // Post to Firebase
                    updates = {};
                    updates["<?php echo DEFAULT_PATH; ?>/tshirts/" + firebase.auth().currentUser.uid] = {
                        paperFile: firebaseName,
                        downloadUrl: downloadURL,
                        status: "Submitted",
                        lastUpdate: (new Date().getTime()/1000).toFixed()
                    }
                    firebase.database().ref().update(updates);
                    
                    // Allow the user to check the status
                    window.location.reload();
                });
            }
            
            function debugFileUpload() {
                // Look, if you submit a paper with this, it won't put you in the conference.
                $('#loading').hide(100);
                $('#conf_upload').show(100);
            }
            
            function resubmitFile() {
                $('#conf_upload').show(100);
            }
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->