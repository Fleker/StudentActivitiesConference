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
        $subject = "IMPORTANT - IEEE SAC17 - Complete Submission for PicoConference!";
        $message = "Dear $name,<br>
            <p>
    If you are receiving this email, it is because are registered for PicoConference at the 2017 IEEE Student Activities Conference. You have not submitted a paper yet. <strong>Completing your submission is required to compete in this event!</strong></p>

            <p><strong>Complete Submission Here: </strong><a href='http://sac17.rowanieee.org/index.php?p=pico_conf_dashboard'>http://sac17.rowanieee.org/index.php?p=pico_conf_dashboard</a></p>
            
            <p>If you are a teammate with someone who uploaded their paper, they should use the dashboard above and add you as a teammate.</p>

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
    enableAuthenticationRequirement("pico_conf_admin", function() {});
</script>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Pico Conference Admin Console</h1>
				<h2>Check the status of the competition</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <div id='loading'>Loading data...</div>
        
        <h2>Papers Submitted</h2>
        <ul id='papers'></ul>
        
        <h2>Participants entered in Pico Conference (<span id='count'>#</span>)</h2>
        <ul id='participants'></ul>
        
        <h2>Scheduling &amp; Rooms</h2>
        <div id='schedule'>
            
        </div>
        
        <button id='email' onclick='sendemail()'>Send e-mail to missing attendees</button>
        
        <script>
            firebase.database().ref("<?php echo DEFAULT_PATH; ?>/papers/").once('value').then(function(snapshot) {
                // Load all papers
                var output = "";
                var s = snapshot.val();
                for (i in s) {
                    var paper = s[i];
                    submissions[i] = paper;
                    submissions[i]['teammates_display'] = "";
                    var date = new Date();
                    date.setTime(paper.lastUpdate * 1000);
                    var dateString = (date.getMonth() + 1) + "/" + date.getDate();
                    output += "<li>" + paper.paperFile + "<ul><li><a href='" + paper.downloadUrl + "' target='_blank'>View PDF</a></li><li>" + paper.status + "</li><li>Last update: " + dateString + "</li><li id='paper_" + i + "'>Submitted by <span id='user_" + i + "'>" + i + "</span>";
                    
                    if (paper.teammates != undefined) {
                        var teammates = paper.teammates.split(',');
                        console.log(teammates);
                        for (var j = 0; j < teammates.length; j++) {
                            if (teammates[j].length > 0) {
                                output += " and <span id='user_" + teammates[j] + "'>" + teammates[j] + "</span>";
                            }
                        }
                    }
                    output += "</li></ul></li>";
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
                        if (attendee.paper_competition == true || attendee.paper_competition == 'TRUE' || attendee.paper_competition == 'true') {
                            output += "<li"
                            if (submissions[i] != undefined) {
                                attendee['key'] = i;
                                entrants_ext.push(attendee);
                            } else if($('#user_' + i).length == 0) {
                                // Quickly highlight missing speakers
                                output += " style='color:red'";
                            }
                            output += ">" + attendee.name + " (" + i + ")</li>";
                            entrants.push(i);
                        }
                        $('#user_' + i).html(attendee.name);
                    }
                    $('#loading').hide();
                    document.getElementById('participants').innerHTML = output;
                    document.getElementById('count').innerHTML = entrants.length;
                    attendees_loaded = true;
                    loadSchedule();
                });
            }
            
            function getMinutes(minutes) {
                return (minutes < 10) ? "0" + minutes : minutes;
            }
            
            function loadSchedule() {
                var output = "";
                var rooms = ["REXT 319"]; // An array of rooms.
                var TALK_FULL_LENGTH = 15; // How long each talk will be, including questions and setup, in minutes.
                var BREAK_AFTER = 4; // How many presentations until there's a break. Won't break if <= 1/2.
                
                var number_of_rooms = rooms.length; // In the future parallelize this if necessary.
                // Find who should go first.
                var highPriority = [];
                var medPriority = [];
                var lowPriority = [];
                for (var i = 0; i < entrants_ext.length; i++) {
                    var attendee = entrants_ext[i];
                    console.log(attendee);
                    var submission = submissions[attendee.key];
                    // Need to rip out the full list of attendees.
                    submission['teammates_display'] = $('#paper_' + attendee.key).html().substring(13);
                    if (attendee.competition == "wie" || attendee.competition == "brownbag" || attendee.competition == "physics" || attendee.competition == "ethics") {
                        highPriority.push(submission.teammates_display);
                    } else if (attendee.competition == "sumo_kit" || attendee.competition == "sumo_scratch" || attendee.competition == "micromouse_kit" || attendee.competition == "micromouse_scratch") {
                        medPriority.push(submission.teammates_display);
                    } else {
                        lowPriority.push(submission.teammates_display);
                    }
                }
                console.log(highPriority, medPriority, lowPriority);
                
                // Generate the list.
                output += "<table>";
                var startingDates = [];
                for (var i = 0; i < number_of_rooms; i++) {
                    startingDates[i] = new Date();
                    // Set the date to April 8, 2017, starting at 2 PM.
                    startingDates[i].setMonth(3);
                    startingDates[i].setDate(8);
                    startingDates[i].setYear(117); // JS is weird
                    startingDates[i].setHours(14);
                    startingDates[i].setMinutes(0);
                }
                
                var orderedSpeakers = highPriority.concat(medPriority).concat(lowPriority);
                for (var i = 0; i < orderedSpeakers.length; i) {
                    output += "<tr>";
                    for (var j = 0; j < number_of_rooms; j++) {
                        if (orderedSpeakers[i] == undefined) {
                            break;
                        }

                        output += "<td>" + orderedSpeakers[i] + " &mdash; " + startingDates[j].getHours() + ":" + getMinutes(startingDates[j].getMinutes()) + " " + rooms[j] + "</td>";
                        i++; // Need to move to next speaker
                        // Update to the next time step
                        startingDates[j].setMinutes(startingDates[j].getMinutes() + TALK_FULL_LENGTH);
                    }
                    output += "</tr>";
                    if (i % BREAK_AFTER == 0 && ((orderedSpeakers.length - i) > (BREAK_AFTER / 2)) && i < orderedSpeakers.length) {
                        // Break
                        output += "<tr><td>BREAK</td></tr>";
                        for (var j = 0; j < number_of_rooms; j++) {
                            startingDates[j].setMinutes(startingDates[j].getMinutes() + TALK_FULL_LENGTH);
                        }
                    }
                }
                output += "</table>";
                
                document.getElementById('schedule').innerHTML = output;
            }
            
            var entrants = [];
            var entrants_ext = [];
            var submissions = {};
            var attendees_loaded = false;
            
            function sendemail() {
                var reminders = [];
                if (!attendees_loaded) {
                    alert('You have to wait to load everything first');
                    return;
                }
                for (var i = 0; i < entrants.length; i++) {
                    if ($('#user_' + entrants[i]).length == 0) {
                        reminders.push(entrants[i]);
                    }
                }
                console.log('Remind', reminders);
                // Send email
                window.location.href = '?p=pico_conf_admin&sendemail=' + reminders.join(',');
            }
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
