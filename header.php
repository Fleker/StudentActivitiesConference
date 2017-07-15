<?php
if ($_SERVER['REMOTE_ADDR'] == "::1") {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0); // turn off error reporting (they're logged to server anyway)
}
include 'config.php';

?>

<?php include ('firebase_include.php'); ?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title>Rowan University IEEE Student Branch</title>
	<meta name="description" content="">
	<meta name="author" content="">
    <link rel="manifest" href="manifest.json">

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-itunes-app" content="app-id=1208273739">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="css/base.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/layout.min.css">
	<link rel="stylesheet" href="css/shortcodes.min.css">
	<link rel="stylesheet" href="css/flexslider.min.css">
	<link rel="stylesheet" href="css/prettyPhoto.min.css">
	<link rel="stylesheet" href="css/style.min.css">
    <link rel="stylesheet" type="text/css" href="css/registration.min.css">

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>

	<!-- JS -->
	<script src="js/jquery-1.8.1.min.js"></script>

	<!-- Favicons -->
	<link rel="shortcut icon" href="images/favicon.png">

	<!-- Styles -->
	<link id="css_color" href='css/styles/rowanbrown.css' rel='stylesheet' type='text/css' />
    <!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="js/fancybox/jquery.mousewheel.pack.js?v=3.1.3"></script>

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="js/fancybox/jquery.fancybox.js?v=2.1.5"></script>
	<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-buttons.css?v=1.0.5" />
	<script type="text/javascript" src="js/fancybox/jquery.fancybox-buttons.js?v=1.0.5"></script>

	<!-- Add Thumbnail helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-thumbs.css?v=1.0.7" />
	<script type="text/javascript" src="js/fancybox/jquery.fancybox-thumbs.js?v=1.0.7"></script>

	<!-- Add Media helper (this is optional) -->
	<script type="text/javascript" src="js/fancybox/jquery.fancybox-media.js?v=1.0.6"></script>

	<?php if($page == 'home') { ?>
	<script type="text/javascript">
      jQuery(window).load(function() {
		jQuery('#slider').fitVids().flexslider({
			animation: "slide",
			controlNav: true,
			directionNav: true,
			slideshowSpeed: 5000,
			animationLoop: true,
			smoothHeight: true,
			pauseOnHover: true
		});
    });
	</script>
	<?php } ?>

	<?php if($page == 'gallery') { ?>
	<script type="text/javascript" charset="utf-8">
	  $(document).ready(function(){
		$("a[rel^='prettyPhoto']").prettyPhoto();
		<?php if(isset($_GET["tv"])) { ?>
		$("a[rel^='prettyPhoto']").prettyPhoto({slideshow:5000, autoplay_slideshow:true,show_title:true,allow_resize:false,allow_expand:true});
		<?php } ?>
	  });
	</script>
	<?php } ?>
    <?php
        // Include Firebase code
        include 'firebase_client.php';
    ?>
    
    <?php if (isset($_GET['p']) && $_GET['p'] == 'memsat') { ?>
        <script src='https://github.com/craftyjs/Crafty/releases/download/0.8.0/crafty-min.js'></script>
<!--        <script src='http://craftyjs.com/release/0.4.2/crafty.js'></script>-->
        <script src='memsat/game/game.min.js'></script>
    <?php } ?>
</head>
<body class="responsive">
<?php if(!isset($_GET["app"])) { ?>
<div class="top-wrapper clearfix">
	<div class="logo-wrapper">

		<div class="logo">
				<img src="https://cdn.frontify.com/api/screen/thumbnail/kynYWkKDPZo6HJ_57TplX_Jpanjvb6sHZFNqJoi9C72S7GMFbW_PBGCT4szzqkCYLyZ_uIb7aktGp5fYf510UA/770" width='80px' alt="logo" />
		</div>

	</div><!-- END .logo-wrapper -->

    <div class="info-wrapper">

    		<div class="info-inner">
    			<span class="info-mail"><a>Rowan University &copy; 2017</a></span><br>
                <span id='userauth' class='info-mail'></span>
    		</div>

    	</div>

</div><!-- END .top-wrapper -->


<!-- BEGIN .header-wrapper -->
<div class="header-wrapper clearfix">

	<!-- BEGIN .header -->
	<div class="header">
		<div class="navigation-wrapper clearfix">

			<ul class="sf-menu" id="main-nav">
				<li><a href="index.php?p=home"><i class="icon-home"></i> Home</a></li>
                <?php if (!isset($_GET['app']) && time() < TIME_LATE_BIRD) { ?>
                <!-- Only show registration button on website (not app) and if registration is open -->
                <li><a href="index.php?p=registration_buyer">Register</a></li>
                <?php } ?>
                <li>
                    <a href="#">Sponsors</a>
                    <ul>
                        <li><a href="index.php?p=sponsors">Current Sponsors</a><li>
                        <li><a href="index.php?p=sponsorwhy">Why Sponsor?</a></li>
                    </ul>
                </li>
                <li><a href="index.php?p=photos">Photos</a>
                <li><a href="index.php?p=vote">Vote</a></li>
                <!-- Registration is closed -->
<!--                <li><a href="index.php?p=registration">Registration</a></li> -->
                <li><a href="index.php?p=competitions">Competitions</a></li>
                <li><a href="index.php?p=hotel">Hotel</a></li>
                <li><a href="index.php?p=banquet">Banquet</a></li>
                <li><a href="index.php?p=schedule">Schedule</a></li>
                <li><a href="index.php?p=faq">FAQ</a></li>
                <li class='admin_menu' style='display:none'>
                    <a href="#">Admin</a>
                    <ul>
                        <li class='admin_item'><a href='?p=registration_stats'>Registration Stats</a></li>
                        <li class='admin_item'><a href='?p=admin_search'>View Attendees</a></li>
                        <li class='admin_item'><a href='?p=admin_export'>Database Backups</a></li>
                        <li class='admin_item'><a href='?p=photo_approval'>Approve Photos</a></li>
                        <li class='admin_item'><a href='?p=pico_conf_admin'>Pico Conference</a></li>
                        <li class='admin_item'><a href='?p=tshirt_admin'>T-Shirt</a></li>
                        <li class='admin_item'><a href= '?p=project_showcase_admin'>Project Showcase</a></li>
                        <li class='admin_item'><a href='?p=admin_vip&password=spacex_elon_musk'>Register VIPs</a></li>
                        <li class='admin_item'><a href='?p=admin_banquet_tables'>Banquet Table</a></li>
                        <li class='admin_item'><a href='?p=vote_results'>Voting Results</a></li>
                        <li class='admin_item'><a href='?p=settings'>Settings</a></li>
                    </ul>
                </li>
                <li id='pico_conf_mngr' style='display:none'><a href='index.php?p=pico_conf_dashboard'>Pico Conference</a></li>
                <li id='tshirt_mngr' style='display:none'><a href='index.php?p=tshirt_dashboard'>T-Shirt Dashboard</a></li>
                <li id='project_mngr' style='display:none'><a href='index.php?p=project_showcase_dashboard'>Project Showcase Dashboard</a></li>
			</ul><!-- END .sf-menu -->

            <style>
                .admin_item {
                    display:none;
                    font-style:italic;
                }
            </style>

		</div><!-- END .navigation-wrapper -->
	</div><!-- END .header -->

</div><!-- END .header-wrapper -->

<div class="clear"></div>
<?php }
?>
