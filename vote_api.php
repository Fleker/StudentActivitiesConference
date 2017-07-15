<?php
/**
 * This API will allow the app and website to query valid items for voting.
 *
 * The client must simply make an API call to `vote_api.php` to get a list of eligible entries.
 * In the response, one should check the `status.code` value. If it is 200, then you are fine. If
 * it is 500, an error occurred.
 * 
 * The list of items are in `data.tshirt` and `data.project`. Each returns an array of objects
 * with particular properties special to each category. Each item does include the creator
 * `.user` attribute, which is the user's uid.
 * This array is returned in random order, to prevent order-based biases.
 */

include 'firebase_include.php';

$result = array(
    "data" => array(),
    "status" => array(
            "code" => 500,
            "message" => "Unknown error"
        )
);
$shirts = json_decode($firebase->get(DEFAULT_PATH . '/tshirts/'), true);
$shirtArray = array();
foreach ($shirts as $key => $value) {
    $value['user'] = $key;
    array_push($shirtArray, $value);   
}
shuffle($shirtArray);

$projects = json_decode($firebase->get(DEFAULT_PATH . '/projects/'), true);
$projectArray = array();
foreach ($projects as $key => $value) {
    $value['user'] = $key;
    array_push($projectArray, $value);   
}
shuffle($projectArray);

$result['data']['tshirt'] = $shirtArray;
$result['data']['project'] = $projectArray;
$result['status']['code'] = 200;
$result['status']['message'] = 'OK';
echo json_encode($result);
?>