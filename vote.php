<?php
$votingEnabled = json_decode($firebase->get(PATH_TO_FLAGS . SETTING_ALLOW_VOTING));

$votingEnabled = true;

?>

<div class="content-wrapper clearfix">
<?php if ($votingEnabled) { ?>
	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Vote for your favorite!</h1>
                Select your choice for best project and best t-shirt.
			</div>
		</div>

		<div class="clear"></div>
        
        <div id='thanks' class="page-title">
            <div class="message success">
                <div class="icon">
                    <h3 style='color:white'>Thanks for voting!</h3><br>
                    <p></p>
                </div>
            </div>
        </div>
        
        <div id='voting_allowed'>
            <h3>T-Shirts</h3>
            <ul id='tshirt'></ul>
        
            <h3>Projects</h3>
            <ul id='projects'></ul>
            
            <strong>Vote for:</strong>
            <div id='vote_for_tshirt'></div>
            <div id='vote_for_project'></div>
            <button id='dovote' onclick='dovote()'>Cast Vote</button>
        </div>
        <div id='voting_not_allowed'>
            <strong>You are not logged in right now, so you cannot vote.</strong>
        </div>
    </div>
    
    <script>
        firebase.auth().onAuthStateChanged(function(user) {   
            if (user) {
                $('#voting_allowed').show();
                $('#voting_not_allowed').hide();
                loadItems();
            } else {
                $('#voting_not_allowed').show();
                $('#voting_allowed').hide();
            }
        });
        
        function loadItems() {
            $.get('vote_api.php', {}, function(d) {
                var data = JSON.parse(d);
                for (i in data.data.tshirt) {
                    var project = data.data.tshirt[i];
                    var output = '<li onclick="selectTshirt(\'' + project.user + '\', \'' + project.downloadUrl + '\');" class="tshirt"><img src="' + project.downloadUrl + '" />';
                    output += '</li>';
                    console.log(output);
                    $('#tshirt').append(output);
                }
                for (i in data.data.project) {
                    var project = data.data.project[i];
                    var output = '<li onclick="selectProject(\'' + project.user + '\', \'' + project.title + '\');" class="project"><ul><span class="title">' + project.title + '</span>';
                    if (project.downloadUrl) {
                        output += '<span class="image"><img src="' + project.downloadUrl + '" /></span>';
                    }
                    output += '<span class="abstract">' + project.abstract + '</span>';
                    output += '</ul></li>';
                    console.log(output);
                    $('#projects').append(output);
                }
            })
        }
        
        tshirt_vote_id  = undefined;
        project_vote_id = undefined;
        function selectTshirt(uid, url) {
            tshirt_vote_id = uid;
            $('#vote_for_tshirt').html('<img src="' + url + '" />')
        }
        
        function selectProject(uid, title) {
            project_vote_id = uid;
            $('#vote_for_project').html(title);
        }
        
        function dovote() {
            if (firebae.auth().curentUser.uid == undefined) {
                return;
            } 
            updates = {};
            if (tshirt_vote_id != undefined) {
                updates["<?php echo DEFAULT_PATH; ?>/attendees/" + firebase.auth().currentUser.uid + "/vote_tshirt"] = tshirt_vote_id;
            }
            if (project_vote_id != undefined) {
                updates["<?php echo DEFAULT_PATH; ?>/attendees/" + firebase.auth().currentUser.uid + "/vote_project"] = project_vote_id;
            }
            firebase.database().ref().update(updates);
            
            $('#vote_for_tshirt').html('');
            $('#vote_for_project').html('');
            $('#thanks').show(100);
            window.location.href = '#thanks';
        }
    </script>
    
    <style>
        #voting_allowed, #voting_not_allowed, #thanks {
            display: none;
        }
        
        #vote_for_tshirt img {
            width: 200px;
        }
        
        #vote_for_project {
            font-weight: bold;
            font-size: 14pt;
        }
        
        .tshirt img {
            width: 400px;
            margin-top: 24px;
        }
        
        .project span {
            display: block;
        }
        
        .project {
            margin-bottom: 16px;
        }
        
        .project .title {
            font-weight: bold;
            font-size: 16pt;
            width: calc(100% - 40px);
            margin-bottom: 4px;
        }
        
        .project .abstract {
            font-size: 10pt;
            margin-left: 16px;
            max-width: 350px;
            line-height: 1.5em;
        }
        
        .project img {
            margin-left: 16px;
            width: 300px;
            margin-top: 10px;
        }
    </style>
    
<?php } else { ?>
    <div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Voting has closed!</h1>
                Thanks for your interest, but voting is no longer available.
			</div>
		</div>

		<div class="clear"></div>
	</div>
<?php } ?>
</div>