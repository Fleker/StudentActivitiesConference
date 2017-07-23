<?php
require 'firebase_include.php';

const WHITELIST = "admin_whitelist.txt";

function add($firebase, $uuid) {
    $firebase->set(DEFAULT_PATH . "/admins/$uuid/", true);  
    echo "Add ".$uuid;
}

function delete($firebase, $uuid) {
    $firebase->delete(DEFAULT_PATH . "/admins/$uuid/");  
    echo "Delete ".$uuid;
}

function searchFor($firebase, $uuid) {
    $value = $firebase->get(DEFAULT_PATH . "/admins/$uuid/");
    return isset($value) && $value != null && $value != "null";
}

if (isset($_GET['list'])) {
    $whitelist = array();
    if ($file = fopen(WHITELIST, "r")) {
        while(!feof($file)) {
            $line = fgets($file);  
            array_push($whitelist, $line);
        }
        fclose($file);
    }
    echo json_encode($whitelist);
} else if (isset($_GET['user'])) {
    $response = array( "admin" => searchFor($firebase, $_GET['user']));
    echo json_encode($response);
} else if (isset($_POST['uuid']) && isset($_POST['delete'])) {
    delete($firebase, $_POST['uuid']);
} else if (isset($_POST['uuid']) && isset($_POST['put'])) {
    add($firebase, $_POST['uuid']);
} else if (isset($_POST['uuid']) && isset($_POST['toggle'])) {
    $response = searchFor($firebase, $_POST['uuid']);
    echo $response;
    if ($response) {
        // Delete   
        delete($firebase, $_POST['uuid']);
    } else {
        // Add 
        add($firebase, $_POST['uuid']);
    }
}
exit();
?>