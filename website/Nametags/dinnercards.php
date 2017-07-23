<?php

include('../firebase_include.php');
include('../firebase_include_js.php');
require('FPDF181/PDF_Label.php');

$_Entrees = array(
    'cordon_bleu' => 'Cordon Bleu',
    'baked_flounder' => 'Baked Flounder',
    'pasta' => 'Pasta'
);

$pdf = new PDF_Label(array('paper-size'=>'letter', 'metric'=>'in', 'marginLeft'=>0.435, 'marginTop'=>3.2, 'NX'=>2, 'NY'=>2, 'SpaceX'=>0.41, 'SpaceY'=>0.25, 'width'=>3.6, 'height'=>4.25, 'font-size'=>18));
$pdf->AddPage();

$json_values = $firebase->get(DEFAULT_PATH . "/attendees/");
$values = json_decode($json_values, true);
$tables = array();

// Sort each by table number
function tableSort($a, $b) {
    return intval($a['table']) - intval($b['table']);
}

foreach ($values as $key => $user) {
    if (!isset($user['table']) || strlen($user['table']) == 0 || $user['table'] == -1 || $user['table'] == "-1") {
        continue;
    }
    array_push($tables, $user);
}
usort($tables, 'tableSort');

foreach ($tables as $key => $user) {
    if (!isset($user['name'])) {
        continue;   
    }
    if (!isset($user['banquet_entree'])) {
        continue;   
    }
    if (isset($user['banquet_opt_out']) && $user['banquet_opt_out'] == true) {
        continue;
    }
    $name = $user['name'];
    $banquet = $_Entrees[$user['banquet_entree']];

    $pdf->Add_Table_Label($name, $banquet, ["Table ".$user["table"]], "ieee", "", "true", false);
    if (isset($user['guest_name']) && strlen($user['guest_name']) > 0) {
        $banquet = $_Entrees[$user['guest_banquet_entree']];
        $pdf->Add_Table_Label($user['guest_name'], $banquet, ["Table ".$user['table']], 'ieee', '', 'true', false);
    }
}

ob_start();
$pdf->Output();
ob_end_flush();

?>
