<?php

require_once __DIR__ . "/firebase/firebaseLib.php";

const DEFAULT_KEY = 'sac17-9fc02';
const DEFAULT_URL = 'https://'.DEFAULT_KEY.'.firebaseio.com/';
//const DEFAULT_TOKEN = 'AIzaSyCrpLk6tIP_DkEpMuCi6oQ4cYu3Z8jBVWw';
const DEFAULT_TOKEN = '';
if (!isset($_GET['firebase_path'])) { // For debug purposes, allow the path to change
    define("DEFAULT_PATH", '/release/');
} else {
    define("DEFAULT_PATH", $_GET['firebase_path']);
}
$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

?>