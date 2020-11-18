
<?php
// require 'vendor/autoload.php'; // If you're using Composer (recommended)
// Comment out the above line if not using Composer
require("sendgrid-php/sendgrid-php.php");
// If not using Composer, uncomment the above line and
// download sendgrid-php.zip from the latest release here,
// replacing <PATH TO> with the path to the sendgrid-php.php file,
// which is included in the download:
// https://github.com/sendgrid/sendgrid-php/releases

$email = new \SendGrid\Mail\Mail(); 
$email->setFrom("auctionthequeries@gmail.com", "The Queries Auction");
$email->setSubject("You placed the bid");

$sendgrid = new \SendGrid("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");

function sendBidPlacedEmail($email,$sendgrid){
try {
    $response = $sendgrid->send($email);
} catch (Exception $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}

};


