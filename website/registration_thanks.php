<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Thanks for Registering!</h1>
                Attendees have received an email asking them to finish registering.
			</div>
		</div>

		<div class="clear"></div>
        
        <div style='font-weight:bold; text-align:center;'>Are you excited yet? You should be! The Student Activities Conference is happening soon!</div>
        <button onclick="window.location = '?p=home'" style='display:block;margin-left:auto;margin-right:auto;margin-top:12px;margin-bottom:12px;'>OKAY</button>
        
        <?php
            if (isset($_GET['attendees']) && count(explode(",", $_GET['attendees'])) == 1) { ?>
                <button onclick="window.location = '?p=registration_attendee&attendee=<?php echo $_GET['attendees']; ?>'" class='button xlarge yellow' style='margin-left:auto; margin-right:auto; display:block;'>Continue Registering</button>
        <?php } ?>
        
        <img src='photos/rowanhall.jpg' style='width:100%' />
        
	</div>

</div>