<?php
$exif = exif_read_data($_POST['img'], 'ANY_TAG', true);
//echo $exif===false ? "No header data found.<br />\n" : "Image contains headers<br />\n";

/*foreach ($exif as $key => $section) {
    foreach ($section as $name => $val) {
        echo "$key.$name: $val<br />\n";
    }
}*/
echo trim(substr($exif['EXIF']['UserComment'], 5)); // =ASCIICOMMENT
?>
