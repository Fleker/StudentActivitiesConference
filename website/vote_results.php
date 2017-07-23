<?php

$DEBUG = false;

$values = json_decode($firebase->get(DEFAULT_PATH . "/attendees/"));

if (!isset($values)) {
    die("Cannot get attendees");   
}

$tshirt = array();
$project = array();

foreach ($values as $i) {
    // User has registered
    if (property_exists($i, 'vote_tshirt')) {
        if (isset($tshirt[$i->vote_tshirt])) {
            $tshirt[$i->vote_tshirt] = $tshirt[$i->vote_tshirt] + 1;   
        } else {
            $tshirt[$i->vote_tshirt] = 1;
        }
    }
    if (property_exists($i, 'vote_project')) {
        if (isset($project[$i->vote_project])) {
            $project[$i->vote_project] = $project[$i->vote_project] + 1;   
        } else {
            $project[$i->vote_project] = 1;
        }
    }
}

// Sort
ksort($tshirt);
ksort($project);
include 'firebase_include_js.php';    
?>
<script src="admin_restriction.js"></script>
<script>
    enableAuthenticationRequirement("vote_results", function() {});
</script>

<div class="content-wrapper clearfix restricted">	
    <div class="container">
        <div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>SAC Voting Stats</h1>
				<h2>Voting is currently <?php 
                    $votingEnabled = json_decode($firebase->get(PATH_TO_FLAGS . SETTING_ALLOW_VOTING));
                    if ($votingEnabled) {
                        echo "Open";
                    } else {
                        echo "Closed";
                    }
                ?>
                </h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <h1>T-Shirt Results</h1>
        <ul>
            <?php
            foreach ($tshirt as $key => $t) {
                $name = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$key/name"));
                $design = json_decode($firebase->get(DEFAULT_PATH . "/tshirts/$key/downloadUrl"));
                echo "<li>$name: $t<br><img src='$design' style='width:50px' /></li>";
            }
            ?>
        </ul>
        
        <h1>Project Results</h1>
        <ul>
            <?php
            foreach ($project as $key => $p) {
                $name = json_decode($firebase->get(DEFAULT_PATH . "/attendees/$key/name"));
                $projectTitle = json_decode($firebase->get(DEFAULT_PATH . "/projects/$key/title"));
                echo "<li>$name ($projectTitle): $p</li>";
            }
            ?>
        </ul>
    </div>
</div>
