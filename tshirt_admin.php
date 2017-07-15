<?php 
include 'firebase_include_js.php';

if (isset($_GET['sendemail'])) {
    $sendto = explode(',', $_GET['sendemail']);
    require 'mail_config.php';
    foreach($sendto as $key) {
        $attendee = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$key"));
        $email = $attendee->email;
        $name = $attendee->name;
        echo "Sending email to uid $key ($email)<br>";
        $subject = "IMPORTANT - IEEE SAC17 - Complete Submission for T-Shirt!";
        $message = "Dear $name,<br>
            <p>
    If you are receiving this email, it is because are registered for the t-shirt competition at the 2017 IEEE Student Activities Conference. You have not submitted a t-shirt yet. <strong>Completing your submission is required to compete in this event!</strong></p>

            <p><strong>Complete Submission Here: </strong><a href='http://sac17.rowanieee.org/index.php?p=tshirt_dashboard'>http://sac17.rowanieee.org/index.php?p=tshirt_dashboard</a></p>

            <p>Please email <a href='mailto:felkern0@students.rowan.edu'>Nick Felker</a> if you have any comments or questions.</p>
            <p>Thanks,<br>
            The 2017 SAC Planning Committee</p>";
        echo "Sent an email to $email<br>";
        sendEmail($email, $name, $subject, $message);
        sleep(1);
        set_time_limit(10);
    }
}
?>

<script src="admin_restriction.js"></script>
<script>
    enableAuthenticationRequirement("tshirt_admin", function() {});
</script>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>T-Shirt Admin Console</h1>
				<h2>Check the status of the competition</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <div id='loading'>Loading data...</div>
        
        <h2>T-Shirts</h2>
        <ul id='papers'></ul>
        
        <h2>Participants entered in T-Shirt (<span id='count'>#</span>)</h2>
        <ul id='participants'></ul>
        
        <button id='email' onclick='sendemail()'>Send e-mail to missing attendees</button>
        
        <script>
            firebase.database().ref("<?php echo DEFAULT_PATH; ?>/tshirts/").once('value').then(function(snapshot) {
                // Load all papers
                var output = "";
                var s = snapshot.val();
                for (i in s) {
                    var paper = s[i];
                    submissions.push(i);
                    var date = new Date();
                    date.setTime(paper.lastUpdate * 1000);
                    var dateString = (date.getMonth() + 1) + "/" + date.getDate();
                    output += "<li>" + paper.paperFile + "<ul><li><a href='" + paper.downloadUrl + "' target='_blank'><img width='100px' src='" + paper.downloadUrl + "'/></a></li><li>" + paper.status + "</li><li>Last update: " + dateString + "</li><li id='user_" + i + "'>Submitted by " + i + "</li></ul></li>";
                }
                document.getElementById('papers').innerHTML = output;
                $('#loading').hide();
                loadAttendees();
            });
            
            function loadAttendees() {
                firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/").once('value').then(function(snapshot) {
                    // Load all attendees
                    var output = "";
                    var s = snapshot.val();
                    for (i in s) {
                        var attendee = s[i];
                        if (attendee.tshirt_competition == true || attendee.tshirt_competition == 'TRUE' || attendee.tshirt_competition == 'true') {
                            output += "<li"
                            if (submissions.indexOf(i) > -1) {
                                entrants_ext.push(attendee);
                            } else {
                                // Quickly highlight missing speakers
                                output += " style='color:red'";
                            }
                            output += ">" + attendee.name + " (" + i + ")</li>";
                            entrants.push(i);
                            $('#user_' + i).html("Submitted by " + attendee.name);
                        }
                    }
                    $('#loading').hide();
                    document.getElementById('participants').innerHTML = output;
                    document.getElementById('count').innerHTML = entrants.length;
                    attendees_loaded = true;
                    loadSchedule();
                });
            }
            
            var entrants = [];
            var entrants_ext = [];
            var submissions = [];
            var attendees_loaded = false;
            
            function sendemail() {
                var reminders = [];
                if (!attendees_loaded) {
                    alert('You have to wait to load everything first');
                    return;
                }
                for (var i = 0; i < entrants.length; i++) {
                    if (submissions.indexOf(entrants[i]) == -1) {
                        reminders.push(entrants[i]);
                    }
                }
                console.log('Remind', reminders);
                // Send email
                window.location.href = '?p=tshirt_competition&sendemail=' + reminders.join(',');
            }
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
