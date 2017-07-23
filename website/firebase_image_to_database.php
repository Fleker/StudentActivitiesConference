<?php include ('firebase_include_js.php'); ?>
<script src="admin_restriction.js"></script>
<script>
    enableAuthenticationRequirement("firebase_image_to_database", function() {});
</script>
<!-- Using EXIF.JS from https://github.com/exif-js/exif-js -->
<script src="js/exif.js"></script>
<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Put Firebase Images into Database</h1>
                <h2>This can be done manually if need be.</h2>
			</div>
		</div>

		<div class="clear"></div>

        <img src='images/firebase_storage.png' />
        <strong>Please use the Firebase storage location.</strong>
        <br><br>

        <img src='' id='fb_img' width='100px' />
        <br>
        <input type='url' id='firebase_url' placeholder="Put in the Firebase STORAGE LOCATION" oninput='checkImagePosting()' style='width: 500px'/>
        <br><br>
        <span id='status' onclick='resetStatus()'></span>

        <script>
            function resetStatus() {
                $('#firebase_url').val('');
                $('#fb_img').attr('src', '');
                $('#status').html('');
            }

            function setStatus(msg) {
                $('#status').html(msg + '<br><small>CLICK TO DISMISS</small');
            }

            function checkImagePosting() {
                var url = $('#firebase_url').val();
                $('#status').html('Okay. Let us see what happens.');
                var imagePath = url.substring(url.indexOf('/images/'));
                if (imagePath == undefined || imagePath.length <= '/images/'.length) {
                    return;
                }
                var firebaseImage = firebase.storage().ref().child(imagePath);

                firebase.database().ref('<?php echo DEFAULT_PATH; ?>/images/').once('value').then(function(snapshot) {
                    // Check whether this image has already been put in database
                    // gs://sac17-9fc02.appspot.com/images/IMG_20170407_154440.jpg
                    var images = snapshot.val();
                    for (i in images) {
                        var img = images[i];
                        if (img.path == imagePath) {
                            setStatus('Image is already posted in the database.');
                            return;
                        }
                    }
                    pullMetadata(firebaseImage, imagePath);
                });
            }

            function pullMetadata(firebaseImage, imagePath) {
                if (!firebaseImage) {
                    console.warn("No image provided");
                    return;
                }
                firebaseImage.getDownloadURL().then(function(url) {
                    $.post('php_read_exif.php', {img: url}, function(d) {
                        console.log("Add image with caption " + d);
                        addToFirebase(firebaseImage, d, imagePath)
                    });
                    console.info("Try to obtain comment");
                    $('#fb_img').attr('src', url);
                }).catch(function(error) {
                  // Handle any errors
                });
            }

            function addToFirebase(firebaseImage, caption, imagePath) {
                // Add to Firebase.
                firebaseImage.getMetadata().then(function(metadata) {
                    // Metadata now contains the metadata for image.
                    var imageKey = firebase.database().ref('<?php echo DEFAULT_PATH;?>/images/').push().key;
                    if (!imageKey) {
                        setStatus("Key not found");
                        return;
                    }
                    var updates = {};
                    updates['<?php echo DEFAULT_PATH; ?>/images/' + imageKey] = {
                        approved: false,
                        caption: caption,
                        path: imagePath,
                        timestamp: new Date().getTime(),
                        uid: ""
                    };
                    setStatus('Photo migration complete');
                    return firebase.database().ref().update(updates);
                }).catch(function(error) {
                    // Uh-oh, an error occurred!
                    setStatus('Error occurred: ' + error.message);
                });
            }
        </script>
	</div>

</div>
