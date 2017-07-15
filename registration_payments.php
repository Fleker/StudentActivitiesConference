<?php

const DEBUG = 0;
require 'registration_helper.php';

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}

function singleTicketPrice($index, $additionallyRegisteredStudents) {
    if (strpos(strtolower($_GET['school']), 'rowan') !== false || strpos(strtolower($_GET['school']), 'stockton') !== false) {
        return 3000; // $30
    }
    if ($index + $additionallyRegisteredStudents >= 10) {
        return OVERFLOW_TICKET_PRICE;   
    }
    return TICKET_PRICE;
}

function ticketPrice($count) {
    $attendeeCountUrl = str_replace(' ', '%20', htmlspecialchars("http://sac17.rowanieee.org/registration_school_count.php?school=".$_GET['school']));
    $attendeeResponse = file_get_contents($attendeeCountUrl);
    $additionalAttendees = json_decode($attendeeResponse)->count;
    if ($_GET['counselor'] == true || $_GET['counselor'] == "true") {
        $count--;   
    }
    $sum = 0;
    for ($i = 0; $i < $count; $i++) {
        $sum += singleTicketPrice($i, $additionalAttendees);
    }
    return $sum;
}

?>
<div class="content-wrapper clearfix">
<?php
    if (DEBUG) {
        $school = $_GET['school'];
        $url = str_replace(' ', '%20', htmlspecialchars("http://localhost/sac/registration_school_count.php?school=".$school));
        echo $school."<br>".$url."<br>".(file_get_contents($url))."<br>";
        echo json_decode(file_get_contents($url))->count."<br>";
        echo ticketPrice(0)."   ".ticketPrice(1)."   ".ticketPrice(2);
    }
?>

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Registration Form</h1>
				<h2>Pay securely with Stripe.</h2>
                <small>Payments are made securely using Stripe. We do not store credit card information.</small><br>
                <?php
                    if (isset($_GET['error'])) {
                        if ($_GET['error'] == "card") { ?>
                            <b>Error: Card was declined</b> <!-- Todo use error box-->   
                <?php    }
                    }
                ?>
			</div>
		</div>

		<div class="clear"></div>
        <form id='formform' method="post" action="registration_submit.php" onsubmit="return validate();" class='entry_form'>
            <h3>Ticket Buyer</h3>
                <small>Payments are made securely using Stripe. We do not store credit card information.</small>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">First &amp; Last Name</label>  
                    <div class="col-md-4">
                        <input id="buyer_name" name="buyer_name" type="text" placeholder="" class="form-control input-md" oninput='validate()' required>
                        <?php placeErrorDialog('buyer_name'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">E-Mail Address</label>  
                    <div class="col-md-4">
                        <input id="buyer_email" name="buyer_email" type="email" placeholder="" class="form-control input-md" oninput='validate()' required>
                        <?php placeErrorDialog('buyer_email'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="textinput">Phone Number</label>  
                    <div class="col-md-4">
                        <input id="buyer_phone" name="buyer_phone" type="tel" placeholder="" class="form-control input-md" oninput='validate()' required>
                    </div>
                </div>
            
            <script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="<?php echo STRIPE_PUBLIC_KEY; ?>"
                data-amount="<?php
                    $value = $firebase->get(DEFAULT_PATH . '/buyers/' . $_GET['payment'] . '/attendees');
                    $attendees = 0;
                    if (strlen($value) > 0) {
                        $attendees = count(explode(',', $value));
                    }
                    $total_price = ticketPrice($attendees);
                    echo $total_price;
                ?>"
                data-name="Complete SAC Registration"
                data-description="<?php echo "Pay with the ".PAYMENT_TITLE." ticket price"; ?>"
                data-image="https://cdn.frontify.com/api/screen/thumbnail/kynYWkKDPZo6HJ_57TplX_Jpanjvb6sHZFNqJoi9C72S7GMFbW_PBGCT4szzqkCYLyZ_uIb7aktGp5fYf510UA/770"
                data-locale="auto"
                data-zip-code="true">
              </script>
            
            <input type="hidden" value="<?php echo $_GET['payment']; ?>" name="firebase_key"/>
            <input type="hidden" value="<?php echo $total_price ?>" name="price"/>
        </form>
        
        <script>
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
            
            function validate() {
                var validationIssue = false;
                validationIssue =  showErrorIfTrue(document.querySelectorAll('#buyer_name')[0].value.match('\\s\\w') == null, 'buyer_name', "Please enter first and last name") || validationIssue;
                validationIssue = showErrorIfTrue(document.querySelectorAll('#buyer_email')[0].value.match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) == null, 'buyer_email', "Please enter a valid email") || validationIssue;
                if (!validationIssue) {
                    $('.stripe-button-el').css('opacity', 1); // We can't prevent form submission, but we can basically hide it.   
                } else {
                    $('.stripe-button-el').css('opacity', 0);   
                }
                return !validationIssue;
            }
            
            // Hide all errors
            $('.message').hide();
            $('.stripe-button-el').css('opacity', 0); 
        </script>
        <!--<strong style="display:block; text-align:center;">Total Price: $<?php echo $total_price/100; ?></strong>-->
    </div>
</div>

<link rel="stylesheet" type="text/css" href="css/registration.css">
