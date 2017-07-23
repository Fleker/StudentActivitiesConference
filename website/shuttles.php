<?php
    const SHUTTLES_URL = 'https://feeds.transloc.com/3/vehicle_statuses?agencies=653';
?>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>SAC 2017 Shuttles</h1>
                <h2>Positions are updated live. Tap each for details.</h2>
			</div>
		</div>

		<div class="clear"></div>
		<div class="page-content with-sidebar">
			<div class ="sixteen columns">
                <div id='map'></div>
            </div>
		</div><!-- END .page-content -->

	</div><!-- END .container -->

</div><!-- END .content-wrapper -->
<!--    <script src='http://maps.googleapis.com/maps/api/js&key=AIzaSyCrpLk6tIP_DkEpMuCi6oQ4cYu3Z8jBVWw&callback=initMap'></script>-->
    <script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyCrpLk6tIP_DkEpMuCi6oQ4cYu3Z8jBVWw'></script>
    <script src='js/gmaps.min.js' type='text/javascript'></script>
    <script>
        function initMap() {
            console.info("Map initalized");
        }
        
        function reloadShuttles() {
            var url = "<?php echo SHUTTLES_URL; ?>";
            var cache = [];
            $.get(url, {}, function(res) {
                if (res.success) {
                    // Clear all
                    map.removeMarkers();
                    for (var i = 0; i < res.vehicles.length; i++) {
                        map.addMarker({
                            lat: res.vehicles[i].position[0],
                            lng: res.vehicles[i].position[1],
                            title: 'Lima',
                            infoWindow: {
                                content: "<div>Bus " + res.vehicles[i].call_name + "<br>Heading " + getBearing(res.vehicles[i].heading) + " at " + res.vehicles[i].speed + " mph</div>"
                            }
                        })
                    }
                }   
            });
        }
        
        function getBearing(degrees) {
            if (degrees < 22.5) {
                return "North";
            } else if (degrees < 67.5) {
                return "Northeast";
            } else if (degrees < 112.5) {
                return "East";
            } else if (degrees < 157.5) {
                return "Southeast";
            } else if (degrees < 202.5) {
                return "South";
            } else if (degrees < 247.5) {
                return "Southwest";
            } else if (degrees < 292.5) {
                return "West";
            } else if (degrees < 337.5) {
                return "Northwest";
            } else {
                return "North";
            }
        }
        
        map = new GMaps({
            div: '#map',
            el: "#map",
            lat: 39.7050304,
            lng: -75.11367199,
            zoom: 14
        });
        setInterval(function() {
            reloadShuttles();
        }, 5000);
        reloadShuttles();
    </script>
    <style>
        #map {
            width: 100%;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            height: 400px !important;
        }
        .page-content, .page-content > .sixteen {
            width: calc(100% - 12px) !important;
        }
    </style>
