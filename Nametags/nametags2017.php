<?php

include('../firebase_include.php');
include('../firebase_include_js.php');
require('FPDF181/PDF_Label.php');

$_Competitions = array(
  'sumo_scratch' => 'Sumo Scratch',
  'sumo_kit' => 'Sumo Kit',
  'micromouse_scratch' => 'Micromouse Scratch',
  'micromouse_kit' => 'Micromouse Kit',
  'brownbag' => 'Brown Bag',
  'ethics' => 'Ethics',
  'physics' => 'Physics',
  'wie' => 'WIE Teach'
);

$pdf = new PDF_Label(array('paper-size'=>'letter', 'metric'=>'in', 'marginLeft'=>0.75, 'marginTop'=>1, 'NX'=>2, 'NY'=>4, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>3.5, 'height'=>2.25, 'font-size'=>18));
$pdf->AddPage();

$json_values = $firebase->get(DEFAULT_PATH . "/attendees/");
$values = json_decode($json_values, true);

foreach($values as $key => $newVals){
  if(array_key_exists('school', $newVals)){
    $tempArr[$key] = $newVals['school'];
  }
}

natsort($tempArr);
$finalVals = array();

foreach($tempArr as $key => $value){
  $finalVals[] = $values[$key];
}

foreach ($finalVals as $key => $user) {

  //Check if they paid
  if($user['paid'] !== "true" && $user['paid'] !== true){
    continue;
  }
  if(array_key_exists('vip', $user)){
    if($user['vip'] === true || strtolower($user['vip']) === "true"){
      continue;
    }
  }

  $competitions = array();

  $school = "";
  if(array_key_exists('school', $user)){
    $school = $user['school'];
  }

  //$main_competition = "";
  if(array_key_exists('competition', $user)){
    if($user['competition'] !== null && strtolower($user['competition']) !== "null" && $user['competition'] !== ""){
      //echo "<script>alert('".$user->competition."');</script>";

      //$main_competition = $user->competition;
      if(array_key_exists($user['competition'], $_Competitions)){
        //$main_competition = $_Competitions[strtolower($user->competition)];
        array_push($competitions, $_Competitions[strtolower($user['competition'])]);
      }
      else{
        //$main_competition = "I MESSED IT UP??";
      }
    }
  }

  //$secondary_competition = "";
  if(array_key_exists('paper_competition', $user)){
    if($user['paper_competition']){
      //$secondary_competition = "Paper Competition";
      array_push($competitions, "Paper Competition");
    }
  }
  if(array_key_exists('project_showcase', $user)){
    if($user['project_showcase']){
      //$secondary_competition = "Project Showcase";
      array_push($competitions, "Project Showcase");
    }
  }
  if(array_key_exists('tshirt_competition', $user)){
    if($user['tshirt_competition']){
      //$secondary_competition = "Tshirt Competition";
      array_push($competitions, "T-shirt Competiton");
    }
  }

  /*$competition_name = "";
  if($main_competition != "" && $secondary_competition != ""){
    $competition_name = $main_competition." and ".$secondary_competition;
  }
  else if($main_competition != ""){
    $competition_name = $main_competition;
  }
  else if($secondary_competition != ""){
    $competition_name = $secondary_competition;
  }
  else if($user->counselor == "true"){
    $competition_name = "Counselor";
  }*/

  if(array_key_exists('counselor', $user)){
    if($user['counselor'] === "true"){
      array_push($competitions, "Counselor");
    }
  }

  $pdf->Add_Label($user['name'], $school, $competitions/*$competition_name*/, "ieee", "", "true", false);
}

// Generate page of empty tags (to be filled out by hand)
for($i = 0; $i < 10; $i++){
  $emptyCompetitions = array();
  array_push($emptyCompetitions, "");
  $pdf->Add_Label(" ", " ", " ", "ieee", "", "true", false);
}

//$name, $school, $dept, $class, $front
//$pdf->Add_Label("Brian", "Rowan", "ECE",  "Embedded", "false");

ob_start();
$pdf->Output();
ob_end_flush();

?>
