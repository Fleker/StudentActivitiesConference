<html>
    <head>
<!--        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>-->
    </head>
    <body>
        <form id="paymentsform" action="registration_submit.php">
            <input type="hidden" name="registration_form_id" value="<?php echo $_GET['submission']; ?>" />
            <script
                src="https://checkout.stripe.com/checkout.js"
                class="stripe-button"
                data-key=""
                data-amount="<?php echo $_GET['cost']; ?>"
                data-name="Stripe.com"
                data-description="Widget"
                data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                data-locale="auto"
                data-zip-code="true">
            </script>
        </form>
    </body>
</html>
