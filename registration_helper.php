<?php
// Helper functions for registration fields

/**
 * Creates a proper error dialog element in HTML and echoes it
 *
 * @param $formid The id for the input element. This appends "_error" to the end of 
 * it to indicate the attached field
 */
function placeErrorDialog($formid) {
    echo "<div class='message error' id='".$formid."_error' style='display:none'><div class='icon'><p></p></div></div>"; 
}
?>