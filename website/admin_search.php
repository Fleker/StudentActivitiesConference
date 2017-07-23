<?php

include 'firebase_include_js.php';
function getAdminClass($firebase, $uuid) {
    $value = $firebase->get(DEFAULT_PATH . "/admins/$uuid/");
    $response = isset($value) && $value != null && $value != "null";
    if ($response) {
        echo "admin ";
    }
}

function getCounselorClass($user) {
    if (property_exists($user, 'counselor') && $user->counselor == 'true') { echo "counselor "; } else { echo ""; }
}

const DEBUG_SEND_ONLY_ONE_EMAIL = false;

if(isset($_GET['sendemail']) && isset($_GET['emailtype'])) {
    $sendto = explode(',', $_GET['sendemail']);
    require 'mail_config.php';

    foreach($sendto as $key) {
        $attendee = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$key"));
        $email = $attendee->email;
        $name = (property_exists($attendee, 'name')) ? $attendee->name : "SAC Attendee";
        echo "Sending email to uid $key ($email)<br>";
        if ($_GET['emailtype'] == 1 || $_GET['emailtype'] == "1") {
                $subject = "IMPORTANT: Complete Registration for 2017 IEEE Student Activities Conference!";
                $message = "Dear $name,<br>
            <p>
    If you are receiving this email, it is because you have not completed registration for the 2017 IEEE Region 2 Student Activities Conference. <strong>Completing registration is required!</strong></p>

            <p><strong>Complete Conference Registration Here: </strong><a href='http://sac17.rowanieee.org/?p=registration_attendee&attendee=$key'>sac17.rowanieee.org/?p=registration_attendee&attendee=$key</a></p>

            <p><strong>IMPORTANT INFORMATION:</strong><br>
            The Rowan IEEE development team has discovered a couple of system issues preventing some users from completing registration. Once you complete all fields on the registration page and click save, you should receive a large green message that confirms you have successfully completed registration. After clicking save, if you do not receive this message, then it's possible there is an issue with your account.</p>

            <p>We are working tirelessly to correct these issues. If you are experiencing issues with registration, please email Jacob Culleny at <a href='mailto:jacob.culleny@ieee.org'>jacob.culleny@ieee.org</a>.</p>

            Best,<br>
            The Rowan IEEE Student Branch
            ";
        } else if ($_GET['emailtype'] == 2 || $_GET['emailtype'] == "2") {
            $secondaryComp = "";
            if (property_exists($attendee, 'tshirt_competition')) {
                if ($attendee->project_showcase == 'true' || $attendee->project_showcase) {
                    $secondaryComp = $secondaryComp . "Project Showcase";
                }
                if ($attendee->tshirt_competition == 'true' || $attendee->tshirt_competition) {
                    if (!empty($secondaryComp)) {
                        $secondaryComp = $secondaryComp . ", ";
                    }
                    $secondaryComp = $secondaryComp . "T-Shirt Competition";
                }
                if ($attendee->paper_competition == 'true' || $attendee->paper_competition) {
                    if (!empty($secondaryComp)) {
                        $secondaryComp = $secondaryComp . ", ";
                    }
                    $secondaryComp = $secondaryComp . "Pico Conference";
                }
                if (empty($secondaryComp)) {
                    $secondaryComp = "None";
                }
            } else {
                $secondaryComp = "None";
            }
            $special = "None";
            if (property_exists($attendee, 'special') && trim($attendee->special) != "") {
                $special = trim($attendee->special);
            }
            echo $secondaryComp;
                $subject = "IMPORTANT: IEEE Region 2 Student Activities Conference - Verify Account Details";
                $message = "Dear $name,<br>
            <p>
            Thank you for registering for the 2017 IEEE Region 2 Student Activities Conference hosted at Rowan University on April 7th-9th. Our team is excited to officially meet you soon!</p>

            <p>
            This email contains the information you selected when you completed conference registration. Please take a look at the information below and confirm there are mistakes with your registration.</p>

            <p><strong>Your Registration Information</strong></p>

            <ol>
                <li><strong>Name: </strong>$name</li>
                <li><strong>Email: </strong>$email</li>
                <li><strong>School: </strong>".$attendee->school."</li>";
            if (property_exists($attendee, 'counselor') && ($attendee->counselor || $attendee->counselor == 'true')) {
                $message = $message . "";
            } else {
                $message = $message . "<li><strong>Year: </strong>".$attendee->year."</li>
                <li><strong>Primary Competition: </strong>".$attendee->competition."</li>
                <li><strong>Secondary Competition(s): </strong>".$secondaryComp."</li>
                ";
            }
                $message = $message . "
                <li><strong>Phone Number: </strong>".$attendee->phone."</li>
                <li><strong>IEEE Number: </strong>".$attendee->ieee_number."</li>
                <li><strong>T-Shirt Size (unisex): </strong>".$attendee->tshirt."</li>
                <li><strong>Banquet Dinner Choice: </strong>".$attendee->banquet_entree."</li>
                <li><strong>Special Requests: </strong>".$special."</li>
            </ol>

            <p>If the above information is correct, then there is nothing you have to do! However, if there is a mistake, please complete the following steps:</p>

            <ol>
                <li>Visit <a href='http://sac17.rowanieee.org'>sac17.rowanieee.org</a></li>
                <li>Login with your email and password you used when you originally registered. If you forget this information, you can always reset your password through our website.</li>
                <li>After successfully logging in, click the 'pencil' icon in the top right corner to access your registration settings</li>
                <li>Correct the appropriate registration information, and click 'save'.</li>
                <li>You should see a green confirmation message after clicking save that verifies your changes have been successfully saved.</li>
            </ol>

            <p>If you have any questions/problems, please reach out to Jacob Culleny at <a href='mailto:jacob.culleny@ieee.org'>jacob.culleny@ieee.org</a>.

            <p>Kind Regards,<br>
            The 2017 SAC Planning Committee</p>
            ";
        } else if ($_GET['emailtype'] == 3 || $_GET['emailtype'] == "3") {
            $subject = "IMPORTANT: IEEE Region 2 Student Activities Conference - Upload Your Paper";
            $message = "Dear $name,<br>
            <p>Thank you for registering for the 2017 Pico Conference at the IEEE Region 2 Student Activities Conference. We are excited to read your paper and watch your presentation. As a reminder, your final papers should be submitted by <strong>Wednesday, March 15th</strong>, so our judges will have time to review all of them. You can upload your publications to <a href='http://sac17.rowanieee.org/index.php?p=pico_conf_dashboard'>the Pico Conference Dashboard</a>.</p>
            <p>Please email <a href='mailto:felkern0@students.rowan.edu'>Nick Felker</a> if you have any comments or questions.</p>
            <p>Thanks,<br>
            The 2017 SAC Planning Committee</p>";
        } else if ($_GET['emailtype'] == 4 || $_GET['emailtype'] == "4") {
            $subject = "IMPORTANT: IEEE Region 2 Student Activities Conference - Upload Your T-Shirt";
            $message = "Dear $name,<br>
            <p>Thank you for registering for the 2017 T-Shirt Competition at the IEEE Region 2 Student Activities Conference. We are excited to look at your artwork. As a reminder, your final papers should be submitted by <strong>Friday, March 31st</strong>. You can upload your shirts to <a href='http://sac17.rowanieee.org/index.php?p=tshirt_dashboard'>the T-Shirt Dashboard</a>.</p>
            <p>Please email <a href='mailto:felkern0@students.rowan.edu'>Nick Felker</a> if you have any comments or questions.</p>
            <p>Thanks,<br>
            The 2017 SAC Planning Committee</p>";
        } else if ($_GET['emailtype'] == 5 || $_GET['emailtype'] == "5") {
            $subject = "IMPORTANT: IEEE Region 2 Student Activities Conference - Upload Your Project";
            $message = "Dear $name,<br>
            <p>Thank you for registering for the 2017 Project Showcase at the IEEE Region 2 Student Activities Conference. We are excited to look at your artwork. As a reminder, your projects should be submitted online prior to the conference to incorporate it into the voting. You should do this by <strong>Wednesday, April 5th</strong>. You can upload your projects to <a href='http://sac17.rowanieee.org/index.php?p=project_showcase_dashboard'>the Project Showcase Dashboard</a>.</p>
            <p>Please email <a href='mailto:felkern0@students.rowan.edu'>Nick Felker</a> if you have any comments or questions.</p>
            <p>Thanks,<br>
            The 2017 SAC Planning Committee</p>";
        }
        if (DEBUG_SEND_ONLY_ONE_EMAIL) {
            echo "Sent dummy emails: $subject";
            echo "<br>&emsp;" . sendEmail("felkern0@students.rowan.edu", "Nick Felker", $subject, $message);
            echo "<br>&emsp;" . sendEmail("handnf@gmail.com", "Nick Felker", $subject, $message);
            echo "<br>&emsp; ". sendEmail("cullenyj5@students.rowan.edu", "Jacob Culleny", $subject, $message);
            break;
        } else {
            echo "Sent an email to $email<br>";
            sendEmail($email, $name, $subject, $message);
//            sleep(1);
            set_time_limit(10);
        }
    }
}
?>

