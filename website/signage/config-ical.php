<?php

/*
 * HERE YOU CAN ENTER THE URL OF ANY ICAL URL TO PARSE. THIS URL MUST BE PUBLIC.
 */

const iCalUrl = "https://calendar.google.com/calendar/ical/students.rowan.edu_9kjulo3v9pdra879p2lhsrpkbk%40group.calendar.google.com/public/basic.ics";

function processICal() {
    // This script will do everything we want mostly, but does not account for
    //   time zones.
    $data = file_get_contents(iCalUrl);
    $lines = explode("\n", $data); // Read line-by-line
    $inEvent = false;
    $export = array();
    $event = array();
    foreach ($lines as $line) {
        $line = trim($line);
        if (!$inEvent && $line == "BEGIN:VEVENT") {
            $inEvent = true;
            $event = array();
        } else if ($inEvent && substr($line, 0, 8) == "DTSTART:") {
            $event['start'] = substr($line, 17, 2) . ':' . substr($line, 19, 2);
            $event['day'] = intval(substr($line, 14, 2));
        } else if ($inEvent && substr($line, 0, 6) == "DTEND:") {
            $event['end'] = substr($line, 15, 2) . ':' . substr($line, 17, 2);
        } else if ($inEvent && substr($line, 0, 8) == "SUMMARY:") {
            $event['name'] = substr($line, 8);
        } else if ($inEvent && substr($line, 0, 9) == "LOCATION:") {
            $event['location'] = substr($line, 9);
        } else if ($inEvent && substr($line, 0, 12) == "DESCRIPTION:") {
            $json = json_decode(substr($line, 12));
            if (isset($json->room)) {
                $event['room'] = $json['room'];
            }
            if (isset($json->floor)) {
                $event['floor'] = $json['floor'];
            }
        } else if ($inEvent && $line == "END:VEVENT") {
            $inEvent = false;
            array_push($export, $event);
        }
    }
    return $export;
}
?>
