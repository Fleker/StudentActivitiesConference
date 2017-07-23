<?php
// Test - http://localhost/sac17.rowanieee.org/index.php?p=users_who_have_password_issues&sendemail=CbQPmHQ55ERg0ZlTSb4k4XBqEg92
    include 'firebase_include.php';
    $data = json_decode($firebase->get(DEFAULT_PATH . "/attendees/"));

    if(isset($_GET['sendemail'])) {
        $subject = "IMPORTANT: Student Activities Conference Registration Issue - Action Needed";        
        $sendto = explode(',', $_GET['sendemail']);
        require 'mail_config.php';
        foreach($sendto as $key) {
            $attendee = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$key"));
            $email = $attendee->email;
            $name = $attendee->name;
            $message = "Dear $name,<br>
            <p>
    Unfortunately, the Rowan University IEEE Student Branch discovered an issue preventing a number of attendees from successfully completing registration. If you are receiving this email, it's because you were identified as an attendee with an issue linked to your SAC account.</p>
            <p>Nonetheless, we have provided a work-around that will allow you to successfully complete registration. Follow the steps below and you'll be on your way to completing registration:</p>
            <strong>Steps to Complete Registration</strong>
            <ol>
                <li>Click on your unique registration link: <a href='http://sac17.rowanieee.org/?p=registration_attendee&attendee=$key'>http://sac17.rowanieee.org/?p=registration_attendee&attendee=$key</a></li>
                <li>A warning message will appear informing you that there is an issue with your account. This is something we added to inform effected users!</li>
                <li>Follow the steps in that warning message
                <ol>
                    <li>Click on the reset password link</li>
                    <li>Reset your password to something that you can remember</li>
                    <li>Go to: <a href='http://sac17.rowanieee.org'>sac17.rowanieee.org</a></li>
                    <li>In the top right corner, click 'Log in' (if there is an account already logged in, click log out)</li>
                    <li>Login with your email and new password</li>
                    <li>You should see: 'Welcome $name'</li>
                    <li>In the top right corner, click the small pencil icon.</li>
                    <li>You will be redirected to the registration page.</li>    
                    <li>Fill out all information, including the password field.</li>
                    <li>Click save. Confirm you have a green message that appears informing you that all information was successfully saved.</li>
                </ol>
                </li>
            </ol>
            
            <p>If you are still having issues, or need assistance, please don't hesitate to contact Jacob Culleny at <a href='mailto:jacob.culleny@ieee.org'>jacob.culleny@ieee.org</a>. We sincerely apologize for the inconvenience. </p>
            
            Best,<br>
            Jacob Culleny
            ";
            echo "Sent an email to $email<br>";
//            sendEmail($email, $name, "Critical information related to registration", $message);
            sendEmail($email, $name, $subject, $message);
        }
    }
?>
<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Who is having login issues?</h1>
			</div>
		</div>
        
        <div class="clear"></div>
        <?php 
//                var_dump($data); 
        ?>
        <div id='the_results'>
        </div>
        
        <script>
            var affected = [];
            <?php 
                $i = 0;
                foreach ($data as $key => $value) {
                    echo "console.log('$key');
                    ";
                    $value = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$key"));
                    /*echo "console.log('".print_r($value)."'); 
                    ";*/
                    echo "console.log('".$value->password."');
                    ";
//                    echo "console.log('---'); ";
                    if (!property_exists($value, 'password') || strlen($value->password) == 0) {
                        $i++;
            ?>
                    console.log("Checking <?php echo $value->email; ?>");
                    firebase.auth().signInWithEmailAndPassword("<?php echo $value->email; ?>", "<?php echo $value->email; ?>0").then(function(user) {
                         console.log("<?php echo $value->email; ?> is good");
                        document.getElementById('the_results').innerHTML += "<span style='color:green'><?php echo $value->email; ?> is good</span><br>";
                    }, function(error) {
                        console.error(error);
                        document.getElementById('the_results').innerHTML += "<span style='color:red'><?php echo $value->email; ?> is having problems</span>";
                        document.getElementById('the_results').innerHTML += "&emsp;&emsp;<button onclick='resetAccount(\"<?php echo $value->email; ?>\")'>Send Reset Password Email</button><br>";
                        affected.push("<?php echo $key; ?>");
                    });
            <?php
                    }
                }
            ?>
                         
            function resetAccount(email) {
                if (email == undefined || email.length < 3) {
                    alert('You need to enter your email above!');
                    return;
                }
                firebase.auth().sendPasswordResetEmail(email).then(function() {
                  // Email sent.
                    alert("Password sent");
                }, function(error) {
                  // An error happened.
                    alert(error);
                });
            } 
            
            setInterval(function() {
                document.getElementById('affected_users').innerHTML = affected.length;
                document.getElementById('affected_users_link').href = '?p=users_who_have_password_issues&sendemail=' + affected.join(',');
            }, 500);
        </script>
        <strong><?php echo $i; ?> people haven't finished registered.</strong>
        <strong><span id='affected_users'></span> are affected</strong>
        <a href='#' id='affected_users_link'>Send email to all affected</a>
        
	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->