<div class="slogan-wrapper clearfix">
	<div class="container">
		<div class="sixteen columns">
            <?php
                if(isset($_GET['msg'])) { ?>
                    <div class='message info'>
                    <p><?php echo $_GET['msg']; ?></p>
                </div>
            <?php }
            ?>
			<h1>
                2017 IEEE R2 Student Activities Conference<br />
                <span class="highlight">April 7<sup>th</sup> - 9<sup>th</sup></span> <br />
			</h1>
			<!--h1 id="countdown"></h1-->
            <?php if (!isset($_GET['app']) && time() < TIME_LATE_BIRD) { ?>
            <!-- Only show registration button on website (not app) and if registration is open -->
			<a href='?p=registration_buyer'>
			    <button class='button xlarge yellow'>Register Now</button>
			 </a>
            <?php } ?>

            <!-- Countdown timer: http://codepen.io/Saravanaa/pen/jgmrH -->

               <script>
                // set the date we're counting down to
                var target_date = new Date('April 7, 2017').getTime() + 1000 * 60 * 60 * 18;

                // variables for time units
                var days, hours, minutes, seconds;

                // get tag element
                var countdown = document.getElementById('countdown');

                // update the tag with id "countdown" every 1 second
                setInterval(function () {

                    // find the amount of "seconds" between now and target
                    var current_date = new Date().getTime();
                    var seconds_left = (target_date - current_date) / 1000;

                    // do some time calculations
                    days = parseInt(seconds_left / 86400);
                    seconds_left = seconds_left % 86400;

                    hours = parseInt(seconds_left / 3600);
                    seconds_left = seconds_left % 3600;

                    minutes = parseInt(seconds_left / 60);
                    seconds = parseInt(seconds_left % 60);


                    function pad(n) {
                        return (n < 10) ? ("0" + n) : n;
                    }

                    days = pad(days);
                    hours = pad(hours);
                    minutes = pad(minutes);
                    seconds = pad(seconds);

                    // format countdown string + set tag value
                    countdown.innerHTML = '<span>' + days +  'D ' + hours + 'H '
                    + minutes + 'M ' + seconds + 'S';

                }, 1000);
            </script>
        </div>
	</div>
</div><!-- END .slogan-wrapper -->

<div class="clear"></div>

<div class="content-wrapper homepage clearfix">
	<!-- Display the teaser trailer -->
	<center>
		<!--h1> Check Out the SAC 2017 Teaser Trailer! </h1-->
		<!--width="760" height="428"-->
		<iframe id="teaser-video" width="640" height="360" src="https://www.youtube.com/embed/B2ewAopDXtc?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
		<style>
			/* Default for mobile */
			#teaser-video{
				width: 75vw;
				height: 42vw; /* 75 * (640/360) = 42 */
			}

			/* Scale for desktop */
			@media only screen and (min-width: 768px){
				#teaser-video{
					width: 640px;
					height: 360px;
				}
			}
		</style>
	</center>
	<br>
	<br>

	<div class="slider-wrapper clearfix">

		<div class="flexslider-wrapper">

			<div id="slider" class="flexslider">
				<ul class="slides">
					<li><img src="photos/rowan_university.png" alt="photo" /></li>
					<li>
						<img src="photos/rowan_hall.png" alt="photo" />
						<div class="flex-caption">
							<p>Rowan Hall: The home of the 2017 Student Activites Conference competitons.</p>
						</div>
					</li>

                    <li><img src="photos/brown_bag.png" alt="photo" /></li>
                    <li><img src="photos/sumo.png" alt="photo" /></li>
				</ul>
			</div>

		</div><!-- END .flexslider-wrapper -->

	</div><!-- END .slider-wrapper -->

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<h1>Latest News/Updates.</h1>
			<h5>Check back here for the latest news/updates for the SAC 2017!</h5>
			<p>Thank you to all who are interested in attending SAC 2017. We will be hosting a variety of schools in IEEE Region 2.</p>
            <img src='images/Region-2-Map (1).png' width='100%' />
			<div class="divider"><span class="divider-line"></span><span class="divider-color"></span></div>

			<h1 title="about">About the SAC</h1>
			<p><b>SAC</b> stands for the Student Activities Conference, which is an annual conference for registered IEEE collegiate members.
			This page is for the 2017 Student Activities Conference of IEEE Region 2, which encompasses parts of Ohio, New Jersey, Pennsylvania, Delaware, Maryland, Washington DC, Virginia, West Virginia.
			The 2017 SAC will be hosted by Rowan University on <strong>April 7-9</strong>. <!--Please check our <a href="index.php?p=faq.php">FAQ</a> for any questions
			you may have and check out the <a href="index.php?p=schedule.php">schedule page</a> to see what events will be available!--></p>

		</div><!-- END .page-content -->
	</div><!-- END .container -->

</div><!-- END .content-wrapper -->
