<?php
include 'firebase_include.php';

// --- storing an array ---
$test = array(
    "foo" => "bar",
    "i_love" => "lamp",
    "id" => 42
);
echo print_r($test)."<br>";
$dateTime = new DateTime();
echo DEFAULT_PATH . '/' . $dateTime->format('c')."<br>";
echo $firebase->set(DEFAULT_PATH . '/' . $dateTime->format('c'), $test)."<br>";
echo $firebase->push(DEFAULT_PATH . '/push', $test)."<br>";
echo "Well, we got to this point.<br>";
echo $firebase->get(DEFAULT_PATH . '/' . $dateTime->format('c'));
?>
