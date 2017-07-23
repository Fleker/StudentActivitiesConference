<?php
header("Content-Type: application/javascript");
include '../firebase_include.php';
?>
firebase.auth().onAuthStateChanged(function(user) {
    if (user) {
        $.get('admin_query.php', {user: user.uid}, function(data) {
            var response = JSON.parse(data);

            if(response.admin) {
                $('.admin_item').show();
                $('.admin_menu').show(100);
            }

            $(".mobile_menu").remove();
            var $responsive_nav = jQuery("<select />");
            $responsive_nav.addClass("mobile_menu");
            $("<option />", {"selected": "selected", "value": "", "text": "Select a page"}).appendTo($responsive_nav);
            $responsive_nav.appendTo(".navigation-wrapper");

            jQuery(".navigation-wrapper ul li a").each(function(){
                var nav_url = jQuery(this).attr("href");
                var nav_text = jQuery(this).text();
                if (jQuery(this).parents("li").length == 2) { nav_text = '- ' + nav_text; }
                if (jQuery(this).parents("li").length == 3) { nav_text = "-- " + nav_text; }
                if (jQuery(this).parents("li").length > 3) { nav_text = "--- " + nav_text; }
                jQuery("<option />", {"value": nav_url, "text": nav_text}).appendTo($responsive_nav)
            });

            field_id = ".navigation-wrapper select";
            jQuery(field_id).change(function()
            {
               value = jQuery(this).attr('value');
               window.location = value;

            });


        });
        var username = user.displayName != undefined ? user.displayName.split(' ')[0] : "Attendee";
        $('#userauth').html('Welcome ' + username + '.&emsp;<a title="Edit Account" href="?p=registration_logout">Logout</a>&emsp;<a class="icon-pencil" href="?p=registration_attendee&attendee=' + user.uid + '"></a>');
        if (user.displayName == undefined) {
            // Need to set user name
            console.warn("Auth name not defined");
//            console.info(user);
            firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/" + user.uid).once('value').then(function(snapshot) {
                var userdata = snapshot.val();
                if (userdata != undefined && userdata.name != undefined) {
                    // Update name
                    user.updateProfile({
                      displayName: userdata.name
                    }).then(function() {
                      // Update successful.
                        console.log("User name updated to " + userdata.name);
                    }, function(error) {
                      // An error happened.
                        console.warn(error);
                    });
                }
            });
        }
        firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/" + user.uid).once('value').then(function(snapshot) {
            var userdata = snapshot.val();
//            console.log(userdata);
            if (userdata != null && (userdata.paper_competition || userdata.paper_competition == "true")) {
                $('#pico_conf_mngr').show();
            }
            if (userdata != null && (userdata.tshirt_competition || userdata.tshirt_competition == "true")) {
                $('#tshirt_mngr').show();
            }
            if (userdata != null && (userdata.project_showcase || userdata.project_showcase == "true")) {
                $('#project_mngr').show();
            }
        });
    } else {
        $('#userauth').html('<a href="?p=registration_signin&from=<?php if (isset($_GET['p'])) { echo $_GET['p']; } else { echo "home"; } ?>">Login</a>');
        $(".admin_menu").remove();
//        console.log("removing admin menu");
    }
});

// Print an easter egg
console.log("You think you have what it takes? Try piloting the MemSat: http://sac17.rowanieee.org/memsat");
