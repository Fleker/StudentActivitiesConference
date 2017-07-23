<?php

$FORMAT_JSON = isset($_GET['json']);

require 'mail_config.php';

$DEBUG = false;

$values = json_decode($firebase->get(DEFAULT_PATH . "/attendees/"));

if (!isset($values)) {
    die("Cannot get attendees");   
}

$attendeeCount = 0;
$counselorCount = 0;
$attendeeTotal = 0;
$guestCount = 0;
$attendeePaid = 0;
$comp = array();
$inHotel = 0;
$noBanquet = 0;
$projectshowcase = 0;
$papercomp = 0;
$tcomp = 0;
$school = array();
$sex = array();
$tshirt = array();
$year = array();
$entree = array();

foreach ($values as $i) {
    if (isset($i->password) && property_exists($i, 'paid') && $i->paid == 'true') {
        // User has registered
        $attendeeCount++;
        $counselor = false;
        if (property_exists($i, 'counselor') && ($i->counselor === true || $i->counselor === "true")) {
            $counselorCount++;
            $counselor = true;
        }
        if (property_exists($i, 'guest_name') && strlen($i->guest_name) > 0) {
            $guestCount++;
        }
        if (property_exists($i, 'competition')) {
            if (isset($comp[$i->competition])) {
                $comp[$i->competition] = $comp[$i->competition] + 1;   
            } else {
                $comp[$i->competition] = 1;
            }
        }
        if (property_exists($i, 'sex')) {
            if (isset($sex[$i->sex])) {
                $sex[$i->sex] = $sex[$i->sex] + 1;   
            } else {
                $sex[$i->sex] = 1;
            }
        }
        if (isset($i->hotel)) {
            $inHotel = $inHotel + ($i->hotel == "true") ? 1 : 0;
        }
        if (isset($i->hotel_opt_out)) {
            $inHotel = $inHotel + ($i->hotel_opt_out == "true") ? 1 : 0;
        }
        if (isset($i->banquet_opt_out)) {
            $noBanquet = $noBanquet + ($i->banquet_opt_out == "true") ? 1 : 0;
            // If opted out, don't count food
            if ($i->banquet_opt_out != "true") {
                if (isset($i->banquet_entree) && isset($entree[$i->banquet_entree])) {
                    $entree[$i->banquet_entree] = $entree[$i->banquet_entree] + 1;
                } else if (isset($i->banquet_entree)) {
                    $entree[$i->banquet_entree] = 1;   
                }
                if (property_exists($i, 'guest_name') && strlen($i->guest_name) > 0) {
                    if (isset($i->guest_banquet_entree) && isset($entree[$i->guest_banquet_entree])) {
                        $entree[$i->guest_banquet_entree] = $entree[$i->guest_banquet_entree] + 1;
                    } else if (isset($i->guest_banquet_entree)) {
                        $entree[$i->guest_banquet_entree] = 1;
                    }
                }
            }
        }
        if (property_exists($i, 'project_showcase') && ($i->project_showcase === true || $i->project_showcase === "true")) {
            $projectshowcase++;   
        }
        if (property_exists($i, 'paper_competition') && ($i->paper_competition === true || $i->paper_competition === "true")) {
            $papercomp++;   
        }
        if (property_exists($i, 'tshirt_competition') && ($i->tshirt_competition === true || $i->tshirt_competition === "true")) {
            $tcomp++;   
        }
        if (isset($tshirt[$i->tshirt])) {
            $tshirt[$i->tshirt] = $tshirt[$i->tshirt] + 1;   
        } else {
            $tshirt[$i->tshirt] = 1;
        }   
        if (property_exists($i, 'year')) {
            if (isset($year[$i->year])) {
                $year[$i->year] = $year[$i->year] + 1;   
            } else {
                $year[$i->year] = 1;
            }
        }
    }
    
    $attendeeTotal++;
    if (property_exists($i, 'paid') && $i->paid == 'true') {
        $attendeePaid++;
        // Count schools of students as long as they paid, regardless whether they have registered
        if (property_exists($i, 'school')) {
            $scName = strtolower(trim($i->school));
            if (isset($school[$scName])) {
                $school[$scName] = $school[$scName] + 1;
            } else {
                $school[$scName] = 1;
            }
        }
    }
}

