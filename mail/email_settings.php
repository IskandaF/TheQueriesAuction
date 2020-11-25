
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'mail/PHPMailer/src/Exception.php';
require 'mail/PHPMailer/src/PHPMailer.php';
require 'mail/PHPMailer/src/SMTP.php';

// Instantiation and passing `true` enables exceptions
function createEmail(){
    $email = new PHPMailer(true);

}

function sendEmail($email){
try {
$email->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
$email->isSMTP();                                            // Send using SMTP
$email->SMTPDebug  = 1;  
$email->SMTPAuth   = TRUE;
$email->Port       = 587;
$email->Host       = "smtp.gmail.com";
$email->Username   = "auctionthequeries@gmail.com";
$email->Password   = "zazwav-3zeqhu-wicdeQ";                          // Enable SMTP authentication
                             // SMTP password
$email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
$email->setFrom("auctionthequeries@gmail.com", "The Queries Auction");
$email->isHTML(true);  

$email->CharSet = 'UTF-8';


$email->send();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$email->ErrorInfo}";
}
}

;


