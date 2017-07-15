<?php
    header("Content-Type: application/json");
    include 'config.php';
    include 'config-google-calendar.php';
    include 'config-ical.php';
    if (CALENDAR_TYPE == TYPE_GOOGLE_CALENDAR) {
        echo json_encode(processGoogleCalendar());
    } else if (CALENDAR_TYPE == TYPE_ICAL) {
        echo json_encode(processICal());
    }
?>
