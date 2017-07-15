<?php

/*
 * HERE IS A FILE WHERE YOU CAN PUT CONFIGURATION DETAILS ABOUT A CONFERENCE OR EVENT
 */

/* THE CALENDAR ID OBTAINED FROM GOOGLE CALENDAR
 * Make sure the calendar is public, making it easy to access from any device without authorization.
 */
const calendarId = "students.rowan.edu_9kjulo3v9pdra879p2lhsrpkbk@group.calendar.google.com";

/* GET AN API KEY AT http://console.developers.google.com */
const API_KEY = "AIzaSyBBI39G66Qcar5kYTF7BJex0H5qgDTgo9s";

/* THE BEGINNING OF THE EVENT - FORMATTED AS AN RFC339 TIMESTAMP WITH MANDATORY TIMEZONE OFFSET */
const timeMin = "2017-04-07T10:00:00Z";
/* THE ENDING OF THE EVENT - FORMATTED AS AN RFC339 TIMESTAMP WITH MANDATORY TIMEZONE OFFSET */
const timeMax = "2017-04-09T10:00:00Z";

/*
 * RFC339 -- YEAR-MONTH-DAY followed by T and then the time with respect to timezones, then a Z.
 */

function generateAPIUrl() {
    return "https://www.googleapis.com/calendar/v3/calendars/". calendarId ."/events?singleEvents=true&timeMax=". timeMax ."&timeMin=". timeMin ."&key=". API_KEY;
}

function processGoogleCalendar() {
    $data = json_decode(file_get_contents(generateAPIUrl()));
    $events = $data->items;
    $output = array();
    foreach ($events as $key => $value) {
        $outputevent = array(
            // Parse out hour and minute
            "start" => substr($value->start->dateTime, 11, 5),
            "end" => substr($value->end->dateTime, 11, 5),
            "name" => $value->summary,
            /* Get actual date */
            "day" => intval(substr($value->start->dateTime, 8, 2)),
            );
        if (isset($value->location)) {
            $outputevent['location'] = $value->location;
        }
        if (isset($value->description)) {
            $jsondata = json_decode($value->description);
            if (property_exists($jsondata, 'room')) {
                $outputevent['room'] = $jsondata->room;
            }
            if (property_exists($jsondata, 'floor')) {
                $outputevent['floor'] = $jsondata->floor;
            }
        }
        array_push($output, $outputevent);
    }
    return $output;
}
?>
