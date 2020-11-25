
<?php
// require 'vendor/autoload.php'; // If you're using Composer (recommended)
// Comment out the above line if not using Composer
require("sendgrid-php/sendgrid-php.php");
require("/Users/soulist/Sites/UCL Computer Science/sendgridkey.php");
// If not using Composer, uncomment the above line and
// download sendgrid-php.zip from the latest release here,
// replacing <PATH TO> with the path to the sendgrid-php.php file,
// which is included in the download:
// https://github.com/sendgrid/sendgrid-php/releases

$email = new \SendGrid\Mail\Mail(); 
$email->setFrom("auctionthequeries@gmail.com", "The Queries Auction");
$email->setSubject("You placed the bid");



function sendBidPlacedEmail($email,$sendgrid){
try {
    $response = $sendgrid->send($email);
} catch (Exception $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}

};


