<?php
include 'config.php';
include 'firebase_include.php';
$needle = strtolower($_GET['school']);

$DEBUG = false;

$values = json_decode($firebase->get(DEFAULT_PATH . "/attendees/"));

$data = array();
if (!isset($values)) {
    $data['error'] = 403;
    $data['count'] = 0;
    echo json_encode($data);
    die();
}

$school = array();

foreach ($values as $i) {
    // User has registered
    if (property_exists($i, 'school')) {
        $scName = strtolower(trim($i->school));
        if (isset($school[$scName]) && $i->counselor === 'false' && $i->paid === 'true') {
            $school[$scName] = $school[$scName] + 1;
        } else if (!isset($school[$scName]) && property_exists($i, 'counselor') && $i->counselor === 'false' && $i->paid === 'true') {
            $scName = strtolower(trim($i->school));
            $school[$scName] = 1;
        }
    }
}
$data['data'] = $school;
$data['error'] = 200;
$data['school'] = $needle;
if (isset($school[$needle])) {
    $data['count'] = $school[$needle];
} else {
    $data['count'] = 0;   
}
echo json_encode($data);

?>
