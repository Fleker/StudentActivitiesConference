<?php

include 'firebase_include_js.php';

?>

<script src="admin_restriction.js"></script>
<script>
    enableAuthenticationRequirement("admin_export", function() {});
</script>

<div class="content-wrapper clearfix restricted">
    <div class="container">
        <div class="sixteen columns">
			<div class="page-title clearfix">
                <h1>Firebase <em>Realtime</em> Database</h1>
				<h2>You can manually back up data.<br>
                    Or you can set up the webpage to periodically download.</h2>
			</div>
		</div>

		<div class="clear"></div>

        Backup database hourly (Client must continue to be active)
        <input type="checkbox" id='schedule' onclick="toggleScheduler()" /><br>
		<button id='export' onclick='exportToJson()'>Export Attendee List</button>
        <br><br><br><br>
        <h3>View all data</h3>
        <code id='text_output'></code>


        <script>            
            function toggleScheduler() {
                if ($('#schedule').is(':checked')) {
                    setTimeout(exportToJson, 1000 * 60 * 60);      
                }
            }
            
            function exportToJson() {
				 // For time-stamping files
                var date = new Date();
                // Form a timestamped filename
				var filename = "attendees-" + (1900+date.getYear()) + "-" + (date.getMonth()+1) + "-" + date.getDate() + "_" + date.getHours() + ":" + date.getMinutes() + ".json"; 
				var jsonFile = firebaseData;
                
				var blob = new Blob([jsonFile], { type: 'text/json;charset=utf-8;' });
				if (navigator.msSaveBlob) { // IE 10+
					navigator.msSaveBlob(blob, filename);
				} else {
					var link = document.createElement("a");
					if (link.download !== undefined) { // feature detection
						// Browsers that support HTML5 download attribute
						var url = URL.createObjectURL(blob);
						link.setAttribute("href", url);
						link.setAttribute("download", filename);
						link.style.visibility = 'hidden';
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
					}
				}
                toggleScheduler();
			}
            
            var firebaseData;
            firebase.database().ref().on('value', function(snapshot) { 
                // Should update info each time it changes.
                console.log(snapshot.val());
                firebaseData = JSON.stringify(snapshot.val());
                document.getElementById('text_output').innerHTML = firebaseData;
            });
        </script>
        
        <style>
            #text_output {
                background-color: #efefef;
                padding: 8px;
                margin-top: 16px;
                font-family: monospace;
                display: block;
                overflow-x: auto;
            }
        </style>
    </div>
</div>
