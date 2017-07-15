<?php if(!isset($_GET["app"])) { ?>
<div class="footer-wrapper">

	<a href="#" class="scrollup">Go To Top</a>
	
	<div class="footer">
	
		<div class="container">

			<div class="four columns">
		    	<div class="widget">
			        <h6>Contact Us</h6>
					<ul>
						<li><i class="icon-home"></i>Rowan IEEE Branch Office: Back of Room 237 (2nd Floor)</li>
						<li><i class="icon-home"></i>Rowan University: 201 Mullica Hill Rd, Glassboro NJ 08028</li>
						<li><i class="icon-envelope-alt"></i> <a href="#">ieee@rowan.edu</a></li>
					</ul>
				</div><!-- END .widget -->        
			</div>

			<div class="four columns">
				<div class="widget">
					<h6>Photos</h6>
					 <ul>
			        	<li>View <a href="?p=photos">photos</a> from the 2017 SAC</li>
			        	<li>You may also add <a href="?p=gallery">photos</a> from the 2017 SAC to our website!</li>
			        </ul>					
				</div><!-- END .widget -->
			</div>
			
			<div class="four columns">
		        <div class="widget">
			        <h6>Useful Links</h6>
			        <ul>
			        	<li><a href="http://rowanieee.org">Rowan IEEE Website</a></li>
			        	<li><a href="https://www.ieee.org/index.html">IEEE Official Site</a></li>
						<li><a href="http://www.rowan.edu">Rowan University</a></li>
			        </ul>
		        </div><!-- END .widget -->
		    </div>

			<div class="clear"></div>
		
		</div>
		
		<div class="clear"></div>
	
	</div><!-- END .footer -->

</div><!-- END .footer-wrapper -->
<?php } ?>
<div class="footer-bottom-wrapper">
	
	<div class="footer-bottom">

		<div class="container">
	
			<div class="sixteen columns">

				<div class="copyright">
					<?php if (isset($_GET['app'])) { ?>
                    <p>Built by Rowan University's SAC 2017 <a href='?p=memsat'>Development</a> committee</p>
                    <?php } else { ?>
                    <p>Built by Rowan University's SAC 2017 <a href='?p=memsat'>Web, iOS, and the BEST MOBILE OPERATING SYSTEM</a> committee</p>
                    <?php } ?>
				</div>

			</div>

		</div>

		<div class="clear"></div>
		
	</div><!-- END .footer-bottom -->

</div><!-- END .footer-bottom-wrapper -->

<!-- JS -->
<script src="js/jquery-ui.min.js"></script>	
<script src="js/jquery.superfish.js"></script>
<script src="js/jquery.supersubs.js"></script>
<script src="js/jquery.dcarousel.js"></script>
<script src="js/jquery.fitvid.js"></script>
<script src="js/jquery.flexslider.js"></script>
<script src="js/jquery.isotope.min.js"></script>
<script src="js/jquery.prettyPhoto.js"></script>
<script src="js/jquery.vegas.js"></script>
<script src="js/scripts.min.js"></script>
<script src="js/footer.js.php"></script>

<script>

</script>
</body>
</html>