if ($FORMAT_JSON) {
    $output = array(
        "attendeeTotal" => $attendeeTotal,
        "attendeeCount" => $attendeeCount,
        "attendeePaid" => $attendeePaid,
        "noHotel" => ($attendeeCount - $inHotel),
        "noBanquet" => ($attendeeCount - $noBanquet),
        "competition" => $comp,
        "projectshowcase" => $projectshowcase,
        "tshirtcomp" => $tcomp,
        "papercomp" => $papercomp,
        "school" => $school,
        "year" => $year,
        "tshirt" => $tshirt,
        "sex" => $sex,
        "entree" => $entree
        );
    echo json_encode($output);
    die();
} else { ?>
    <?php
        include 'firebase_include_js.php';    
    ?>
    <script src="admin_restriction.js"></script>
    <script>
        enableAuthenticationRequirement("registration_stats", function() {});
    </script>

<div class="content-wrapper clearfix restricted">	
    <div class="container">
        <div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>SAC Registration Stats</h1>
				<h2>As of right now.</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <h1>General Stats</h1>
        <ul>
            <li><?php echo $attendeeTotal;?> total tickets have been purchased.</li>
            <li><?php echo $attendeeCount;?> have completed registration (<?php echo $attendeePaid - $attendeeCount;?> have not but have paid).</li>
            <li><?php echo $attendeePaid;?> have paid (<?php echo $attendeeTotal - $attendeePaid;?> have not).</li>
            <li><?php echo $counselorCount;?> are counselors. (<?php echo $attendeeTotal - $counselorCount;?> are not.)</li>
            <li><?php echo ($attendeeCount - $inHotel);?> will be staying in the Marriot (<?php echo $inHotel;?> are not.)</li>
            <li><?php echo ($attendeeCount - $noBanquet);?> will be attending the Banquet. (<?php echo $noBanquet;?> are not.)</li>
            <li><?php echo ($guestCount);?> guests will be attending the Banquet.</li>
            <li><?php echo $sex['female']." women and ".$sex['male']." men." ?></li>
        </ul>
        
        <h1>Competition Stats</h1>
        <ul>
            <?php 
                foreach ($comp as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
            ?>
        </ul><br>
        <ul>
            <li><strong>Project Showcase: </strong><?php echo $projectshowcase; ?></li>
            <li><strong>T-Shirt Comp: </strong><?php echo $tcomp; ?></li>
            <li><strong>Paper Comp: </strong><?php echo $papercomp; ?></li>
        </ul>
        
        <h1>Academic Stats</h1>
        <h2>School</h2>
        <ul>
            <?php
                //natsort($school); // Sort by number registered
                ksort($school);   // Sort by school name
                foreach ($school as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
            ?>
        </ul>
        <h2>Academic Year</h2>
        <ul>
            <?php 
                foreach ($year as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
            ?>
        </ul>
        
        <h1>T-Shirt Size</h1>
        <ul>
            <?php 
                foreach ($tshirt as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
            ?>
        </ul>
        
        <h1>Banquet Information</h1>
        <h2>Entree</h2>
        <ul>
            <?php 
                foreach ($entree as $key => $value) {
                    echo "<li><strong>$key</strong>: $value</li>";
                }
            ?>
        </ul>
        
        <h1>Ticket Info</h1>
        <?php echo "Ticket Price: ".TICKET_PRICE."<br>
                    Payment Title: ".PAYMENT_TITLE."<br>
                    Start: ".PAYMENT_START_DISP."<br>
                    End: ".PAYMENT_END_DISP;
        ?>
    </div>
</div>

<?php
}
?>
