var debug = false;
var events = [];
// TODO Do better error handling - throw everything in a try / catch

function hideAll() {
    try {
        document.getElementById('now').innerHTML = "<h2>No Events Found</h2><h4>Current Location: " + room.replace(/%20/g, " ") + "</h4>";
        document.getElementById('next').innerHTML = "";
    } catch(e) {
        console.error(e);
        document.getElementById('error').innerHTML = e.message;
    }
}

function getEventStart(event) {
    var eventStartH = event.start.substring(0, event.start.indexOf(':'));
    var eventStartM = event.start.substring(event.start.indexOf(':') + 1, event.start.length);
    var eventStart = new Date();
    eventStart.setHours(eventStartH);
    eventStart.setMinutes(eventStartM);
    // For debugging
    eventStart.setDate(date.getDate());
    return eventStart;
}

function getEventEnd(event) {
    var eventEndH = event.end.substring(0, event.end.indexOf(':'));
    var eventEndM = event.end.substring(event.end.indexOf(':') + 1, event.end.length);
    var eventEnd = new Date();
    eventEnd.setHours(eventEndH);
    eventEnd.setMinutes(eventEndM);
    // For debugging
    eventEnd.setDate(date.getDate());
    return eventEnd;
}

function enableDebug(boolean) {
    debug = boolean;
}

var date = new Date();

function displayTime() {
    // Using Date to find the current day and time
    if (!debug) {
        date = new Date();
    }
    console.log("Updating time");
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    // Display date and time
    console.log(date);
    document.getElementById('date_time').innerHTML = months[date.getMonth()] + " " + date.getDate() + "&emsp;" + date.getHours() + ":" + ((date.getMinutes() < 10) ? "0" + date.getMinutes() : date.getMinutes());
}

function redrawEvents() {
    if (roomEvents.length == 0) {
        // No events
        hideAll();
    }
    var day = date.getDate();
    var eventsNow = [];
    var eventsNext = [];
    for (var i = 0; i < roomEvents.length; i++) {
        var e = roomEvents[i];
        if (e.day != day) {
            continue;
        }
//            console.log(getEventStart(e), date, getEventEnd(e));
        // Transition time
        var TRANSITION_TIME = 0;
        if (getEventStart(e).getTime() - TRANSITION_TIME <= date.getTime() && date.getTime() < getEventEnd(e).getTime() - TRANSITION_TIME) {
            eventsNow.push(e);
//                console.log("Adding ",e);
        } else if (date.getTime() < getEventEnd(e).getTime() - TRANSITION_TIME) {
            eventsNext.push(e);
//                console.log("Pushing", date, e);
        }
    }
    console.log(eventsNext);
    // Display events
    var rowA = "<table><tr>";
    var rowB = "<tr>";
    var rowC = "<tr>";
    for (var i = 0; i < eventsNow.length; i++) {
        var e = eventsNow[i];
        // Event is happening now
//            console.log("Displaying ", e);
        rowA += "<td style='width: " + (1700 / eventsNow.length) + "px'><h2>" + e.name + "</h2></td>";
        rowB += "<td><h3>" + e.start + " &mdash; " + e.end + "</h3></td>";
        rowC += "<td><h4>" + e.location + "</h4></td>";
    }
    document.getElementById('now').innerHTML = rowA + "</tr><tr>" + rowB + "</tr><tr>" + rowC + "</tr></table>";

    var rowA = "<table><tr>";
    var rowB = "<tr>";
    var rowC = "<tr>";
    document.getElementById('next').innerHTML = "<h2>Next:</h2>";
    var nextCount = 0;
    for (var i = 0; i < Math.min(6, eventsNext.length); i++) {
        var n = eventsNext[i];
        if (n !== undefined) {
            // Check when event is
            var NEXT_EVENT_THRESHOLD = 1000 * 60 * 60 + 1000 * 60 * 20; // Next hour and twenty
            if (date.getTime() + NEXT_EVENT_THRESHOLD > getEventStart(n).getTime() || nextCount == 0) {
                rowA += "<td><h2>Next: " + n.name + "</h2></td>";
                rowB += "<td><h3>" + n.start + " - " + n.end + "</h3></td>";
                rowC += "<td><h4>" + n.location + "</h4></td>";
                document.getElementById('next').innerHTML = rowA + "</tr><tr>" + rowB + "</tr><tr>" + rowC + "</tr></table>";
                nextCount++;
            }
        }
    }
}

function parseEvents() {
    roomEvents = [];
    for (var i = 0; i < events.length; i++) {
        var e = events[i];
        console.log(e);
        // Floor can be set to whatever for defaults to show
        if (room === undefined && e.room === undefined && e.floor === undefined) {
            console.log("Add by default");
            // We'll add it by default
            roomEvents.push(e);
        } else if (room !== undefined && e.room !== undefined && e.room == room) {
            console.log("Room match");
            // Add to valid event list
            roomEvents.push(e);
        } else if (floor !== undefined && e.floor !== undefined && e.floor == floor) {
            console.log("Floor match");
            // Add to valid event list
            roomEvents.push(e);
        } else if ((e.room !== undefined && room === undefined) || (e.floor !== undefined && floor === undefined)) {
            console.log("Undefined match");
            roomEvents.push(e);
        } else {
            console.log("No match");
        }
    }
    redrawEvents();
}

function refreshEvents() {
    $.ajax({
        url: 'events.php',
        complete: function(data) {
        },
        success: function(data) {
            events = data;
            if (date.getTime() > end_of_event) {
                document.getElementById('now').style.display = 'none';   
                document.getElementById('next').style.display = 'none';   
                document.getElementById('rightnow').style.display = 'none';   
                document.getElementById('thanks').style.display = 'block';   
                document.getElementById('thanks').style.fontSize = '48pt';   
                document.getElementById('thanks').style.textAlign = 'center';   
                document.getElementById('thanks').style.marginLeft = '-140px';
            } else {
                try {
                    parseEvents();
                } catch (e) {
                    console.error(e);
                    document.getElementById('error').innerHTML = e.message;
                }
            }
        },
        error: function(err) {
            console.warn(err)
        }
    });
}

// Get the location-specific filter.
var loc = window.location.search.substr(1);
console.log(location);
// Allow values to cache, so we can use them in manifest.
var room = localStorage['room'];
var floor = localStorage['floor'];
if (loc.indexOf('floor') > -1) {
    // Floor-specific
    floor = loc.substr(6);
}
if (loc.indexOf('room') > -1) {
    // Or room-specific
    room = loc.substr(5);
}

var roomEvents = [];
document.getElementById('thanks').style.display = 'none';
if (room !== undefined || floor !== undefined) {
    displayTime();
    refreshEvents();
    setInterval(displayTime, 1000 * 5);
    setInterval(refreshEvents, 1000 * 60);
    setTimeout(function() {
        window.location.reload(); // Refresh page every 10 minutes.
    }, 1000 * 60 * 10)
} else {
    document.getElementById('error').innerHTML = "Please provide a floor or room parameter in the URL with /?room= or /?floor=<br><button onclick='localStorage[\"room\"] = prompt(\"Room?\")'>Set Room</button><button onclick='localStorage[\"floor\"] = prompt(\"Floor?\")'>Set Floor</button>";
}

document.getElementById('title').innerHTML = title;
document.getElementById('thanks_text').innerHTML = thanks_text;
document.getElementById('date_time').onclick = function() {
    // Clear our localStorage
    localStorage = undefined;
}
