<?php

const PAGE_PASSWORD = 'spacex_elon_musk';

if (!$_GET['password'] == PAGE_PASSWORD) {
    die("You don't have permission to access this page.");
}
?>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Special Registration</h1>
				<h2>Before you select your dinner choice and t-shirt option, we need a few pieces of information.</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <span>Please enter your name and email address.</span>
        <input type='text' id='name' placeholder="Your Name" />
        <input type='email' id='email' placeholder="Your Email" />
        <div id='messages'></div>
        
        <button id='register'>Next</button>
        
        <div style='opacity:0.6; margin-top:24px;'>VIPs use the same underlying model as regular attendees and counselors, so we have to create a simplified account for you before you proceed.</div>
        
        <script>
            function addToDatabase(user, callback) {
                var updates = {
                    paid: true,
                    vip: true,
                    counselor: false,
                    email: $('#email').val(),
                    name: $('#name').val()
                }
                console.log("<?php echo DEFAULT_PATH; ?>/attendees/" + user.uid);
                console.log(updates);
                firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/" + user.uid).set(updates);
                callback(user);
            }
            
            function registerUser(callback) {
                console.log("About to register " + $('#email').val());
                firebase.auth().createUserWithEmailAndPassword($('#email').val(), $('#email').val()+"0").then(function(success) {
                    var user = firebase.auth().currentUser;
                    var name = $('#name').val();
					user.updateProfile({displayName: name});
                    console.log("Registering user: " + user.uid + ", " + user.email);
                    // Logout the user
                    firebase.auth().signOut();
                    callback(user);
                }, function(error) {
                    // Handle Errors here, if error is valid.
                    $('#message').html('Something weird happened. Hang on.');
                    $.get('registration_user_paid.php', {email: $('#email').val()}, function(rawdata) {
                        var data = JSON.parse(rawdata);
                        console.log(data);
                        if (data.paid == 'true' || data.paid == true || data.uuid == undefined) {
                            var errorCode = error.code;
                            var errorMessage = error.message;
                            console.log(errorCode, errorMessage); // Not sure how to handle potential errors
                            $('#messages').html('Error creating account: ' + errorMessage);
                        } else {
                        }
                    });

                });
            } 
            
            $('#register').on('click', function() {
                // Create an auth
                $('#messages').html("Thanks! We're setting up your account now.");
                registerUser(function(user) {
                    // After that, add to database
                    addToDatabase(user, function(user) {
                        // After that, send them to the registration page
                        // But we need to give a brief timeout for server propogation.
                        console.info("Successful");
                        setTimeout(function(user) {
                            $('#messages').html('Redirecting you to finish account creation');
                            window.location.href = '?p=registration_attendee&attendee=' + user.uid;
                        }, 3000, user);
                    })
                });
            });
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->