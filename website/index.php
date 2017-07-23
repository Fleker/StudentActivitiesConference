<?php 

if(!isset($_GET["p"]))			// If page is not set
	$page = "home";				// Default to Main			
else 
	$page = preg_replace('/[^-a-zA-Z0-9_]/', '',$_GET["p"]);

include("header.php"); 			// Includes the header file.

include($page.".php");			// Includes intended page

include("footer.php"); 			// Includes footer

// Sidebars are included on per page basis!

?>
