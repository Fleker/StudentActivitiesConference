<?php
// See attendees, change values

include 'registration_helper.php';
const DEBUG = false;
const DEBUG_DISABLE_AUTH = "false"; // This is a string since we're injecting to JS.
if ($_SERVER['REMOTE_ADDR'] != "::1") {
    define("DEBUG_DISABLE_AUTH", "false"); // Don't allow this code to be run in production
}

function getValue($value, $key) {
    if (property_exists($value, $key)) {
        echo $value->$key;
    }
}

function getValueDefault($value, $key, $default) {
    if (property_exists($value, $key)) {
        return $value->$key;
    }
    return $default;
}

function isValue($value, $key) {
    if (property_exists($value, $key) && $value->$key != 'false') {
        return $value->$key;
    }
    return false;
}

function ifValue($value, $key, $true, $false) {
    if (property_exists($value, $key) && $value->$key != 'false') {
        return $true;
    }
    return $false;
}

function returnCheckedIfTrue($value, $key) {
    if (getValueDefault($value, $key, 'false') === 'true') {
        echo 'checked';
    }
}

function returnCheckedIf($value, $key, $expected) {
    if (getValueDefault($value, $key, '') === $expected) {
        echo 'checked';
    }
}

function returnSelectedIfTrue($value, $key, $expected) {
    if (getValueDefault($value, $key, '') == $expected) {
        echo 'selected';
    }
}

//include 'firebase_include.php';

$attendee = $_GET['attendee'];
$value = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$attendee"));
$createNewAccount = property_exists($value, 'password') == false;
$vip = (property_exists($value, 'vip') && $value->vip) ? "true" : "false";

// Test with attendee=-KXxmeDlvDb5dg8yllAt
// Counselor - -KYXDMgGTwyZVKYpYPIK

