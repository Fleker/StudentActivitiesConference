<?php

require('FPDF/PDF_Label.php');

//require('../pages/functions.php');
//connectToMySQL();
/*------------------------------------------------
To create the object, 2 possibilities:
either pass a custom format via an array
or use a built-in AVERY name
------------------------------------------------*/

// Example of custom format
// $pdf = new PDF_Label(array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>99, 'height'=>38, 'font-size'=>14));

// Example of custom format
 $pdf = new PDF_Label(array('paper-size'=>'letter-land', 'metric'=>'in', 'marginLeft'=>0.58, 'marginTop'=>0.69, 'NX'=>1, 'NY'=>1, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>4, 'height'=>3, 'font-size'=>18));



// Standard format
//$pdf = new PDF_Label('L7163');

$pdf->AddPage();
// Print labels
//echo "Q"; 
    $a = array("RAS On-The-Spot (1st Place)", "RAS On-The-Spot (1st Place)"
               
               );
    $b = array(  "Jordan Frank", "Jordan Blake"
               
               );
    
           /*
           
           "Brown Bag (1st Place)" => "Heather Bell",
           "Brown Bag (1st Place)" => "Christopher Creager",
           "Brown Bag (1st Place)" => "Thomas Savageau",
            
           "Brown Bag (2nd Place)" => "Richard Johnson",
           "Brown Bag (2nd Place)" => "Mark Keenan",
           "Brown Bag (2nd Place)" => "John Supel",
            
           "Brown Bag (3rd Place)" => "Evan Klingensmith",
           "Brown Bag (3rd Place)" => "Thomas Kucinsky",
           "Brown Bag (3rd Place)" => "David Scherer",
           "Brown Bag (3rd Place)" => "Matthew Wolfe",
           "Brown Bag (3rd Place)" => "Taif Choudhury",
           "Brown Bag (3rd Place)" => "Isuru Daulagala",
           "Brown Bag (3rd Place)" => "Nicholas Ross",
            
           "RAS Pre-Built (1st Place)" => "Tony Liang",
           "RAS Pre-Built (1st Place)" => "Michael Sangillo",
           "RAS Pre-Built (2nd Place)" => "Jacob Antoun",
           "RAS Pre-Built (2nd Place)" => "Zachary French",
           "RAS Pre-Built (2nd Place)" => "William Kopaczewski",
           "RAS Pre-Built (3rd Place)" => "Deborah Hudson",
           "RAS Pre-Built (3rd Place)" => "Nathan Schomer",
           "RAS On-The-Spot (1st Place)" => "Jordan Frank",
           "RAS On-The-Spot (1st Place)"=>"Jordan Blake",
           "RAS On-The-Spot (2nd Place)" => "Andrew Powel",
           "RAS On-The-Spot (2nd Place)" => "",
           "RAS On-The-Spot (3rd Place)" => "",
           "RAS On-The-Spot (3rd Place)" => ""
           );*/
    /*
     Jordan Blake	343855957	Lafayette College Brown Bag, 1st
     Jordan Frank
     Mont 2nd
     Daniel Albuquerque	319810361	Montgomery College Micromouse,
     Jordan Deuser	319817867	Montgomery College Micromouse,
     Spencer Hamblin	319997811	Montgomery College Micromouse,
     Dennis Ngo	319946969	Montgomery College Micromouse,
     Temple 3rd
     
     T-Shirt 1: 49.760101010101
     T-Shirt 3: 34.901515151515
     T-Shirt 9: 31.25
     
     Project 1: 45.833333333333
     Project 6: 30.429292929293
     Project 4: 29.722222222222
     */
    $i = 0;
    foreach($a as $key => $val) {
        $pdf->Add_Fancy_Label($a[$i], $b[$i]);
        $i++;
    }
    
    
//    echo var_dump($row);
//    echo $row['fname']." ".$row['lname'];


/*for($i=0;$i<sizeof($tag);$i++) {
	$text = sprintf("%s\n%s\n%s\n%s", $tag[$i]["NAME"], $tag[$i]["SCHOOL"], $tag[$i]["DEPT"], $tag[$i]["CLASS"]);
	$pdf->Add_Label($tag[$i]["NAME"], $tag[$i]["SCHOOL"], $tag[$i]["DEPT"], $tag[$i]["CLASS"]);
}*/
    
    include('../vote/setup.php');
    $q = mysql_query("SELECT * FROM `attendeeinfo` WHERE `school` = 'Counselor'");
    while($row = mysql_fetch_assoc($q)) {
        if($row['fname'] == "Shafaye") {
            $row['fname'] = "AB";
            $row['lname'] = "Shafaye";
        }
        if($row['fname'] == "pareshkumar") {
            $row['fname'] = "Pareshkumar";
            $row['lname'] = "Brahmbhatt";
        }
        $pdf->Add_Fancy_Label2($row['school'], $row['fname']." ".$row['lname']);
    }
    $pdf->Add_Fancy_Label2("", "Robert Krchnavek");

$pdf->Output();
?>
