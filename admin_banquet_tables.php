<?php 
include 'firebase_include_js.php';
?>

<script src="admin_restriction.js"></script>
<script>
    enableAuthenticationRequirement("admin_banquet_tables", function() {});
</script>

<div class="content-wrapper clearfix">

	<div class="container">
		<div class="sixteen columns">
			<div class="page-title clearfix">
				<h1>Banquet Table Admin</h1>
				<h2>Set the tables for each attendee</h2>
			</div>
		</div>

		<div class="clear"></div>
        
        <input type='number' placeholder="Number of tables" id='table_count' oninput='redrawTableSum()' style='width:250px' />
        <input type='number' placeholder="Number of seats per table" id='table_seats' oninput='redrawTableSum()' style='width:250px' />
        
        <a href='Nametags/dinnercards.php' target='_blank'><button id='generate'>Dinner Cards PDF</button></a>
        
        <h3>Tables and counts</h3>
        <div id='table_sum'></div>
        
        <div id='loading'>Loading data...</div>
        
        <br><br><h3>Attendees</h3>
        <table>
            <thead>
                <tr><td>Name</td><td>School</td><td>Table</td></tr>
            </thead>
            <tbody id='attendees'>
            </tbody>
        </table>
        
        <br><br><h3>Table and Meals</h3>
        <table>
            <thead>
                <tr><td>Table</td><td>Meals</td></tr>
            </thead>
            <tbody id='table_meals'>
            </tbody>
        </table>

        <script>
            function schoolsort(a, b) {
                if (a['school'] == undefined) {
                    a['school'] = '';
                }
                return a['school'].localeCompare(b['school']);
            }
            
            function showGuests(id) {
                var output = '';
                var attendee_table = $('.attendee_table');
                for (var i = 0; i < attendee_table.length; i++) {
                    var table = attendee_table[i].value;
                    if (id != table) {
                        continue;
                    }
                    output += $('.attendee_display_name')[i].dataset['name'] + "\n";
                    if (attendee_table[i].dataset != undefined && attendee_table[i].dataset['guest'] != undefined && attendee_table[i].dataset['guest'] != 'undefined' && attendee_table[i].dataset['guest'] != '') {
                        // Need to account for a guest
                        output += '  With ' + attendee_table[i].dataset['guest'] + "\n";
                    }
                }
                alert(output);
            }

            function redrawTableSum() {
                // Get all of the tables.
                var attendee_table = $('.attendee_table');
                var entries = [];
                for (var i = 0; i < attendee_table.length; i++) {
                    var table = attendee_table[i].value;
                    if (entries[table] == undefined) {
                        entries[table] = 0;   
                    }
                    entries[table]++;
//                    console.log(attendee_table[i].dataset);
                    if (attendee_table[i].dataset != undefined && attendee_table[i].dataset['guest'] != undefined && attendee_table[i].dataset['guest'] != 'undefined' && attendee_table[i].dataset['guest'] != '') {
                        // Need to account for a guest
                        entries[table]++;   
                    }
                }
                $('#table_sum').html('');
                var table_count = parseInt($('#table_count').val());
                var seats_per = parseInt($('#table_seats').val());
                for (i in entries) {
                    var entry = entries[i];
                    var tableInt = parseInt(i);
                    var color = (tableInt > table_count || entry > seats_per) ? 'red' : 'black';
//                    console.log(tableInt, entry, table_count, seats_per, tableInt > table_count, entry > seats_per);
                    $('#table_sum').append("<span style='color: " + color + "; pointer: cursor;' onclick=\"showGuests(" + tableInt + ")\">" + tableInt + " (" + entry + ")</span>&emsp;");
                }
            }
            
            function changeTable(key) {
                if (key == undefined || key.length == 0) {
                    console.warn('Woah, there is no key');
                    return;
                }
                // Get the table input
                var table = $('#' + key + ' input').val();
                if (table != $('#' + key + ' input').attr('data-original')) {
                    firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/" + key + "/table/").set(table);
                    redrawTableSum();
                }
            }
            
            function row(attendee) {
                var output = "<td";
                if (attendee['name'] != undefined) {
                    output += ' class="attendee_display_name" data-name="' + attendee['name'] + '">' + attendee['name'];
                    if (attendee['guest_name'] != undefined && attendee['guest_name'] != '') {
                        output += "<br><small>And " + attendee['guest_name'] + "</small>";   
                    }
                    if (attendee['counselor'] == true || attendee['counselor'] == 'true') {
                        output += "<br><small>Counselor</small>";   
                    }
                    if (attendee['vip'] == true || attendee['vip'] == 'true') {
                        output += "<br><small>VIP</small>";   
                    }
                    output += "<br><small>" + attendee['key'] + "</small>";
                } else {
                    return '';
                    output += "Null<br><br>" + attendee['key'];
                }
                output += "</td><td>";
                if (attendee['school'] != undefined) {
                    output += attendee['school'];
                } else {
                    output += "Null";   
                }
                output += "</td><td><input data-original='" + ((attendee['table'] != undefined) ? parseInt(attendee['table']) : -1) + "' tabindex='" + tabindex + "' class='attendee_table' data-guest='" + attendee['guest_name'] + "' onblur='changeTable(\"" + attendee['key'] + "\")' value='" + ((attendee['table'] != undefined) ? parseInt(attendee['table']) : -1) + "' type='number' /></td>";
                return output;
            }
            
            function outputTableOfTableAndMeals(attendees) {
                // NOTE: These labels turn our backend keys into nice text
                var labels = {
                    'cordon_bleu': "Cordon Bleu",
                    'pasta': "Pasta",
                    'baked_flounder': "Baked Flounder"
                }
                var tableObj = []
                for (i in attendees) {
                    var attendee = attendees[i];
                    if (attendee.table && attendee.table > -1) {
                        if (!tableObj[attendee.table]) {
                            tableObj[attendee.table] = {};
                        }
                        if (!tableObj[attendee.table][attendee.banquet_entree]) {
                            tableObj[attendee.table][attendee.banquet_entree] = 0;
                        }
                        console.log(attendee.banquet_entree, attendee.guest_banquet_entree);
                        tableObj[attendee.table][attendee.banquet_entree]++;
                        if (attendee.guest_banquet_entree && attendee.guest_name && attendee.guest_name.length > 0) {
                            if (!tableObj[attendee.table][attendee.guest_banquet_entree]) {
                                tableObj[attendee.table][attendee.guest_banquet_entree] = 0;
                            }
                            tableObj[attendee.table][attendee.guest_banquet_entree]++;
                        }
                    }
                }
                console.log(tableObj);
                var output = "";
                for (i in tableObj) {
                    output += "<tr><td>Table " + i + "</td><td>";
                    for (j in tableObj[i]) {
                        output += labels[j] + ": " + tableObj[i][j] + ", ";
                    }
                    output += "</td></tr>";
                }
                $('#table_meals').html(output);
            }

            tabindex = 0;
            firebase.database().ref("<?php echo DEFAULT_PATH; ?>/attendees/").on('value', function(snapshot) {
                // Load all attendees - make this collaborative.
                var s = snapshot.val();
                var attendee_array = [];
                for (i in s) {
                    var attendee = s[i];
                    if (attendee['paid'] == false) {
                        if ((attendee['counselor'] != true && attendee['counselor'] != 'true') || (attendee['vip'] != true && attendee['vip'] != 'true')) {
                            console.warn("User is not a counselor or vip");
                            continue;
                        }
                    }
                    attendee['key'] = i;
                    attendee_array.push(attendee);
                }
                // Recreate the table
                // Sort array
                attendee_array.sort(schoolsort);
                console.log("Found " + attendee_array.length + " attendees");
                for (i in attendee_array) {
                    var attendee = attendee_array[i];
                    if ($('#' + attendee['key']).length != 0) {
                        $('#' + attendee['key']).html(row(attendee));
                    } else {
                        $('#attendees').append("<tr id='" + attendee.key + "'>" + row(attendee) + "</tr>");
                        tabindex++;
                    }
                }
                redrawTableSum();

                outputTableOfTableAndMeals(attendee_array);
                $('#loading').hide(100);
            });
        </script>
        
        <style>
            .attendee_table {
                width: 50px;
            }
        </style>

	</div><!-- END .container -->

</div><!-- END .homepage-content-wrapper -->