<script src="admin_restriction.js"></script>
<script>
    enableAuthenticationRequirement("admin_search", function() {});
</script>

<div class="content-wrapper clearfix restricted">
    <div class="container">
        <div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>SAC Attendees</h1>
				<h2>Click "UUID" to expand that field.<br>
                    Click on Attendee name to toggle Admin status</h2>
                <h3>Please wait until the entire page has loaded.</h3>
			</div>
		</div>

		<div class="clear"></div>

        <script>
			// Pull attendee data from firebase
            attendees = [];
            <?php
                $values = json_decode($firebase->get(DEFAULT_PATH . "/attendees/"));
                foreach ($values as $key => $user) { ?>
        attendees.push({
						// finishedRegistering, hotel_opt_out, paper_competition, project_showcase, tshirt_competition are manually set to strings because they;re stored as a boolean type
                        key: "<?php echo $key; ?>",
                        name: "<?php if (property_exists($user, 'name')) { echo htmlspecialchars($user->name); } else { echo 'null'; } ?>",
                        sex: "<?php if (property_exists($user, 'sex')) { echo $user->sex; } else { echo "null"; } ?>",
                        email: "<?php if (property_exists($user, 'email')) { echo htmlspecialchars($user->email); } else { echo 'null'; } ?>",
                        school: "<?php if (property_exists($user, 'school')) { echo $user->school; } else { echo "null"; } ?>",
                        phone: "<?php if (property_exists($user, 'phone')) { echo $user->phone; } else { echo ""; } ?>",
                        paid: "<?php if (property_exists($user, 'paid')) { echo $user->paid; } else { echo "null"; } ?>",
                        toc: "<?php if (property_exists($user, 'toc')) { echo $user->toc; } else { echo "null"; } ?>",
						year: "<?php if (property_exists($user, 'year')) { echo $user->year; } else { echo "null"; } ?>",
						ieee_number: "<?php if (property_exists($user, 'ieee_number')) { echo $user->ieee_number; } else { echo "null"; } ?>",
						tshirt: "<?php if (property_exists($user, 'tshirt')) { echo $user->tshirt; } else { echo "null"; } ?>",
						hotel_opt_out: "<?php if (property_exists($user, 'hotel_opt_out')) { echo ($user->hotel_opt_out?"TRUE":"FALSE"); } else { echo "null"; } ?>",
						competition: "<?php if (property_exists($user, 'competition')) { echo $user->competition; } else { echo "null"; } ?>",
						project_showcase: "<?php if (property_exists($user, 'project_showcase')) { echo ($user->project_showcase?"TRUE":"FALSE"); } else { echo "null"; } ?>",
						paper_competition: "<?php if (property_exists($user, 'paper_competition')) { echo ($user->paper_competition?"TRUE":"FALSE"); } else { echo "null"; } ?>",
						tshirt_competition: "<?php if (property_exists($user, 'tshirt_competition')) { echo ($user->tshirt_competition?"TRUE":"FALSE"); } else { echo "null"; } ?>",
						banquet_opt_out: "<?php if (property_exists($user, 'banquet_opt_out')) { echo ($user->banquet_opt_out?"TRUE":"FALSE"); } else { echo "null"; } ?>",
						banquet_entree: "<?php if (property_exists($user, 'banquet_entree')) { echo $user->banquet_entree; } else { echo "null"; } ?>",
						counselor: "<?php if (property_exists($user, 'counselor')) { echo $user->counselor; } else { echo "null"; } ?>",
            vip: "<?php if (property_exists($user, 'vip')) { echo ($user->vip?'TRUE':'FALSE'); } else { echo 'null'; } ?>",
                        ticket_price: "<?php if (property_exists($user, 'ticket_price')) { echo $user->ticket_price; } else { echo "null"; } ?>",
						guest_name: "<?php if (property_exists($user, 'guest_name')) { echo $user->guest_name; } else { echo "null"; } ?>",
            registered_date: "<?php
            if (property_exists($user, 'registered')) {
              $timestamp = $user->registered;
              echo date("m/d/Y, H:i:s", $timestamp);
            }
            else {
              echo "null";
            } ?>",
            updated_date: "<?php
            if (property_exists($user, 'updated')) {
              $timestamp = $user->updated;
              echo date("m/d/Y, H:i:s", $timestamp);
            }
            else {
              echo "null";
            } ?>",
						// Something weird is happening in this field.
						special: "<?php
						if (property_exists($user, 'special')) {
							// some sanitization
							$string = $user->special;
							$string = preg_replace('/\s+/', ' ', trim($string)); // remove newline's
							$string = trim($string); // remove leading & trailing whitespace
							echo $string;
						} else {
							echo "null";
						} ?>",
						finishedRegistering: "<?php echo (isset($user->password)?"true":"false"); ?>"
                    });
            <?php
                }
            ?>
        </script>

        <input type="search" placeholder="Search" id='search' oninput='filterRows()' /><br>
        Only users who haven't paid:
        <input type="checkbox" id='nopaid' onclick="filterRows()" /><br>
        Only users who haven't completed registration:
        <input type="checkbox" id='noregister' onclick="filterRows()" /><br>
        Only users who haven't completed waiver:
        <input type="checkbox" id='nowaiver' onclick="filterRows()" /><br>
        Only counselors:
        <input type="checkbox" id='counselor' onclick="filterRows()" /><br>

        <button id='export' value="Export Attendee List" onclick="exportToCsv()">Export Attendee List</button><br><br>

        <table>
            <thead>
                <tr>
                    <td onclick="$('.uuid').toggle();" style="text-decoration:underline;">UUID</td>
                    <td>Name</td>
                    <td>Contact Info</td>
                    <td>Reg Status</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($values as $key => $user) { ?>
                    <tr class='searchRow'>
                        <td><div class='uuid' style='display:none'><?php echo $key; ?></div></td>
                        <td
                            onclick="adminify('<?php echo $key; ?>')"
                            class='<?php getAdminClass($firebase, $key); getCounselorClass($user); ?> name'
                            ><?php if (property_exists($user, 'name')) { echo htmlspecialchars($user->name); } else { echo "null"; } ?>
                                <br>
                            <?php if (property_exists($user, 'school')) { echo $user->school; } else { echo "null"; } ?><br>

                            <small style='text-transform:uppercase;'><?php if (property_exists($user, 'competition')) { echo trim($user->competition); } else { echo "null"; } ?>
                            <?php if (property_exists($user, 'project_showcase') && $user->project_showcase == 'true') { echo ", Project"; } ?>
                            <?php if (property_exists($user, 'tshirt_competition') && $user->tshirt_competition == 'true') { echo ", T-Shirt"; } ?>
                            <?php if (property_exists($user, 'paper_competition') && $user->paper_competition == 'true') { echo ", Paper"; } ?></small>
                            </td>
                        <td><a href='mailto:<?php echo $user->email; ?>'><?php if (property_exists($user, 'email')) { echo htmlspecialchars($user->email); } else { echo "null"; } ?></a><br>
                            <a href='tel:<?php if (property_exists($user, 'phone')) { echo $user->phone; } else { echo ""; } ?>'><?php if (property_exists($user, 'phone')) { echo $user->phone; } else { echo ""; } ?></a>
                        </td>
                        <td><?php echo "Paid: "; if (property_exists($user, 'paid')) { echo $user->paid; } else { echo "null"; } ?><br>
                        <?php echo "Registered: "; if (isset($user->password)) { echo "true"; } else { echo "false"; } ?><br>
                        <?php echo "Signed Waiver: "; if (isset($user->toc)) { echo $user->toc; } else { echo "false"; } ?><br>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button onclick='sendCompleteRegistrationEmail()' style='display:block' id='email_registration'>Send "Complete Registration" E-Mail</button><br>
        <button onclick='sendCheckYourDetailsEmail()' style='display:block' id='email_verify'>Send "Here's Your Registration Details" E-Mail</button><br>
        <button onclick='sendPicoConfEmail()' style='display:block'>Send "Pico Conference Reminder" E-Mail</button><br>
        <button onclick='sendTshirtEmail()' style='display:block'>Send "T-Shirt Reminder" E-Mail</button><br>
        <button onclick='sendProjectEmail()' style='display:block'>Send "Project Showcase Reminder" E-Mail</button><br>
        
        <div id='messages'></div>

        <script>
            function filterRows() {
                var searchKey = $('#search').val().toLowerCase();
                $('.searchRow').hide();
                for (var i = 0; i < attendees.length; i++) {
                    var a = attendees[i];
                    var fn = a.name.split(' ')[0];
                    var ln = a.name.split(' ')[1];
                    if (ln == undefined) {
                        console.warn("User " + fn + " (" + a.key + ") has no last name!");
                    }
                    // Check if search starts with a given value
                    if (a.key.toLowerCase().indexOf(searchKey) == 0 ||
                            fn.toLowerCase().indexOf(searchKey) == 0 ||
                            (ln != undefined && ln.toLowerCase().indexOf(searchKey) == 0) ||
                            a.school.toLowerCase().indexOf(searchKey) == 0 ||
                            a.email.toLowerCase().indexOf(searchKey) == 0) {
                        $($('.searchRow')[i]).show();
                    }
                    if ($('#nopaid').is(':checked') && a.paid == "true") {
                        $($('.searchRow')[i]).hide();
                    }
                    if ($('#noregister').is(':checked') && a.finishedRegistering == "true") {
                        $($('.searchRow')[i]).hide();
                    }
                    if ($('#nowaiver').is(':checked') && a.toc == "1") {
                        $($('.searchRow')[i]).hide();
                    }
                    if ($('#counselor').is(':checked') && a.counselor != "true") {
                        $($('.searchRow')[i]).hide();
                    }
                }
            }

            function adminify(uuid) {
                toggle = confirm("Toggle admin privilege for user?");
                if (toggle) {
                    $.post("admin_query.php", {uuid: uuid, toggle: true}, function(res) {
                        console.log(res);
                         for (var i = 0; i < attendees.length; i++) {
                            if (attendees[i].key == uuid) {
                                $($('.name')[i]).toggleClass('admin');
                            }
                         }
                    });
                }
            }

			function exportToCsv() {
				<?php date_default_timezone_set("America/New_York"); ?> // For time-stamping files
				var filename = "<?php echo "attendees-".date('Y-m-d')."_".date("h:ia").".csv"; ?>"; // Form a timestamped filename
				var processRow = function (row) {
					var finalVal = '';
					for (var j = 0; j < row.length; j++) {
						var innerValue = row[j] === null ? '' : row[j].toString();
						if (row[j] instanceof Date) {
							innerValue = row[j].toLocaleString();
						};
						var result = innerValue.replace(/"/g, '""');
						if (result.search(/("|,|\n)/g) >= 0)
							result = '"' + result + '"';
						if (j > 0)
							finalVal += ',';
						finalVal += result;
					}
					return finalVal + '\n';
				};

				// Form a header
				var csvFile = 'Date Registered, Date Updated, Registration Complete, Paid, Full Name, Sex, Email, UUID, Phone, School, IEEE Number, Year, T-Shirt Size, Competition, Banquet Opt-Out, Banquet Entree, Special Request, Paper Competition, Project Showcase, T-Shirt Competition, Hotel Opt-Out, Accepted ToC, Counselor, VIP, Ticket Price, Guest Name\n';

				// Fill in the CSV file one attendee at a time
				for (var i = 0; i < attendees.length; i++) {
                    var a = attendees[i];
					// In the future we may want to add a little bit of text-processing to make the CSV output more readable
                    // Check based on different filters
                    var userRow = [a.registered_date, a.updated_date, a.finishedRegistering, a.paid, a.name, a.sex, a.email, a.key, a.phone, a.school, a.ieee_number, a.year, a.tshirt, a.competition, a.banquet_opt_out, a.banquet_entree, a.special, a.paper_competition, a.project_showcase, a.tshirt_competition, a.hotel_opt_out, a.toc, a.counselor, a.vip, a.ticket_price, a.guest_name];
                    var shouldInclude = true;
                    if ($('#nopaid').is(':checked')) {
                        shouldInclude = shouldInclude && a.paid != "true";
                    }
                    if ($('#nowaiver').is(':checked')) {
                        shouldInclude = shouldInclude && a.toc != "1";
                    }
                    if ($('#noregister').is(':checked')) {
                        shouldInclude = shouldInclude && a.finishedRegistering != "true";
                    }
                    if (shouldInclude) {
                        csvFile += processRow(userRow);
                    } else {
                        console.info("Skipping " + a.name);
                    }
                }

				var blob = new Blob([csvFile], { type: 'text/csv;charset=utf-8;' });
				if (navigator.msSaveBlob) { // IE 10+
					navigator.msSaveBlob(blob, filename);
				} else {
					var link = document.createElement("a");
					if (link.download !== undefined) { // feature detection
						// Browsers that support HTML5 download attribute
						var url = URL.createObjectURL(blob);
						link.setAttribute("href", url);
						link.setAttribute("download", filename);
						link.style.visibility = 'hidden';
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
					}
				}
			}

            var DEBUG = false;
            function sendCompleteRegistrationEmail() {
                $('#email_registration').hide(100);
                var validAttendees = [];
                for (var i = 0; i < attendees.length; i++) {
                    var a = attendees[i];
                    if (a.finishedRegistering != "true" && a.paid == "true") {
                        validAttendees.push(a);
                    }
                }
                if (DEBUG) {
                    validAttendees = [validAttendees[0], validAttendees[1], validAttendees[2]];
                }
                completed = 0;
                for (var i = 0; i < validAttendees.length; i++) {
                    var a = validAttendees[i];
                    if (DEBUG) {
                        a.email = "felkern0@students.rowan.edu";
                    }
                    var msg = `Dear ` + a.name + `,<br>
                        <p>If you are receiving this email, it is because you have not completed registration for the 2017 IEEE Region 2 Student Activities Conference. <strong>Completing registration is required!</strong></p>
                        
                        <p><strong>Complete Conference Registration Here: </strong><a href='http://sac17.rowanieee.org/?p=registration_attendee&attendee=` + a.key + `'>sac17.rowanieee.org/?p=registration_attendee&attendee=` + a.key + `</a></p>
                        
                        <p><strong>IMPORTANT INFORMATION:</strong><br>
                        The Rowan IEEE development team has discovered a couple of system issues preventing some users from completing registration. Once you complete all fields on the registration page and click save, you should receive a large green message that confirms you have successfully completed registration. After clicking save, if you do not receive this message, then it's possible there is an issue with your account.</p>

                        <p>We are working tirelessly to correct these issues. If you are experiencing issues with registration, please email Jacob Culleny at <a href='mailto:jacob.culleny@ieee.org'>jacob.culleny@ieee.org</a>.</p>   
                        Best,<br>
                        The Rowan IEEE Student Branch`;
                    
                    
                    setTimeout(function(a, msg) {
                        $.post('email_api_post.php', {name: a.name, email: a.email, subject: "IMPORTANT: Complete Registration for 2017 IEEE Student Activities Conference!", msg: msg}, function(data) {
                            completed++;
                            $('#messages').html('Sent ' + completed + ' out of ' + validAttendees.length + '  (' + Math.round(completed*100/validAttendees.length) + '%)');
                        });
                    }, 5000 * i, a, msg);
                }
            }
            
            var completed = 0;

            function sendCheckYourDetailsEmail() {
                $('#email_verify').hide(100);
                var validAttendees = [];
                for (var i = 0; i < attendees.length; i++) {
                    var a = attendees[i];
                    validAttendees.push(a);
                }
                
                if (DEBUG) {
//                    validAttendees = [validAttendees[0], validAttendees[1], validAttendees[2]];
                }
                completed = 0;
                for (var i = 0; i < validAttendees.length; i++) {
                    var a = validAttendees[i];
                    console.log('Attendee ', validAttendees[i], a);
                    if (DEBUG) {
                        a.email = "felkern0@students.rowan.edu";
                    }
                    var secondaryComp = "";
                    if (a.tshirt_competition !== undefined) {
                        if (a.project_showcase == 'true' || a.project_showcase == true) {
                            secondaryComp = secondaryComp + "Project Showcase";
                        }
                        if (a.tshirt_competition == 'true' || a.tshirt_competition == true) {
                            if (secondaryComp.length > 0) {
                                secondaryComp = secondaryComp + ", ";
                            }
                            secondaryComp = secondaryComp + "T-Shirt Competition";
                        }
                        if (a.paper_competition == 'true' || a.paper_competition == true) {
                            if (secondaryComp.length > 0) {
                                secondaryComp = secondaryComp + ", ";
                            }
                            secondaryComp = secondaryComp + "Pico Conference";
                        }
                        if (secondaryComp.length == 0) {
                            secondaryComp = "None";
                        }
                    } else {
                        secondaryComp = "None";
                    }
                    var special = "None";
                    if (a.special !== undefined && a.special.trim().length > 0) {
                        special = a.special.trim();
                    }
                    var msg = `<p>Dear ` + a.name + `,</p>
                        <p>Thank you for registering for the 2017 IEEE Region 2 Student Activities Conference hosted at Rowan University on April 7th-9th. Our team is excited to officially meet you!</p>
                        <p>This email contains the information you selected when you completed conference registration. Please take a look at the information below and confirm there are no mistakes with your registration.</p>

                        <strong>Your Registration Information</strong>
                        <ol>
                            <li><strong>Name: </strong>`+ a.name + `</li>
                            <li><strong>Email: </strong>` + a.email + `</li>
                            <li><strong>School: </strong>` + a.school + `</li>`;
                    if (a.counselor == true || a.counselor == 'true') {
                        msg += `<li><strong>Labeled as a counselor</strong></li>`;  
                    } else {
                        msg += `<li><strong>Year: </strong>` + a.year + `</li>
                            <li><strong>Primary Competition: </strong>` + a.competition + `</li>
                            <li><strong>Secondary Competition(s): </strong>` + secondaryComp + `</li>`;
                    }
                    
                    msg += `<li><strong>Phone Number: </strong>` + a.phone + `</li>
                        <li><strong>IEEE Number: </strong>` + a.ieee_number + `</li>
                        <li><strong>T-Shirt Size (unisex): </strong>` + a.tshirt + `</li>
                        <li><strong>Banquet Dinner Choice: </strong>` + a.banquet_entree + `</li>
                        <li><strong>Special Requests: </strong>` + special + `</li>
                    </ol>
                    <p>If the above information is correct, then there is nothing you have to do! However, if there is a mistake, please complete the following steps:</p>
                    <ol>
                        <li>Visit <a href='http://sac17.rowanieee.org'>sac17.rowanieee.org</a></li>
                        <li>Login with your email and password you used when you originally registered. If you forget this information, you can always reset your password through our website.</li>
                        <li>After successfully logging in, click the 'pencil' icon in the top right corner to access your registration settings.</li>
                        <li>Correct the appropriate registration information, and click 'save'.</li>
                        <li>You should see a green confirmation message after clicking save that verifies your changes have been successfully saved.</li>
                    </ol>
                    <p><strong style='border-bottom: solid 1px black;'>You must complete all registration updates by Tuesday, March 28th at 11:59 PM. After this date, you will no longer be able to update your SAC17 account.</strong></p>
                    <p>If you have any questions/problems, please reach out to Jacob Culleny at <a href='mailto:jacob.culleny@ieee.org'>jacob.culleny@ieee.org</a>.</p>
                        Kind Regards,<br>
                        The 2017 SAC Planning Committee`;
                    setTimeout(function(a, msg) {
                        console.log(a);
                        $.post('email_api_post.php', {name: a.name, email: a.email, subject: "IMPORTANT: Verify your account details for IEEE SAC!", msg: msg}, function(data) {
                            completed++;
                            $('#messages').html('Sent ' + completed + ' out of ' + validAttendees.length + '  (' + Math.round(completed*100/validAttendees.length) + '%)');
                        });
                    }, 5000 * i, a, msg);
                }
            }

            function sendPicoConfEmail() {
                var validAttendees = [];
                for (var i = 0; i < attendees.length; i++) {
                    var a = attendees[i];
                    if (a.paper_competition !== undefined && (a.paper_competition === 'TRUE' || a.paper_competition === 'true' || a.paper_competition === true)) {
                        validAttendees.push(a.key);
                    }
                }
//                console.log(validAttendees);
                window.location.href = "?p=admin_search&sendemail=" + validAttendees.join(",") + "&emailtype=3";
            }

            function sendTshirtEmail() {
                var validAttendees = [];
                for (var i = 0; i < attendees.length; i++) {
                    var a = attendees[i];
                    if (a.tshirt_competition !== undefined && (a.tshirt_competition === 'TRUE' || a.tshirt_competition === 'true' || a.tshirt_competition === true)) {
                        validAttendees.push(a.key);
                    }
                }
//                console.log(validAttendees);
                window.location.href = "?p=admin_search&sendemail=" + validAttendees.join(",") + "&emailtype=4";
            }

            function sendProjectEmail() {
                var validAttendees = [];
                for (var i = 0; i < attendees.length; i++) {
                    var a = attendees[i];
                    if (a.project_showcase !== undefined && (a.project_showcase === 'TRUE' || a.project_showcase === 'true' || a.project_showcase === true)) {
                        validAttendees.push(a.key);
                    }
                }
//                console.log(validAttendees);
                window.location.href = "?p=admin_search&sendemail=" + validAttendees.join(",") + "&emailtype=5";
            }
            
            console.log("Everything is loaded.");
        </script>

        <style>
            .admin {
                font-weight: bold;
                color: red;
            }
            .counselor {
                font-weight: bold;
                font-style: italic;
                color: darkgreen;
            }
        </style>
    </div>
</div>
