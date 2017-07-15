 <?php
if ((time() < TIME_LATE_BIRD) || (isset($_GET['late']))) {
    include 'registration_helper.php';
?>
<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Registration Form</h1>
				<h2>Form data is saved locally, so it is saved between reloads.</h2>
			</div>
		</div>

		<div class="clear"></div>

        <div id='formdiv'>

        </div>
        <div id='form_submission'></div>
        <form id='formform' method="post" action="registration_submit.php" onsubmit="return validate();" class='contactForm'>

        </form>
        <div id='form_stripe'></div>
        <?php placeErrorDialog('form'); ?>
        <button id="add" onclick="add()">Add New Attendee</button>
        <button id="add_counselor" onclick="add(true)">Add Counselor</button>
        <div id='form_submission'>
            <button onclick="submit()" style='background:#4CAF50' id='payments'>Go to Payment</button><br>
            <strong>Total Price: </strong><span id='payment'></span>
        </div>

        <div id='form_global'>
            <div class='entry_form'>
                <input type="text" placeholder="Why?" id='why' name='why' onchange="saveForm()" style='display:none' />
                <div style='font-size:24pt;text-align:center;line-height:1;'><?php echo PAYMENT_TITLE; ?> Ticket Registration ($<?php echo TICKET_PRICE/100 ?> / ticket)</div>
                <div style='font-size:18pt;text-align:center;margin-top:8px;'><?php echo PAYMENT_START_DISP; ?> &mdash; <?php echo PAYMENT_END_DISP; ?></div>
                <h2>Please Enter School Name</h2>
                <div class="form-group">
                    <?php include 'datalist_select_university.php'; ?>
                    <div class="col-md-4">
                        <input id="buyer_school" name='buyer_school' type="text" placeholder="" class="form-control input-md" oninput='saveForm(); showPrice();' required list="universities">
                        <?php placeErrorDialog('buyer_school'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <a href='?p=faq#cost' target="_blank">What does the registration fee include?</a>
                </div>
            </div>

            <div class='ticket_separator'>
                <hr>
                <h3>Ticket Information</h3>
            </div>
        </div>

        <div id='form_sample'>
            <div class="container entry_form user_form">
              <h2><a class='attendee_display' data-toggle="collapse">Collapsible Panel</a></h2>
                <div class='attendee_price_hint'></div>
              <div class="panel-group">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title" style="display:none">
                      <a >Collapsible panel</a>
                    </h4>
                  </div>
                  <div id="collapse1" class="panel-collapse collapse">
                      <div class="panel-body">
                          <button class='remove_button' onclick="removeAttendee(this); return false;">Remove attendee</button>
                          <div class="form-group">
                              <label class="col-md-4 control-label" for="textinput">First &amp; Last Name</label>
                              <div class="col-md-4">
                                  <input class="attendee_name" type="text" placeholder="" class="form-control input-md" onkeyup='saveForm()' required>
                                  <div class='message error attendee_name_error' ><div class='icon'><p></p></div></div>
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-md-4 control-label" for="textinput">E-Mail Address</label>
                              <div class="col-md-4">
                                  <input class="attendee_email" type="email" placeholder="" class="form-control input-md" onkeyup='saveForm()' required>
                                  <div class='message error attendee_email_error' ><div class='icon'><p></p></div></div>
                              </div>
                          </div>

                          <input class="attendee_counselor" type='hidden' name='attendee_counselor' />
                          <input class="attendee_firebase_key" type='hidden' name='attendee_firebase_key' />
                          <input class="attendee_ticket_price" type='hidden' name='attendee_ticket_price' />
                      </div>
                  </div>
                </div>
              </div>
            </div>
            <hr>
        </div>

        <script>
            registration = localStorage['registration'] || "{}";
            registration = JSON.parse(registration);
            registration.users = registration.users || [];
            data2 = {error: 0, count: 0};
            function loadForm() {
                document.getElementById('formform').innerHTML = document.getElementById('form_global').innerHTML;
                if (registration.users !== undefined) {
                    for (i = 0; i < registration.users.length; i++) {
                        var u = registration.users.length;
                        // Get the form HTML
                        var html = document.getElementById('form_sample').innerHTML;
                        document.getElementById('formform').innerHTML += html;
                    }
                    // Show things
                    $('.ticket_separator')[0].style.display = 'block';
                } else {
                    // No users, don't bother showing anything
                    $('.ticket_separator')[0].style.display = 'none';
                }
                setTimeout(function() {
                    // Load global args
                    document.getElementById('why').value = registration.why || "";
//                    document.getElementById('buyer_name').value = registration.buyer_name || "";
//                    document.getElementById('buyer_email').value = registration.buyer_email || "";
                    document.getElementById('buyer_school').value = registration.buyer_school || "";
//                    document.getElementById('buyer_phone').value = registration.buyer_phone || "";

                    // Load local args
                    refreshGlobals();
                    document.getElementById('add_counselor').style.display = 'inline-block';
                    for (var i = 0; i < registration.users.length; i++) {
                        console.log("Getting info for attendee " + i);
                        $('.attendee_name')[i].value = registration.users[i].attendee_name || "";
                        $('.attendee_email')[i].value = registration.users[i].attendee_email || "";
                        $('.attendee_counselor')[i].value = registration.users[i].attendee_counselor || false;
                        if (isTrue(registration.users[i].attendee_counselor)) {
                            document.getElementById('add_counselor').style.display = 'none';
                        }
                    }
                    formUpdate();
                    setTimeout("showPrice();", 30);
                    // Hide all errors
                    $('.message').hide();
                }, 30);

                ///////////////////////////////Add "Other" option for Safari////////////////////////////////

                if (!('options' in document.createElement('datalist'))) { //datalist is not supported
                  var $buyer_school = $("#buyer_school");
                  $buyer_school.hide();

                  var otherOption = document.createElement("option");
                  otherOption.value = "Other";
                  otherOption.text = "Other";
                  document.getElementById('universities-select').appendChild(otherOption);

                  var schoolIsOnList = false;
                  for(var i = 0; i < document.getElementById('universities-select').length; i++){
                    if(document.getElementById('universities-select').options[i].value == registration.buyer_school){
                      $("#universities-select").val(registration.buyer_school);
                      schoolIsOnList = true;
                    }
                  }
                  if(!schoolIsOnList && (registration.buyer_school != null)){
                    $("#universities-select").val("Other");
                    $buyer_school.show();
                    $buyer_school.val(registration.buyer_school);
                  }

                  $('#universities-select').on('change', function(){
                    if($(this).val().toLowerCase() == "other"){
                      $buyer_school.show();
                      $buyer_school.val("");
                      $buyer_school.trigger("input");
                    }
                    else{
                      $buyer_school.hide();
                      $buyer_school.val($(this).val());
                      $buyer_school.trigger("input");
                    }
                  });
                }

            }

            function saveForm() {
                var r = {};
                // Save global data
                r['why'] = document.getElementById('why').value;
//                r['buyer_name'] = document.getElementById('buyer_name').value;
//                r['buyer_email'] = document.getElementById('buyer_email').value;
                r['buyer_school'] = document.getElementById('buyer_school').value;
//                r['buyer_phone'] = document.getElementById('buyer_phone').value;

                r['users'] = [];
                for (var i = 0; i < registration.users.length; i++) {
                    // Save individual data
                    console.log("Saving attendee data " + i);
                    r.users[i] = {};
                    r.users[i]['attendee_name'] = $('.attendee_name')[i].value;
                    r.users[i]['attendee_email'] = $('.attendee_email')[i].value;
                    if ($('.attendee_counselor')[i].value.length > 0) {
                        r.users[i]['attendee_counselor'] = $('.attendee_counselor')[i].value;
                    } else {
                        r.users[i]['attendee_counselor'] = registration.users[i].attendee_counselor;
                    }
                }
                console.log(r.users);
                localStorage['registration'] = JSON.stringify(r);
                registration = r;
                formUpdate();
                // Generate stripe button
            }

            function price(index, additionallyRegisteredStudents) {
                if ($('#buyer_school').val().toLowerCase().indexOf('rowan') > -1 || $('#buyer_school').val().toLowerCase().indexOf('stockton') > -1) {
                    return 30;
                }
                if (data2 != undefined) {
                    additionallyRegisteredStudents = data2.count;
                }
                var hasACounselor = -1;
                for (var i = 0; i < registration.users.length; i++) {
                    if (isTrue(registration.users[i].attendee_counselor)) {
                        hasACounselor = i;
                    }
                }
                if (hasACounselor == index) {
                    return 0;
                }
                // TODO Make overflow size dynamic
                if (index > (hasACounselor > -1 && hasACounselor < 10 ? 10 - additionallyRegisteredStudents : 9 - additionallyRegisteredStudents)) {
                    return overflow_price / 100;
                }
                return ticket_price / 100;
            }

            function formUpdate() {
                var hasACounselor = false;
                for (var i = 0; i < registration.users.length; i++) {
                    var displayIndex = i + 1;
                    if (hasACounselor) {
                        displayIndex = i;
                    }
                    // Setup attendee form properties
                    var attendee_header = "";
                    console.log($('.attendee_name')[i].value)
                    if ($('.attendee_name')[i].value.length == 0) {
                        if (isTrue(registration.users[i].attendee_counselor)) {
                            attendee_header = "Counselor Ticket (Free)";
                            hasACounselor = true;
                            $('.attendee_ticket_price')[i].value = 0;
                        } else {
                            attendee_header = "Ticket " + (displayIndex) + " ($" + price(i) + ")";
                            $('.attendee_ticket_price')[i].value = price(i);
                        }
                    } else {
                        if (isTrue(registration.users[i].attendee_counselor)) {
                            hasACounselor = true;
                            attendee_header = $('.attendee_name')[i].value + " - Counselor Ticket (Free)";
                            $('.attendee_ticket_price')[i].value = 0;
                        } else {
                            attendee_header = $('.attendee_name')[i].value + " - Ticket " + (displayIndex) + " ($" + price(i) + ")";
                            $('.attendee_ticket_price')[i].value = price(i);
                        }
                    }


                    $('.attendee_display')[i].innerHTML = attendee_header;

                    $('.collapse')[i].id = "attendee_form" + i;

                    $('.attendee_name')[i].name = "attendee_name" + i;
                    $('.attendee_name')[i].id = "attendee_name" + i;
                    $('.attendee_name_error')[i].id = "attendee_name"+i+"_error";
                    $('.attendee_email')[i].name = "attendee_email" + i;
                    $('.attendee_email')[i].id = "attendee_email" + i;
                    $('.attendee_email_error')[i].id = "attendee_email" + i + "_error";
                    $('.attendee_counselor')[i].name = "attendee_counselor" + i;
                    $('.attendee_firebase_key')[i].name = "attendee_firebase_key" + i;
                    $('.attendee_ticket_price')[i].name = "attendee_ticket_price" + i;
                    if (isTrue(registration.users[i].attendee_counselor)) {
                        $('.remove_button')[i].innerHTML = "Remove counselor"; // Custom remove message
                    } else {
                        $('.remove_button')[i].innerHTML = "Remove attendee"; // Default remove message
                    }
                }
            }

            function refreshGlobals() {
            }

            function add(counselor) {
                // Adds yet another student
                var index = registration.users.length;
                registration.users[index] = {};
                if (counselor) {
                    registration.users[index]['attendee_counselor'] = true;
                }
                saveForm();
                loadForm();
            }

            function removeAttendee(button) {
                // Iterate through buttons to get our index
                var removes = $('.remove_button');
                for (var i = 0; i < removes.length; i++) {
                    if (removes[i] == button) {
                        // Delete this student
                        console.log("splice " + i);
                        console.log(registration.users);
                        console.log(registration.users.splice(i, 1));
                        $('.user_form')[i].remove();
//                        delete registration.users[i];
                        console.log(registration.users);
                    }
                }
                saveForm();
                loadForm();
            }

            function submit() {
                if (validate()) {
                    var index = registration.users.length;
                    completeFirebaseRegistration(0, index);
                }
            }

            function completeFirebaseRegistration(index, length) {
                if (index == length) {
                    $('.entry_form').hide(300);
                    $('#add').hide(300);
                    $('#form_submission').hide(300);
                    $('hr').hide(300);
                    $('form').submit(); // Submit the form and do what comes next
                    return;
                }
                firebase.auth().createUserWithEmailAndPassword($('.attendee_email')[index].value, $('.attendee_email')[index].value+"0").then(function(success) {
                    var user = firebase.auth().currentUser;
					var name = $('.attendee_name')[index].value;
					user.updateProfile({displayName: name});
                    $('.attendee_firebase_key')[index].value = user.uid;
                    console.log("Registering user #" + index + ": " + user.uid + ", " + user.email);
                    // Logout the user
                    firebase.auth().signOut();
                    completeFirebaseRegistration(index + 1, length);
                }, function(error) {
                    // Handle Errors here, if error is valid.
                    $.get('registration_user_paid.php', {email: $('.attendee_email')[index].value}, function(rawdata) {
                        var data = JSON.parse(rawdata);
                        console.log(data);
                        if (data.paid == 'true' || data.paid == true) {
                            var errorCode = error.code;
                            var errorMessage = error.message;
                            console.log(errorCode, errorMessage); // Not sure how to handle potential errors
                            alert("Error creating account " + errorMessage);
                        } else {
                            // Probably fine.
                            if (data.uuid != undefined && data.uuid != 'undefined') {
                                $('.attendee_firebase_key')[index].value = data.uuid;
                                console.log(data);
                                completeFirebaseRegistration(index + 1, length);
                            } else {
                                // We NEED to generate one.
                                // Try to auth user.
                                firebase.auth().signInWithEmailAndPassword($('.attendee_email')[index].value, $('.attendee_email')[index].value + '0').then(function(success) {
                                    console.log("Found email!");
                                    $('.attendee_firebase_key')[index].value = firebase.auth().currentUser.uid;
                                    completeFirebaseRegistration(index + 1, length);
                                }, function(error) {
                                    // Everything is broken.
                                    alert("The account " + $('.attendee_email')[index].value + " is active, but no data exists. " + error.message);
                                });
                            }
                        }
                    });

                });
            }

            /**
             * This method checks a boolean expression. If true, an error dialog
             * will appear at the provided element. Otherwise, it will disappear.
             *
             * @param boolean The predicate for this error
             * @param formid The top-level element id for this error
             * @param formtext The error message to be shown
             */
            function showErrorIfTrue(boolean, formid, formtext) {
                formid += "_error"; // Append error to indicate error block
                if (boolean) {
                    console.log($('#' + formid + ' > div > p'));
                    console.log(formtext);
                    $('#' + formid + ' > div > p').html(formtext);
                    $('#' + formid).show(100);
                } else {
                    $('#' + formid).hide(100);
                }
                return boolean;
            }

            function validateUserLength() {
                return showErrorIfTrue(registration.users.length == 0, 'form', 'You need to register at least one attendee');
            }

            function showPrice() {
                // Show total price
                // Get already registered student count
                console.log("Get count for school " + $('#buyer_school').val());
                $.get('registration_school_count.php', {school: $('#buyer_school').val()}, function(data) {
                    console.log(data);
                    window.data2 = JSON.parse(data);
                    console.log(data2.count + " attendees already registered (not including counselors)");
                    var cost = 0;
                    for (var i = 0; i < registration.users.length; i++) {
                        // Get price of ticket
                        var p = price(i, data2.count);
                        if (p == overflow_price / 100) {
                            // TODO Swap this with some sort of status variable in the event that overflow and ticket price are identical
                            $('.attendee_price_hint')[i].innerHTML = "<a href='?p=faq#guests' target='_blank'>Why is this the ticket price?</a>";
                        } else {
                            // Remove hint
                            $('.attendee_price_hint')[i].innerHTML = '';
                        }
                        console.log("User " + i + " $" + p);
                        cost += p;
                    }
                    document.getElementById('payment').innerHTML = "$"+(cost);
                    if (cost == 0) {
                        $('#payments').html("Register");
                    } else {
                        $('#payments').html("Go to payment");
                    }
                    // TODO Overflow help
                    formUpdate();
                });
            }

            function validate() {
                showPrice();

                var validationIssue = false;
                // Can't register nobody!
                validationIssue = validateUserLength() || validationIssue;
                validationIssue = showErrorIfTrue($('#buyer_school').val().length == 0, 'buyer_school', 'Please enter school name') || validationIssue;

                for (var i = 0; i < registration.users.length; i++) {
                    console.log("Loop " + i);
                    validationIssue =  showErrorIfTrue($('.attendee_name')[i].value.match('\\s\\w') == null, 'attendee_name'+i, "Please enter first and last name") || validationIssue;
                    validationIssue = showErrorIfTrue($('.attendee_email')[i].value.match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) == null, 'attendee_email'+i, "Please enter a valid email") || validationIssue;
                }
                console.log("Exit");
                return !validationIssue;
            }

            function isTrue(val) {
                return val === true || val === "true";
            }

            loadForm();

            $(document).keypress(function(event){
              if(event.which == '13'){ //enter key
                event.preventDefault();
              }
            });

            var ticket_price = <?php echo TICKET_PRICE; ?>;
            var overflow_price = <?php echo OVERFLOW_TICKET_PRICE; ?>;
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
<link rel="stylesheet" type="text/css" href="css/registration.css">
<?php } else { ?>
<div class="content-wrapper clearfix">
	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Registration is closed</h1>
				<h2>Registration for the 2017 Region 2 SAC has closed. Email <a href='mailto:jacob.culleny@ieee.org'>Jacob Culleny</a> if this is an error.</h2>
			</div>
		</div>
    </div>
</div>
<?php } ?>
