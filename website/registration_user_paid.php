<?php
include 'config.php';
include 'firebase_include.php';
$needle = $_GET['email'];

$DEBUG = false;

$values = json_decode($firebase->get(DEFAULT_PATH . "/attendees/"));

$data = array();
$data['paid'] = -1; // No value at all
if (!isset($values)) {
    $data['error'] = 403;
    echo json_encode($data);
    die();
}

$school = array();

foreach ($values as $key => $i) {
    // User has registered
    if ($i->email == $needle) {
        $data['paid'] = $i->paid;
        $data['uuid'] = $key;
    }
}
$data['error'] = 200;

echo json_encode($data);

?>
