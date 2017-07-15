<div>
<?php 
include 'firebase_include_js.php';
?>


<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Project Showcase Admin Console</h1>
				<h2>Check the status of the competition</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <div id='loading'>Loading data...</div>
        
        <h2>Projects Submitted</h2>
        <ul id='projects'></ul>
        
        <h2>Participants entered in Project Showcase (<span id='count'>#</span>)</h2>
        <ul id='participants'></ul>
        
      
        </div>
        

        
        <script>
            firebase.database().ref("<?php echo DEFAULT_PATH; ?>/projects/").once('value').then(function(snapshot) {
                // Load all projects
                var output = "";
                var s = snapshot.val();
                for (i in s) {
                    var project = s[i];
                    submissions[i] = project;
                    submissions[i]['teammates_display'] = "";
                    var date = new Date();
                    if (project.lastUpdate) { 
                        date.setTime(project.lastUpdate * 1000);
                        var dateString = (date.getMonth() + 1) + "/" + date.getDate();
                    } else {
                        var dateString = "Never";
                    }
                    output += "<li><strong>" + project.title + "</strong><ul>";
                    if (project.downloadUrl) {
                        output += "<li><a href='" + project.downloadUrl + "' target='_blank' style='color:darkorange'>View Project</a></li>";
                    }
                    output += "<li><strong>" + project.status + "</strong></li><li><strong>Last update: </strong>" + dateString + "</li><li id='project_" + i + "'><strong>Submitted by: </strong><span id='user_" + i + "'>" + i + "</span>";
                
                    if (project.teammates != undefined) {
                        var teammates = project.teammates.split(',');
                        console.log(teammates);
                        for (var j = 0; j < teammates.length; j++) {
                            if (teammates[j].length > 0) {
                                output += " and <span id='user_" + teammates[j] + "'>" + teammates[j] + "</span>";
                            }
                        }
                    }
                    
                    output += "</li><li><strong>Abstract: </strong>" + project.abstract + "</li></li></ul></li>";
                }
                document.getElementById('projects').innerHTML = output;
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
                        if (attendee.project_showcase == true || attendee.project_showcase == 'TRUE' || attendee.project_showcase == 'true') {
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
                            $('#user_' + i).html("Submitted by " + attendee.name + " from " + attendee.school);
                        }
                     $('#user_' + i).html(attendee.name + " from " + attendee.school);
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
                    submission['teammates_display'] = $('#project_' + attendee.key).html().substring(13);
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
                
                
            }
            
            var entrants = [];
            var entrants_ext = [];
            var submissions = {};
            var attendees_loaded = false;
            
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
