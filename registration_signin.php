<?php
    if (isset($_GET['attendee'])) {
        $attendee = $_GET['attendee'];
        $value = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$attendee"));
    }
?>
<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Please sign in to continue</h1>
				<h2></h2>
			</div>
		</div>
        
        <div class="clear"></div>
        <form class="contactForm" onsubmit='return false;'>
            <input type="email" placeholder="Email" id='email' />
            <input type='password' placeholder='Password' id='password' />
            <button id='submit' onclick="login()" type='submit' id='submit'>Sign-In</button>
            <?php if (!isset($_GET['attendee']) || $value->paid == true || $value->paid == 'true') { ?>
            <a onclick='resetUI()' href='#' id='forgot_password'>Forgot password? Send reset email.</a>
            <button id='reset' onclick="resetAccount()" style='display:none; margin-top: 16px;'>Reset</button>
            <?php } ?>
        </form>
        <div id='errors' style='color:red'></div>
        
        <script>
            var from = "<?php echo $_GET['from']; ?>";
            var attendee = "<?php if (isset($_GET['attendee'])) { echo $_GET['attendee']; } ?>";
            var attendeeEmail = "<?php 
                if (isset($value)) {
                    echo $value->email;
                }
            ?>";
            
            function login() {
                $('#errors').html('');
                if (attendee.length > 0 && attendeeEmail.toLowerCase() != $('#email').val().toLowerCase()) {
                    $('#errors').html('Invalid email entered');
                    return false;   
                }
                firebase.auth().signInWithEmailAndPassword($('#email').val().toLowerCase(), $('#password').val()).then(function(user) {
                    console.log("User logged in");
                    redirect();
                }, function(error) {
                    // Handle Errors here.
                    var errorCode = error.code;
                    var errorMessage = error.message;
                    console.error("Error code '" + errorCode + "'");
                    console.error(error);
                    if (errorCode === undefined) {
                        $('#errors').html('');
                        console.log("Redirecting the user to the previous page");
                        redirect();
                    } else {
                        $('#errors').html(errorMessage);   
                    }
                });
            }
        
            function redirect() {
                window.location.href = '?p=' + from + '&attendee=' + attendee;
            }
            
            function resetUI() {
                $('#password').hide();
                $('#submit').hide();
                $('#forgot_password').hide();
                $('#reset').show();
            }
             
            function resetAccount() {
                var email = $('#email').val();
                if (email == undefined || email.length < 3) {
                    alert('You need to enter your email above!');
                    return;
                }
                firebase.auth().sendPasswordResetEmail($('#email').val()).then(function() {
                  // Email sent.
                    window.location.href = "?p=home&msg=Check%20your%20email%20for%20reset%20instructions";
                }, function(error) {
                  // An error happened.
                    alert(error);
                });
            }   
        </script>
        
        <style>
            .contactForm input {
                min-width:300px;
                margin-bottom: 1em;
            }
            
            #forgot_password, #reset {
                margin-left:32px;
            }
            
            #password, #email, #reset {
                display: block;
            }
        </style>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
