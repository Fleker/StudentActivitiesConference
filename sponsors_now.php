<?php
    $step = isset($_POST['step']) ? $_POST['step'] + 1 : 0;
    $showBasicDetails = $step == 0;
    $basicDetailsType = $showBasicDetails ? "text" : "hidden";
    $showStripe = $step == 1;

    if ($step == 2) {
        // Complete the payment
        require_once('stripe/init.php');
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        
        $customer = \Stripe\Customer::create(array(
            "email" => $_POST['email'],
            "source" => $_POST['stripeToken'],
        ));

        $charge = \Stripe\Charge::create(array(
            "amount" => $_POST['amount'] * 100, // Amount in cents
            "currency" => "usd",
            "customer" => $customer->id,
            "description" => "Payment from user"
            ));
        
        // Throw into database
        include 'firebase_include.php'; // Include Firebase API
        $sponsor = array(
                "amount" => $_POST['amount'] * 100, // To be compatible with Stripe
                "email" => $_POST['email'],
                "contact" => $_POST['name'],
                "phone" => $_POST['phone'],
                "company" => $_POST['company'],
                "stripe_token" => $_POST['stripeToken'],
                "date" => time()
            );
        $firebase->push(DEFAULT_PATH . '/sponsors/', $sponsor);
        
        // Send an email to Culleny.
        include 'mail_config.php';
        $message = "Here are the details:<br><br><ul><li>".$_POST['company']."</li><li>".$_POST['name']."</li><li>".$_POST['phone']."</li><li><strong>$".$_POST['amount']."</strong></li></ul>";
        sendEmail("felkern0@students.rowan.edu", "Jake Culleny", "Another company is sponsorship SAC!", $message);
        
        // Send an email to that person to confirm the payment was successful.
        if (false) {
            $message = "Thanks again for your valuable contribution! Below are the details<br><br><ul><li>".$_POST['company']."</li><li>".$_POST['name']."</li><li>".$_POST['phone']."</li><li><strong>$".$_POST['amount']."</strong></li></ul>";
            sendEmail($_POST['email'], $_POST['name'], "Thanks for Sponsoring SAC17", $message);
        }
    }   

//$step = 2;
?>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1><?php if ($step < 2) { echo "SAC 2017 Sponsorship"; } else { echo "THANKS!"; } ?></h1>
				<h2><?php if ($step < 2) { echo "Become a sponsor now! Just provide a few details."; } else { echo "Your contribution to this event's success is greatly appreciated."; } ?></h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <?php if ($step == 1) { ?>
            <style> 
                .form-group {
                    display:none;   
                }
            </style>
        <?php } ?>
        
        <?php if ($step < 2) { ?>
        <form action='?p=sponsors_now' method="post">
            <input name="step" type='hidden' value='<?php echo $step ?>' />
            <div class="form-group">
                <label class="col-md-4 control-label" for="textinput">Name of Company or Organization</label>  
                <div class="col-md-4">
                    <input name='company' class="form-control input-md" type='<?php echo $basicDetailsType; ?>' value='<?php echo $_POST['company']; ?>' required />
                </div>
            </div>    
            
            <div class="form-group">
                <label class="col-md-4 control-label" for="textinput">Name of Contact</label>  
                <div class="col-md-4">
                    <input name='name' class="form-control input-md" type='<?php echo $basicDetailsType; ?>' value='<?php echo $_POST['name']; ?>' required />
                </div>
            </div>     
            
            <div class="form-group">
                <label class="col-md-4 control-label" for="textinput">Contact E-Mail Address</label>  
                <div class="col-md-4">
                    <input name='email' class="form-control input-md" value='<?php echo $_POST['email']; ?>' type='<?php echo $showBasicDetails ? "email" : "hidden"; ?>' required />
                </div>
            </div>        
            
            <div class="form-group">
                <label class="col-md-4 control-label" for="textinput">Contact Phone Number</label>  
                <div class="col-md-4">
                    <input name='phone' class="form-control input-md" value='<?php echo $_POST['phone']; ?>' type='<?php echo $showBasicDetails ? "text" : "hidden"; ?>' required />
                </div>
            </div>      
            
            <div class="form-group">
                <label class="col-md-4 control-label" for="textinput">Sponorship Amount ($). (Put in the integer value. Do not use decimals or commas)</label>  
                <div class="col-md-4">
                    <input name='amount' class="form-control input-md" value='<?php echo $_POST['amount']; ?>' type='<?php echo $showBasicDetails ? "number" : "hidden"; ?>' required onkeyup='formatMoney()' id='amount' />
                </div>
            </div>
            
            
            
            
         

            
            <?php if ($showBasicDetails) { ?>
            <br><br>
                <button type='submit'>Go to Payment</button>
            <?php } else if ($showStripe) { ?>
                <script
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="<?php echo STRIPE_PUBLIC_KEY; ?>"
                    data-amount="<?php
                        echo $_POST['amount'] * 100;
                    ?>"
                    data-name="Complete Sponsorship"
                    data-description="Sponsoring on behalf of <?php echo $_POST['company']; ?>"
                    data-image="https://cdn.frontify.com/api/screen/thumbnail/kynYWkKDPZo6HJ_57TplX_Jpanjvb6sHZFNqJoi9C72S7GMFbW_PBGCT4szzqkCYLyZ_uIb7aktGp5fYf510UA/770"
                    data-locale="auto"
                    data-zip-code="true">
                  </script>
            <?php } ?>
        </form>
        <?php } else { ?>
        <div style='text-align:center;'>
            <strong>You will receive an email confirming the payment.</strong>
            <button onclick="window.location = '?p=home'" style='display:block;margin-left:auto;margin-right:auto;margin-top:12px;margin-bottom:12px;'>RETURN HOME</button>
            <img src='photos/rowanhall.jpg' style='width:100%' />
        </div>
        <?php } ?>
        
        <script>
            function formatMoney() {
                $('#amount').val($('#amount').val().replace(/,/g, ''));
            }
        </script>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->