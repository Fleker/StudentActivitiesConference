<?php 
include 'firebase_include_js.php';
?>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Pico Conference Manager</h1>
				<h2>Check the status of your paper</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <div id='loading'>Loading data...</div>
        
        <div id='conf_status' style='display:none'>
            <div id='conf_status_label'></div>
            <div id='conf_team_info'></div>
            <a id='conf_download'>View your submission</a><br>
            <button id='resubmit' onclick='resubmitFile()'>Resubmit</button>
            <button id='resubmit' onclick='updateTeammates()'>Update Teammates</button>
        </div>
        <div id='conf_upload' style='display:none'>
            <p>Before submitting, please check your paper to make sure it adheres to <a href='docs/rules/sac-2017-paper.pdf' target="_blank">competition rules</a>. Failure to adhere to these rules can result in disqualification.</p>
            
            <p>Please select a PDF file.</p>
            <input type='file' id='paper_upload'><br><br>
            <strong>If you have any teammates, list them here as comma-separated emails.</strong>
            <input type='text' placeholder='Comma-separated teammate emails' id='teammates' />
            <button onclick="pullTeammates('teammates')">Submit</button><br><br>
        </div>
        <div id='conf_teammates' style='display:none'>
            <p>If you have any teammates, list them here with comma-separated emails.</p>
            <input type='text' placeholder='Comma-separated teammate emails' id='teammates_only' />
            <button onclick="updateOnlyTeammates('teammates_only')">Submit</button><br><br>
        </div>
        
        <div id='upload_status'></div>
        
        <script>
            firebase.auth().onAuthStateChanged(function(user) {
                if (user != null) {
                    // Get user id
                    var id = user.uid
                    // Query this user to see if they're registered for PicoConf
                    firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/" + id).once('value').then(function(snapshot) {
                        var userdata = snapshot.val();
                        console.log(userdata);
                        if (userdata == null) {
                            $('#loading').html("<div class='message error'><div class='icon'><p>You cannot submit a paper if you are not registered for the competition.</p></div></div>");
                        } else if (userdata.paper_competition || userdata.paper_competition == "true") {
                            // User is registered
                            // Query this user to see if they've already submitted
                            firebase.database().ref("<?php echo DEFAULT_PATH; ?>/papers/" + id).once('value').then(function(snapshot) {
                                 if (snapshot.val() != null) {
                                     // User has already submitted
                                     var confStatus = snapshot.val();
                                     if (confStatus.teammates_emails != undefined) {
                                         $('#teammates').val(confStatus.teammates_emails);
                                         $('#teammates_only').val(confStatus.teammates_emails);
                                         $('#conf_team_info').html('You are with ' + (confStatus.teammates_emails.split(',').length) + ' teammate(s): ' + confStatus.teammates_emails);
                                     }
                                     $('#conf_status').show(100);
                                     // What to show?
                                     $('#conf_status_label').html("Your paper has been submitted.");
                                     $('#conf_download').attr('href', confStatus.downloadUrl);
                                 } else {
                                     // Allow user to submit
                                     $('#conf_upload').show(100);
                                 }
                                 $('#loading').hide(100);
                            });
                        } else {
                            $('#loading').html("<div class='message error'><div class='icon'><p>You cannot submit a paper if you are not registered for the competition.</p></div></div>");
                        }
                    });
                } else {
                    $('#loading').html("<div class='message error'><div class='icon'><p>You cannot submit a paper if you are not logged in.</p></div></div>");   
                }
            });
            
            function pullTeammates(id) {
                if ($('#' + id).val().length > 0) {
                    $('#upload_status').show().html('Finding your teammates...');
                    var teammate_ids = '';
                    var email_string = $('#' + id).val();
                    firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/").once('value').then(function(snapshot) {
                        // Load all attendees because I need an email lookup
                        var s = snapshot.val();
                        for (i in s) {
                            var attendee = s[i];
                            if (email_string.indexOf(attendee.email) > -1) {
                                teammate_ids += i + ',';
                            }
                        }
                        uploadFile(teammate_ids, email_string);
                    });
                } else {
                    uploadFile('', '');
                }
            }
            
            function updateOnlyTeammates(id) {
                if ($('#' + id).val().length > 0) {
                    $('#upload_status').show().html('Finding your teammates...');
                    var teammate_ids = '';
                    var email_string = $('#' + id).val();
                    firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/").once('value').then(function(snapshot) {
                        // Load all attendees because I need an email lookup
                        var s = snapshot.val();
                        for (i in s) {
                            var attendee = s[i];
                            if (email_string.indexOf(attendee.email) > -1) {
                                teammate_ids += i + ',';
                            }
                        }
                        updates = {};
                        updates["<?php echo DEFAULT_PATH; ?>/papers/" + firebase.auth().currentUser.uid + "/teammates"] = teammates_ids;
                        updates["<?php echo DEFAULT_PATH; ?>/papers/" + firebase.auth().currentUser.uid + "/teammates_emails"] = email_string.trim();
                        updates["<?php echo DEFAULT_PATH; ?>/papers/" + firebase.auth().currentUser.uid + "/lastUpdate"] = (new Date().getTime()/1000).toFixed();
                        firebase.database().ref().update(updates);
                        // Send email to all teammates
                        var mates = email_string.split(',');
                        for (i in mates) {
                            var email = mates[i].trim();
                            $.post('email_api_post.php', {name: '', email: email, subject: "PicoConference Notice", msg: getEmailMsg()}, function() {
                                // Allow the user to check the status
                                window.location.reload();
                            });
                        }
                    });
                }
            }
            
            function uploadFile(teammate_ids, teammate_emails) {
                // Get the file
                var file = $('#paper_upload')[0].files[0];
                // Validate type
                if (file.name.substr(-3) != "pdf") {
                    $('#loading').html("<div class='message error'><div class='icon'><p>This paper must be a PDF.</p></div></div>");
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
                // Save paper to the `papers/` folder with a unique name
                var firebaseName = 'papers/' + file.name + "_" + Math.random() + file.name.substr(-3);
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
                    console.log(teammate_ids);
                    updates["<?php echo DEFAULT_PATH; ?>/papers/" + firebase.auth().currentUser.uid] = {
                        paperFile: firebaseName,
                        downloadUrl: downloadURL,
                        status: "Submitted",
                        teammates: teammate_ids,
                        teammates_emails: teammate_emails.trim(),
                        lastUpdate: (new Date().getTime()/1000).toFixed()
                    }
                    firebase.database().ref().update(updates);
                    
                    // Send email to all attendees
                    $.post('email_api_post.php', {name: '', email: firebase.auth().currentUser.email, subject: "PicoConference Notice", msg: msg}, function() {
                        // Send email to all teammates
                        var mates = teammate_emails.split(',');
                        for (i in mates) {
                            var email = mates[i].trim();
                            $.post('email_api_post.php', {name: '', email: email, subject: "PicoConference Notice", msg: getEmailMsg()}, function() {
                                // Allow the user to check the status
                                window.location.reload();
                            });
                        }
                    });
                });
            }
            
            function getEmailMsg() {
                return `<p>Hello.</p>
                        <p>Your paper for Pico Conference has been submitted.</p>
                        <p>Thanks,<br>
                        Nick Felker<br>
                        PicoConference Chair<br>
                        felkern0@students.rowan.edu`;
            }
            
            function debugFileUpload() {
                // Look, if you submit a paper with this, it won't put you in the conference.
                $('#loading').hide(100);
                $('#conf_upload').show(100);
            }
            
            function resubmitFile() {
                $('#conf_upload').show(100);
            }
            
            function updateTeammates() {
                $('#conf_teammates').show(100);
            }
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->