$ALLOWED_TO_UPDATE = json_decode($firebase->get(PATH_TO_FLAGS . SETTING_ALLOW_USER_UPDATES));
if ($ALLOWED_TO_UPDATE || isset($_GET['late']) || (!property_exists($value, 'password'))) {
    include 'datalist_university.php';
    include 'datalist_academic_year.php';
?>
<style>
    form {
        display: none;
    }
    h5 {
        margin-bottom: 0.5em;
    }
</style>
<script>
    // Handle user not logged in case
    createNewAccount = <?php echo $createNewAccount?'true':'false'; ?>;
    <?php if ($vip == 'true') { ?> vip = true; console.log("User is a VIP"); <?php } ?>
    console.log("Do we need to create a new account? " + createNewAccount);
    firebase.auth().onAuthStateChanged(function(user) {
        console.log("Authenticated user:", user);
        if (user && user.email.toLowerCase() == "<?php echo $value->email; ?>".toLowerCase() || <?php echo DEBUG_DISABLE_AUTH; ?>) {
            // User is signed in.
            console.log("Case 1: User is signed in", $('form'));
            setTimeout(function() {
                console.log("Show form", $('form'));
                $('form').show();
            }, 1000);
        } else if (user == null && createNewAccount) {
            // Third case - Sign in the user with temporary credentials.
            console.log("Case 2: We need to sign in the user with temporary credentials", $('form'));
            firebase.auth().signInWithEmailAndPassword("<?php echo $value->email; ?>", "<?php echo $value->email; ?>0").catch(function(error) { $('.warning').show(100); console.error(error) });
            setTimeout(function() {
                console.log("Show form", $('form'));
                $('form').show();
            }, 1000);
        } else if ((user == null && !createNewAccount) || user.email.toLowerCase() != "<?php echo $value->email; ?>".toLowerCase()) {
            // user.email != "<?php echo $value->email; ?>"
            // No user is signed in.
            console.log("Case 3: Nobody is signed in or the wrong user is signed in.");
            window.location.href = '?p=registration_signin&from=registration_attendee&attendee=<?php echo $attendee; ?>';
        } else {
            console.log("Some fourth, unexpected option happened");
        }
    });
</script>
<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
                <?php if (isset($_GET['update'])) { ?>
    			<div class="message success">
                    <div class="icon">
                        <h3 style='color:white'>Thanks for updating your profile!</h3><br>
                        <p>You are now completely registered for SAC! You can continue to make changes, and you can revisit your settings by clicking on the pencil icon in the top-right corner.</p>
                    </div>
                </div>
                <?php } ?>
                <div class='message warning' style="display:none">
                    <div class="icon">
                        <h3 style="color:white">Having trouble registering?</h3><br>
                        <!--<p>You have encountered an issue with your registration. We are currently investigating. Here are some steps to report your problem.
                        <ul style='color:white'>
                            <li>Open the Developer Tools (F12 on Chrome)</li>
                            <li>Click on the Console tab</li>
                            <li>Check 'Preserve Log'</li>
                            <li>Edit info and submit the changes</li>
                            <li>Select all of the content in the console</li>
                            <li>Copy</li>
                            <li>Open a new email to <a href='mailto:felkern0@students.rowan.edu'>the Webmaster</a> and paste the contents</li>
                        </ul>
                        </p>-->
                        <p>
                            Right now you're having issues registering due to a bug we are currently investigating. Please email <a href='mailto:felkern0@students.rowan.edu'>felkern0@students.rowan.edu</a>.
                        </p>
                        <p>
                            It may be useful to first reset your account password. You will receive an email to change your password. Then, login with your email and new password. Then, click on the pencil icon in the top-right corner.<br>
                            <button onclick='resetAccount("<?php echo $value->email; ?>")'>Reset Password</button>
                            <script>
                                function resetAccount(email) {
                                    firebase.auth().sendPasswordResetEmail(email).then(function() {
                                      // Email sent.
                                        alert("Email Reset Sent");
                                    }, function(error) {
                                      // An error happened.
                                        alert(error);
                                    });
                                }
                            </script>
                        </p>
                    </div>
                </div>
				<h1>Finish registering <?php echo explode(' ', getValueDefault($value, 'name', 'Attendee'))[0]; ?></h1>
				<h2>Form data is saved locally, so it is saved between reloads.</h2>
			</div>
		</div>

		<div class="clear"></div>

        <div id='formdiv'>

        </div>
        <div id='form_submission'></div>
        <form id='formform' method="post" action="registration_submit.php" onsubmit="return validate()" class='contactForm'>

        </form>
        <div id='form_stripe'></div>
        <div id='form_submission'>
            <button onclick="submit()" type='submit'>Save</button>
            <div id='submit_error'></div>
        </div>

        <div id='form_global'>
            <div class='entry_form'>
                <h3>Your Info</h3>
                <input type="hidden" name='attendee_key' value="<?php echo $attendee; ?>" />
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">First &amp; Last Name</label>
                    <div class="col-md-4">
                        <input id="attendee_name" name="attendee_name" type="text" class="form-control input-md" onkeyup='saveForm()' required value="<?php getValue($value, 'name'); ?>" <?php echo ifValue($value, 'name', 'readonly', ''); ?>></input>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">E-Mail Address</label>
                    <div class="col-md-4">
                        <input id="attendee_email" name="attendee_email" type="email" placeholder="" class="form-control input-md" onkeyup='saveForm()' required value="<?php getValue($value, 'email'); ?>" <?php echo ifValue($value, 'email', 'readonly', ''); ?>>
                    </div>
                </div>
            <?php if ($vip != 'true') { ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">Phone Number</label>
                    <div class="col-md-4">
                        <input id="attendee_phone" name="attendee_phone" type="tel" placeholder="" class="form-control input-md" onkeyup='saveForm()' required value="<?php getValue($value, 'phone'); ?>">
                        <?php placeErrorDialog('attendee_phone'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">Sex<br><small>This will be used to organize hotel rooms.</small></label>
                    <div class="col-md-4">
                        <input name='attendee_sex' type='radio' value="female" <?php returnCheckedIf($value, 'sex', 'female') ?> />Female
                        <input name='attendee_sex' type='radio' value="male" <?php returnCheckedIf($value, 'sex', 'male') ?> />Male
                        <?php placeErrorDialog('attendee_sex'); ?>
                    </div>
                </div>
                <br><br>

                <h5>Set a password</h5>
                You can use your SAC account for voting and personalization in the app.
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">Choose a good password.</label>
                    <div class="col-md-4">
                        <input id="attendee_password" name="attendee_password" type="password" placeholder="" class="form-control input-md" onkeyup='validatePassword1();validatePassword2();saveForm()' required>
                        <label class="col-md-4 control-label" for="textinput">Confirm password</label>
                        <input id="attendee_password2" type="password" placeholder="" class="form-control input-md" onkeyup='validatePassword1();validatePassword2();saveForm()' required>
                        <?php placeErrorDialog('attendee_password'); ?>

                        <div id="password-level">
                            <div id="password-bar"></div>
                        </div>

                        <style>
                            #password-level {
                                margin: 0.5em 0;
                                height: 10px;
                                width: 268px;
                              background: #ccc;
                            }

                            #password-level #password-bar {
                                height: 100%;
                                background: #08c;
                                width: 0%;
                                transition: width 500ms ease-in;
                            }
                        </style>
                    </div>
                </div>

                <div class="form-group" style="display:none">
                    <label class="col-md-4 control-label" for="textinput">School Name</label>
                    <div class="col-md-4">
                        <input id="attendee_school" name='attendee_school' type="text" placeholder="" class="form-control input-md" onkeyup='validateSchool();saveForm()' required list="universities" value="<?php getValue($value, 'school'); ?>">
                        <?php placeErrorDialog('attendee_school'); ?>
                    </div>
                </div>
            <?php } ?>
            <?php if (!isValue($value, 'counselor') && $vip != 'true') { ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">Academic Year</label>
                    <div class="col-md-4">
                        <input id="attendee_year" name='attendee_year' type="text" placeholder="" class="form-control input-md" onkeyup='validateYear();saveForm()' oninput='validateYear();saveForm()' required list="academic_year" value="<?php getValue($value, 'year'); ?>"/>
                        <?php placeErrorDialog('attendee_year'); ?>
                    </div>
                </div>

            <?php }
                if ($vip != 'true') { ?>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">8 Digit IEEE Membership Number</label>
                    <div class="col-md-4">
                        <input id="attendee_ieee_number" name='attendee_ieee_number' type="text" class="form-control input-md" onkeyup='validateIEEE();saveForm();' required value="<?php getValue($value, 'ieee_number');  ?>">
                    </div>
                    <?php placeErrorDialog('attendee_ieee_number'); ?>
                </div>
            <?php } ?>

                <div class="form-group">
                    <label class="col-md-4 control-label" for="attendee_tshirt">Unisex T-Shirt Size</label>
                    <div class="col-md-4">
                        <select id="attendee_tshirt" name="attendee_tshirt" class="form-control" onchange="saveForm()" value="<?php getValue($value, 'tshirt') ?>">
                            <option value="small" <?php returnSelectedIfTrue($value, 'tshirt', 'small') ?> >S</option>
                            <option value="medium" <?php returnSelectedIfTrue($value, 'tshirt', 'medium') ?> >M</option>
                            <option value="large" <?php returnSelectedIfTrue($value, 'tshirt', 'large') ?> >L</option>
                            <option value="xlarge" <?php returnSelectedIfTrue($value, 'tshirt', 'xlarge') ?> >XL</option>
                            <option value="xxlarge" <?php returnSelectedIfTrue($value, 'tshirt', 'xxlarge') ?> >XXL</option>
                        </select>
                    </div>
                </div>
            <?php if ($vip != 'true') { ?>
                <div class="form-group attendee_group_hotel">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label for="attendee_hotel">
                                <input type="checkbox" name="attendee_hotel" id='attendee_hotel' onchange="saveForm();" value="true"  <?php echo returnCheckedIfTrue($value, 'hotel_opt_out'); ?> data-value="<?php echo getValueDefault($value, 'hotel_opt_out', 'true'); ?>"/>
                                Opting <b>OUT</b> of staying at the hotel, <b>Glassboro Marriot</b>? If checked, you will be responsible for your own travel accomodations (no reimbursement provided).
                            </label>
                        </div>
                    </div>
                </div>
            <?php } if (!isValue($value, 'counselor') && $vip != 'true') { ?>
                <br><br>
                <h3>Attendee Competitions</h3>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="attendee_competition">Competition</label>
                    <div class="col-md-4">
                        <select id="attendee_competition" name="attendee_competition" class="form-control" onchange="saveForm()" value="<?php getValue($value, 'competition'); ?>">
                            <option value="null" <?php returnSelectedIfTrue($value, 'competition', 'null') ?>>None of the Above</option>
                            <option value="sumo_kit" <?php returnSelectedIfTrue($value, 'competition', 'sumo_kit') ?>>Sumo Robotics (Kit)</option>
                            <option value="sumo_scratch" <?php returnSelectedIfTrue($value, 'competition', 'sumo_scratch') ?>>Sumo Robotics (Scratch)</option>
                            <option value="brownbag" <?php returnSelectedIfTrue($value, 'competition', 'brownbag') ?>>Brown Bag</option>
                            <option value="micromouse_kit" <?php returnSelectedIfTrue($value, 'competition', 'micromouse_kit') ?>>Micromouse (Kit)</option>
                            <option value="micromouse_scratch" <?php returnSelectedIfTrue($value, 'competition', 'micromouse_scratch') ?>>Micromouse (Scratch)</option>
                            <option value="physics" <?php returnSelectedIfTrue($value, 'competition', 'physics') ?>>Physics</option>
                            <option value="wie" <?php returnSelectedIfTrue($value, 'competition', 'wie') ?>>WIE Competition</option>
                            <option value="ethics" <?php returnSelectedIfTrue($value, 'competition', 'ethics') ?>>Ethics</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label for="attendee_project_showcase" style='display:block'>
                                <input type="checkbox" id="attendee_project_showcase" name="attendee_project_showcase" onchange="saveForm();" value="true" <?php echo returnCheckedIfTrue($value, 'projectshowcase') ?>>
                                Project Showcase
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label for="attendee_paper_competition">
                                <input type="checkbox" id="attendee_paper_competition" name="attendee_paper_competition" onchange="saveForm();" value="true" <?php echo returnCheckedIfTrue($value, 'paper_competition') ?>>
                                <?php echo PAPER_NAME; ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label for="attendee_tshirt_competition">
                                        <input type="checkbox" id="attendee_tshirt_competition" name="attendee_tshirt_competition" onchange="saveForm();" value="<?php echo getValueDefault($value, 'tcomp', 'true') ?>" <?php echo returnCheckedIfTrue($value, 'tcomp') ?>>
                                        T-Shirt Competition
                            </label>
                        </div>
                    </div>
                    <?php placeErrorDialog('attendee_competition'); ?>
                </div>
            <?php }
                if ($vip != 'true') { ?>

                <br><br>
                <h3>Awards Banquet</h3>
                <div class="form-group attendee_group_banquet">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label for="attendee_banquet">
                                <input type="checkbox" id="attendee_banquet" name="attendee_banquet" onchange="saveForm();" value="true" <?php echo returnCheckedIfTrue($value, 'banquet_opt_out') ?>>
                                Opting <strong>OUT</strong> of attending the banquet. If checked yes, you are stating that you will <strong>NOT</strong> be attending the banquet dinner on Saturday, April 8th, 2017.
                            </label>
                        </div>
                    </div>
                </div>
            <?php } ?>

                <br>
                <h4>Select your meal:</h4>
                <strong>Entree</strong>
                <select id='attendee_banquet_entree' name="banquet_entree" value="<?php getValue($value, 'banquet_entree'); ?>">
                    <option value="cordon_bleu" <?php returnSelectedIfTrue($value, 'banquet_entree', 'cordon_bleu') ?>>Boneless Breast of capon Cordon Bleu</option>
                    <option value="baked_flounder" <?php returnSelectedIfTrue($value, 'banquet_entree', 'baked_flounder') ?>>Baked Flounder (in Lemon Butter Sauce)</option>
                    <option value="pasta" <?php returnSelectedIfTrue($value, 'banquet_entree', 'pasta') ?>>Pasta Primavera (Vegetarian)</option>
                </select>

                <select id='attendee_banquet_veg' name="banquet_veg" value="<?php getValue($value, 'banquet_veg'); ?>" style='display:none'>
                    <option value="potatoes">Oven Browned Potatoes</option>
                    <option value="mixed_veggies">Italian Mixed Vegetables</option>
                </select>

                <strong>Comments or special requests</strong>
                <textarea id='attendee_special' name='attendee_special' onchange="saveForm();">
                    <?php trim(getValue($value, 'special')); ?>
                </textarea>
            <?php if ($vip != 'true') { ?>
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="checkbox">
                            <label for="attendee_toc">
                                <input type="checkbox" name="attendee_toc" id='attendee_toc' onchange="saveForm();" value="true"  <?php echo returnCheckedIfTrue($value, 'toc'); ?> data-value="<?php echo getValueDefault($value, 'toc', 'true'); ?>"/>
                                By checking this box, I acknowledge that I have carefully read the <a href='docs/IEEE SAC Waiver.pdf' target="_blank" onclick='$("#attendee_toc").show(100)'>SAC17 waiver</a> and fully understand that it is a release of liability and that I am at least 18 years of age and competent to sign this document.
                            </label>
                        </div>
                    </div>
                    <?php placeErrorDialog('attendee_toc'); ?>
                </div>
            <?php } ?>
            <?php if ($vip == 'true') { ?>
            <a style='cursor:pointer;' onclick='$("#plus_one").show(100);'>I'm bringing a guest</a>
            <div id='plus_one' style='display:<?php if (isValue($value, 'guest_name')) { echo 'block'; } else { echo 'none'; } ?>'>
                <br><br><br>
                <h4>Guest Details</h4>
                <div class="form-group">
                    <div class="col-md-4">
                    <label class="col-md-4 control-label" for="textinput">Name of Guest</label>
                    <div class="col-md-4">
                        <input id="guest_name" name='guest_name' type="text" class="form-control input-md" onkeyup='saveForm();' required value="<?php getValue($value, 'guest_name');  ?>">
                    </div>
                </div>
                <br>
                <strong>Select Guest&#039;s Entree</strong>
                <select id='guest_banquet_entree' name="guest_banquet_entree" value="">
                    <option value="cordon_bleu" <?php returnSelectedIfTrue($value, 'guest_banquet_entree', 'cordon_bleu') ?>>Boneless Breast of capon Cordon Bleu</option>
                    <option value="baked_flounder" <?php returnSelectedIfTrue($value, 'guest_banquet_entree', 'baked_flounder') ?>>Baked Flounder (in Lemon Butter Sauce)</option>
                    <option value="pasta" <?php returnSelectedIfTrue($value, 'guest_banquet_entree', 'pasta') ?>>Pasta Primavera (Vegetarian)</option>
                </select>                    
            </div>
            <?php } ?>
            </div>
        </div>

        <div id='form_sample'>

        </div>

        <style>
            #form_sample, #form_global, .panel-footer {
                display: none;
            }
            .entry_form input[type='text'], .entry_form input[type='number'], .entry_form input[type='email'], input[type='tel'] {
                display: block;
                margin-top: 4px;
                margin-left: 4px;
                border-radius: 5px;
                padding: 2px;
            }

            .attendee_special {
                margin-left:16px;
                margin-right:16px;
                margin-top:4px;
                margin-bottom: 4px;
                border: solid 1px #999;
                border-radius: 5px;
                padding:4px;
            }
            iframe.stripe {
                width:400px;
                height:600px;
                border:0px;
            }
        </style>

        <script>
            registration = localStorage['registration_personal_<?php echo $_GET['attendee']; ?>'] || "{}";
            registration = JSON.parse(registration);
            registration.users = registration.users || [];

            function loadIfPossible(id) {
                if (registration[id] !== undefined && registration[id] !== "" && document.getElementById(id).value == "") {
                    document.getElementById(id).value = registration[id].trim();
                }
            }

            function checkIfPossible(id) {
                if (registration[id] == true && document.getElementById(id).checked == false) {
                    document.getElementById(id).checked = true;
                    return true;
                }
                return false;
            }

            function loadForm() {
                document.getElementById('formform').innerHTML = document.getElementById('form_global').innerHTML;
                setTimeout(function() {
                    // Load global args
//                    loadIfPossible('attendee_name');
//                    loadIfPossible('attendee_email');
                    loadIfPossible('attendee_phone');
                    loadIfPossible('attendee_school');
                    loadIfPossible('attendee_year');
                    loadIfPossible('attendee_ieee_number');
                    loadIfPossible('attendee_tshirt');
                    checkIfPossible('attendee_hotel');
                    loadIfPossible('attendee_competition');
                    checkIfPossible('attendee_project_showcase');
                    checkIfPossible('attendee_tshirt_competition');
                    checkIfPossible('attendee_paper_competition');
                    checkIfPossible('attendee_banquet');
                    loadIfPossible('attendee_banquet_entree');
                    loadIfPossible('attendee_special');

                    refreshGlobals();
                    formUpdate();
                    $('.error').hide();
                    $('.warning').hide();
                }, 30);
            }

            function saveForm() {
                var r = {};
                // Save global data
                r['attendee_name'] = document.getElementById('attendee_name').value;
                r['attendee_email'] = document.getElementById('attendee_email').value;
                r['attendee_phone'] = document.getElementById('attendee_phone').value;
                // Cannot read property 'value' of null
                <?php if (!isValue($value, 'counselor')) { ?>
                r['attendee_school'] = document.getElementById('attendee_school').value;
                r['attendee_year'] = document.getElementById('attendee_year').value;
                <?php } ?>
                r['attendee_ieee_number'] = document.getElementById('attendee_ieee_number').value;
                r['attendee_tshirt'] = document.getElementById('attendee_tshirt').value;
                r['attendee_hotel'] = document.getElementById('attendee_hotel').checked;
                <?php if (!isValue($value, 'counselor')) { ?>
                r['attendee_competition'] = document.getElementById('attendee_competition').value;
                r['attendee_project_showcase'] = document.getElementById('attendee_project_showcase').checked;
                r['attendee_tshirt_competition'] = document.getElementById('attendee_tshirt_competition').checked;
                r['attendee_paper_competition'] = document.getElementById('attendee_paper_competition').checked;
                <?php } ?>
                r['attendee_banquet'] = document.getElementById('attendee_banquet').checked;
                r['attendee_banquet_entree'] = document.getElementById('attendee_banquet_entree').value;
                r['attendee_special'] = document.getElementById('attendee_special').value.trim();


                localStorage['registration_personal_<?php echo $_GET['attendee']; ?>'] = JSON.stringify(r);
                registration = r;
                formUpdate();

                loadIfPossible('attendee_special'); // Retrim
            }

            function formUpdate() {

            }

            function refreshGlobals() {
            }

            function add() {
                saveForm();
                loadForm();
            }

            function submit() {
                if (validate()) {
                    console.log("Starting updating process");
                    $('.entry_form').hide(300);
                    $('#add').hide(300);
                    $('#form_submission').hide(300);
                    <?php if ($createNewAccount) { ?>
                    updateAccount();
                    <?php } else { ?>
                    // Change password if provided
                    updateAccount();
                    <?php } ?>
                    console.log("Submitting form");
                    $('form').submit(); // Submit the form and do what comes next
                } else {
                    $('#submit_error').html("Fix the errors and make sure all fields are filled in the form before submitting.");
                }
            }

            /*function createAccount() {
                firebase.auth().createUserWithEmailAndPassword($('#attendee_email').val(), $('#attendee_password').val()).catch(function(error) {
                    // Handle Errors here.
                    var errorCode = error.code;
                    var errorMessage = error.message;
                    console.log(errorCode, errorMessage); // Not sure how to handle potential errors
                    alert("Error creating account " + errorMessage);
                });
            }*/

            function updateAccount() {
                <?php if ($vip != 'true') { ?>
                if (!showErrorIfTrue(document.getElementById('attendee_password').value != document.getElementById('attendee_password2').value, 'attendee_password', 'Your passwords don\'t match') && !showErrorIfTrue(document.getElementById('attendee_password').value.length < 8, 'attendee_password', 'Your password must be 8 or more characters')) {
                    var user = firebase.auth().currentUser;
                    console.log("Updating password...");
                    if (user == null || user == undefined) {
                        console.error("The user doesn't exist!");
                    }
                    user.updatePassword($('#attendee_password').val()).then(function() {
                        // Update successful.
                    }, function(error) {
                        // An error happened.
                        console.error(error);
                        $('.warning').show(100);
                    });
                }
                <?php } ?>
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
//                    console.log($('#' + formid + ' > div > p'));
//                    console.log(formtext);
                    $('#' + formid + ' > div > p').html(formtext);
                    $('#' + formid).show(100);
                } else {
                    $('#' + formid).hide(100);
                }
                return boolean;
            }

            function validateIEEE() {
                return showErrorIfTrue(document.getElementById('attendee_ieee_number').value.length != 8, 'attendee_ieee_number', "Your IEEE number must be 8 digits long");
            }

            function validatePassword1() {
                if (typeof pm != "undefined") {
                    pm.check();
                    return showErrorIfTrue(document.getElementById('attendee_password').value != document.getElementById('attendee_password2').value && createNewAccount, 'attendee_password', 'Your passwords don\'t match') || showErrorIfTrue(document.getElementById('attendee_password').value.length < 8 && createNewAccount, 'attendee_password', 'Your password must be 8 or more characters');
                }
                return false;
            }

            function validatePassword2() {

            }

            function validateSchool() {
                return showErrorIfTrue(document.getElementById('attendee_school').value.length == 0, "attendee_school", "Your school name must be filled in")
            }

            function validateYear() {
               return  showErrorIfTrue(document.getElementById('attendee_year').value.length == 0, "attendee_year", "Your school year must be filled in")
            }

            function validateWaiver() {
                return showErrorIfTrue(!document.getElementById('attendee_toc').checked, "attendee_toc", "You must agree to the waiver");
            }

            function validateSex() {
                return showErrorIfTrue($('[name="attendee_sex"]:checked').val() === undefined, "attendee_sex", "You must provide a value for this field.")
            }

            function validatePhone() {
                // Not required, but detect errorneous characters
                return showErrorIfTrue($('#attendee_phone').val().match('[a-zA-Z]') !== null, "attendee_phone", "This is not a valid phone number.");
            }
            
            function validateCompetition() {
                return showErrorIfTrue($('#attendee_competition').val() == "null" && $('#attendee_paper_competition:checked').val() === undefined && $('#attendee_tshirt_competition:checked').val() === undefined && $('#attendee_project_showcase:checked').val() === undefined, "attendee_competition", "You must select at least one competition");
            }

            function validate() {
                $('#submit_error').html("");
                if (typeof pm != "undefined") {
                    pm.check();
                }

                var result = false;
                console.log("Validating user input data. True is bad.");
                <?php if ($vip != 'true') { ?>
                    result = validateIEEE() || result;
                    console.log("Validating IEEE number", result);
                    result = validatePassword1() || result;
                    result =  validatePassword2() || result;
                    console.log("Validating Passwords", result);
                    result =  validateWaiver() || result;
                    console.log("Validating Waiver selection", result);
                    result = validateSex() || result;
                    console.log("Validating sex", result);
                    result = validatePhone() || result;
                    console.log("Validating phone number");

                    // Don't validate certain fields if you're a counselor.
                    <?php if (!isValue($value, 'counselor')) { ?>
                    result = validateSchool() || result;
                    console.log("Validating user school", result);
                    result = validateYear() || result;
                    console.log("Validating user year", result);
                    result = validateCompetition() || result;
                    console.log("Validating competition", result);
                    <?php } ?>
                <?php } ?>
                console.log("Submit? " + result);
                return !result;
            }

            loadForm();

            // PASSWORD STRENGTH
            (function() {
            function PasswordMeter( element, meter ) {

                this.element = element;
                this.elementValue = this.element.value;
                this.elementValueLength = this.elementValue.length;
                this.meter = meter;
                this.meterWidth = this.meter.offsetWidth;
                this.meterBar = this.meter.querySelector( "#password-bar" );

                this.tokens = {
                    letters: "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",
                    numbers: "0123456789",
                    specialChars: "!&%/()=?^*+][#><;:,._-|"
                };

                this.letters = this.tokens.letters.split( "" );
                this.numbers = this.tokens.numbers.split( "" );
                this.specialChars = this.tokens.specialChars.split( "" );
                this.init();
            }

            PasswordMeter.prototype = {
                init: function() {
                    this.check();
                },
                check: function() {
                    var self = this;
//                    console.log(self.element);
                    var val = document.getElementById("attendee_password").value || "";
//                    console.log(val);
                    var total = self.elementValueLength;
//                    console.log(total);

                    var totalLetters = 0;
                    var totalNumbers = 0;
                    var totalSpecialChars = 0;

                    var tokens = val.split( "" );
                    var len = tokens.length;
                    var i;

                    for( i = 0; i < len; ++i ) {
                        var token = tokens[i];
                        if( self._isLetter( token ) ) {
                            totalLetters++;
                        } else if( self._isNumber( token ) ) {
                            totalNumbers++;
                        } else if( self._isSpecialChar( token ) ) {
                            totalSpecialChars++;
                        }

                    }
                    total = val.length;


                    var result = self._calculate( total, totalLetters, totalNumbers, totalSpecialChars );
                    var perc = result * 10;
                    var percStr = perc.toString();
                    self.meterBar.style.width = percStr + "%";
                },
                _isLetter: function( token ) {
                    var self = this;
                    if( self.letters.indexOf( token ) == -1 ) {
                        return false;
                    }
                    return true;
                },
                _isNumber: function( token ) {
                    var self = this;
                    if( self.numbers.indexOf( token ) == -1 ) {
                        return false;
                    }
                    return true;
                },
                _isSpecialChar: function( token ) {
                    var self = this;
                    if( self.specialChars.indexOf( token ) == -1 ) {
                        return false;
                    }
                    return true;
                },
                _calculate: function( total, letters, numbers, chars ) {
                    if (total == 0) {
//                        console.log(total, letters, numbers, chars);
                        return 0;
                    }
                    var level = 0;
                    var l = parseInt( letters, 10 );
                    var n = parseInt( numbers, 10 );
                    var c = parseInt( chars, 10 );

                    if ( total < 16 ) {
                        level += 1;
                    }
                    if( total >= 16 ) {
                        level += 4;
                    }
                    if( l > 0 ) {
                        level += 1;
                    }
                    if( n > 0 ) {
                        level += 2;
                    }
                    if( c > 0 ) {
                        level += 3;
                    }
                    return level;
                }
            };

            document.addEventListener("DOMContentLoaded", function() {
                var password = document.querySelector("#attendee_password"),
                    meter = document.querySelector("#password-level");
                    pm = new PasswordMeter(password, meter);
            });

        })();
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
<?php
} else {
?>
<div class="content-wrapper clearfix">
	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
                <?php if (isset($_GET['update'])) { ?>
    			<div class="message success">
                    <div class="icon">
                        <h3 style='color:white'>Thanks for updating your profile!</h3><br>
                        <p>You are now completely registered for SAC!</p>
                    </div>
                </div>
                <?php } else { ?>
				<h1>Sorry. You cannot update this form now.</h1>
                <?php } ?>
                <h2>Your information is below.</h2>
			</div>
		</div>
		<div class="clear"></div>
        <?php 
            $properties = array(
                "name" => "Name",
                "email" => "E-Mail",
                "phone" => "Phone Number",
                "school" => "School",
                "year" => "Year",
                "competition" => "Primary Competition",
                "tshirt_competition" => "T-Shirt Competition",
                "project_showcase" => "Project Showcase",
                "paper_competition" => "Paper Competition",
                "ieee_number" => "IEEE Number",
                "tshirt" => "T-Shirt Size (unisex)",
                "banquet_entree" => "Banquet Dinner Choice",
                "special" => "Special Requests",
                "guest_name" => "Name of Guest",
                "guest_banquet_entree" => "Guest's Banquet Dinner Choice"
                );
        
            foreach ($properties as $key => $val) {
                if (property_exists($value, $key) && strlen($value->$key) > 0 && $value->$key != 'false' && $value->$key != false) {
                    echo "<div><strong>$val</strong>: " . $value->$key . "</div>";
                }
            }
        ?>

        <button onclick="window.location = '?p=home'" style='display:block;margin-left:auto;margin-right:auto;margin-top:12px;margin-bottom:12px;'>OKAY</button>
        <img src='http://sac17.rowanieee.org/photos/rowanhall.jpg' style='width:100%' />
        <link rel="stylesheet" type="text/css" href="css/registration.css">
    </div><!-- END .container -->
</div><!-- END .homepage-content-wrapper -->
<?php
}
?>
