<?php

const SENDER_EMAIL_ADDRESS = "<sender-email-address>";
const SENDER_NAME = "<sender-name>";
const SENDER_EMAIL_PASSWORD = "<sender-email-password>";

require 'phpmailer/PHPMailerAutoload.php';

function sendEmail($to, $displayName, $subject, $message) {
    $mail = new PHPMailer;

    //Enable SMTP debugging. 
    $mail->SMTPDebug = 0;                               
    //Set PHPMailer to use SMTP.
    $mail->isSMTP();            
    //Set SMTP host name                          
    $mail->Host = "smtp.gmail.com";
    //Set this to true if SMTP host requires authentication to send email
    $mail->SMTPAuth = true;                          
    //Provide username and password     
    $mail->Username = SENDER_EMAIL_ADDRESS;          
    $mail->Password = SENDER_EMAIL_PASSWORD;     
    //If SMTP requires TLS encryption then set it
    $mail->SMTPSecure = "tls";                           
    //Set TCP port to connect to 
    $mail->Port = 587;                                   

    $mail->From = SENDER_EMAIL_ADDRESS;
    $mail->FromName = SENDER_NAME;

    $mail->addAddress($to, $displayName);

    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = $message;

    if(!$mail->send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    } else {
        return "Message has been sent successfully";
    }
}

?>