<?php 

// Because we are doing auth stuff, we can't rely on the standard admin script. This needs to be some password.

if (isset($_GET['sendemail'])) {
    $sendto = explode(',', $_GET['sendemail']);
    require 'mail_config.php';
    foreach($sendto as $key) {
        $attendee = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$key"));
        $email = $attendee->email;
        $name = $attendee->name;
        echo "Sending email to uid $key ($email)<br>";
        $subject = "IMPORTANT: Complete Submission for SAC!";
        $message = "Dear $name,<br>
            <p>
    You have just been automatically registered for SAC. Please complete your registration.</p>

            <p><strong>Complete Submission Here: </strong><a href='https://sac17.rowanieee.org/?p=registration_attendee&attendee=$key'>https://sac17.rowanieee.org/?p=registration_attendee&attendee=$key</a></p>

            <p>Please email <a href='mailto:felkern0@students.rowan.edu'>Nick Felker</a> if you have any comments or questions.</p>
            <p>Thanks,<br>
            The 2017 SAC Planning Committee</p>";
        echo "Sent an email to $email<br>";
        sendEmail($email, $name, $subject, $message);
        sleep(1);
        set_time_limit(10);
    }
}

const PAGE_PASSWORD = 'spacex_elon_musk';

if (!$_GET['password'] == PAGE_PASSWORD) {
    die("You don't have permission to access this page.");
}
?>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Register VIPs</h1>
				<h2>These are not counselors or attendees</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <span>Enter the VIP's email.</span>
        <input type='text' id='name' placeholder="Person's Name" />
        <input type='email' id='email' placeholder="Person's Email" />
        
        <button id='register'>Register</button>
        
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
                    $.get('registration_user_paid.php', {email: $('#email').val()}, function(rawdata) {
                        var data = JSON.parse(rawdata);
                        console.log(data);
                        if (data.paid == 'true' || data.paid == true || data.uuid == undefined) {
                            var errorCode = error.code;
                            var errorMessage = error.message;
                            console.log(errorCode, errorMessage); // Not sure how to handle potential errors
                            alert("Error creating account " + errorMessage);
                        } else {
                        }
                    });

                });
            } 
            
            $('#register').on('click', function() {
                // Create an auth
                registerUser(function(user) {
                    // After that, add to database
                    addToDatabase(user, function(user) {
                        // After that, send an email
                        console.info("Successful");
                        window.location.href = '?p=admin_vip&sendemail=' + user.uid + '&password=<?php echo PAGE_PASSWORD; ?>';
                    })
                });
            });
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->