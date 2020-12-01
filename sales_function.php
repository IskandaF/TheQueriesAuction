<?php include_once("mysqli.php");

include_once("mail/email_settings.php");
?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
/*

                */







//Get the number of auctions ended
$numsalesquery = "SELECT i.itemID, i.closeDate, i.highestbidID, i.reservePrice,u.email,i.sellerID, b.bidID, b.bidValue, b.bidderUserID
                  FROM Items i, Bids b,Users u
                  WHERE i.closeDate <= CURDATE()
                  AND i.highestbidID = b.bidID
                  AND i.sellerID=u.userID
                  ";
$numsalesresult = mysqli_query($connection, $numsalesquery);
$numsales = mysqli_num_rows($numsalesresult);
$numsalesrow = mysqli_fetch_array($numsalesresult);

$useremailsalesquery=" SELECT u.email, i.itemID
  FROM Items i, Bids b,Users u
  WHERE i.closeDate <= CURDATE()
  AND i.highestbidID = b.bidID
  AND b.bidderUserID=u.userID
";
$useremailsalesresult = mysqli_query($connection, $useremailsalesquery);
$useremail = mysqli_num_rows($useremailsalesresult);
$useremailrow = mysqli_fetch_array($useremailsalesresult);




//Iskander, this shows whether the highest bid was above the reserve price or not for each ended auction.
//I've printed these statements out for now, but you can use them to send emails instead.
//You can notify the seller that the auction has failed.
// while ($row = mysqli_fetch_array($numsalesresult)) {
//   if ($numsalesrow['bidValue'] >= $numsalesrow['reservePrice']) {
//     if ($numsalesrow["sellerID"] != $numsalesrow["bidderUserID"]){
//     echo 'Item no. ' . $numsalesrow['itemID'] . ' was successfully sold by seller ' . $numsalesrow['sellerID'] . ' to ' .
//     $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'] . '<br>';




//     }

// // Sending an email to the winner
//   } else {
//     echo($numsalesrow["email"]);
//     echo 'Item no. ' . $numsalesrow['itemID'] . ' was not sold by seller ' . $numsalesrow['sellerID'] . '. Highest bid was £' .
//     $numsalesrow['bidValue'] . ' but the reserve price was £' . $numsalesrow['reservePrice'] . '<br>';

//     $email = new PHPMailer(true);
//           $email->Subject = "Selling notification";

//           $email->Body    = 'Your item no. ' . $numsalesrow['itemID'] . ' was not sold. Highest bid was £' .
//           $numsalesrow['bidValue'] . ' but the reserve price was £' . $numsalesrow['reservePrice']. '<br>';

//           $email->AltBody = 'Your item no. ' . $numsalesrow['itemID'] . ' was not sold. Highest bid was £' .
//           $numsalesrow['bidValue'] . ' but the reserve price was £' . $numsalesrow['reservePrice']. '<br>'.

//           $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'];
//         $email->addAddress($numsalesrow["email"]);
//           sendEmail($email);
//   }



//     //echo 'itemID: ' . $numsalesrow['itemID'] . '<br>';
//     //echo 'reservePrice: ' . $numsalesrow['reservePrice'] . '<br>';
//     //echo 'bidValue: ' . $numsalesrow['bidValue'] . '<br>';

// }

$nowDate = date("Y-m-d");



//echo $numsales;

//Add the auctions to Sales table if endDate has expired
$salesQuery = "INSERT INTO ExpiredAuctions (catID, closeDate, description, highestbidID, itemID, openDate, reservePrice, sellerID, title)
                SELECT catID, closeDate, description, highestbidID, itemID, openDate, reservePrice, sellerID, title
                FROM Items
                WHERE closeDate <= CURDATE()";




if ($connection->query($salesQuery) === TRUE) {

    //echo $numsales . " new record(s) added to ExpiredAuction table successfully <br>";

    //If successful, remove the ended auctions from the Items table
    $removeQuery = "DELETE FROM Items
                      WHERE closeDate <= CURDATE()";

    if ($connection->query($removeQuery) === TRUE) {
      //echo $numsales . " old record(s) removed from Items table successfully <br>";


      //Update saleSuccess column with 'n' if reservePrice > bidValue or if highest bidderUserID = sellerID, else 'y'

      $unsuccessfulUpdate = "UPDATE ExpiredAuctions e, Bids b SET e.saleSuccess = 'n'
                              WHERE (e.highestbidID = b.bidID AND e.reservePrice > b.bidValue)
                              OR (e.highestbidID = b.bidID AND b.bidderUserID = e.sellerID) ";
      $connection->query($unsuccessfulUpdate);


      $successfulUpdate = "UPDATE ExpiredAuctions e, Bids b SET e.saleSuccess = 'y'
                            WHERE e.highestbidID = b.bidID AND e.reservePrice <= b.bidValue
                            AND b.bidderUserID != e.sellerID";
      $connection->query($successfulUpdate);


      //Write a note in the saleslogs.txt file to confirm the transaction
      $myfile = fopen("saleslogs.txt", "a");
      $txt = "Added " . $numsales . " items to Sales table on " . $nowDate;
      fwrite($myfile, "\n". $txt);
      fclose($myfile);


      //insert bids for expired items into ExpiredBids table
      $expiredBids = "INSERT INTO ExpiredBids (bidDate, bidderUserID, bidID, bidValue, itemID)
                      SELECT bidDate, bidderUserID, bidID, bidValue, itemID
                      FROM Bids
                      WHERE itemID IN (
                      SELECT itemID FROM ExpiredAuctions)";
      if ($connection->query($expiredBids) === TRUE) {
        //echo "Bids for expired items moved to ExpiredBids table <br>";


      $removeBids = "DELETE FROM Bids WHERE bidID IN (
                      SELECT bidID FROM ExpiredBids)";
      if ($connection->query($removeBids) === TRUE) {
        //echo "Bids for expired items removed from Bids table <br> ";
      } else {
        echo "Error: " . $sql . "<br>" . $connection->error;
      }


    } else {
      echo "Error: " . $sql . "<br>" . $connection->error;
    }






      ///Iskander, add email notification function for successful sale here

          // Sending an email to the seller
          if ($numsalesrow['bidValue'] >= $numsalesrow['reservePrice']) {
            if ($numsalesrow["sellerID"] != $numsalesrow["bidderUserID"]){
    $email = new PHPMailer(true);
    $email->Subject = "Selling notification";

    $email->Body    = 'Your item no. ' . $numsalesrow['itemID'] . ' was successfully to ' .
    $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'] . '<br>';

    $email->AltBody = 'Your item no. ' . $numsalesrow['itemID'] . ' was successfully to ' .
    $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'];
    $email->addAddress($numsalesrow["email"]);
    sendEmail($email);
// End of sending an email to the seller


// Sending an email to the winner
    echo($useremailrow["email"]);
    $email = new PHPMailer(true);
    $email->Subject = "Selling notification";

    $email->Body    = 'Congratulations! You won the auction for the item no. ' . $numsalesrow['itemID'] .' for £' . $numsalesrow['bidValue'] . '<br>';

    $email->AltBody = 'Congratulations! You won the auction for the item no. ' . $numsalesrow['itemID'] .' for £' . $numsalesrow['bidValue'];
    $email->addAddress($useremailrow["email"]);
    sendEmail($email);

      // Email reporting to the user

      // Email reporting to the user

      // header('Location: '.$_SERVER['PHP_SELF']);



    }

          };

            if ($numsalesrow['bidValue'] == $numsalesrow['reservePrice']){
              if ($numsalesrow["sellerID"] == $numsalesrow["bidderUserID"]){
            $email = new PHPMailer(true);
            $email->Subject = "Selling notification";

            $email->Body    = 'We are really sorry to inform you that no one has bid on your item ' . $numsalesrow['itemID'];

            $email->AltBody = 'We are really sorry to inform you that no one has bid on your item ' . $numsalesrow['itemID'];

            $email->addAddress($numsalesrow["email"]);
            sendEmail($email);
            $flag=True;
          }
      //  if ($flag){

      //              $email = new PHPMailer(true);
      //     $email->Subject = "Selling notification";

      //     $email->Body    = 'Your item no. ' . $numsalesrow['itemID'] . ' was not sold. Highest bid was £' .
      //     $numsalesrow['bidValue'] . ' but the reserve price was £' . $numsalesrow['reservePrice']. '<br>';

      //     $email->AltBody = 'Your item no. ' . $numsalesrow['itemID'] . ' was not sold. Highest bid was £' .
      //     $numsalesrow['bidValue'] . ' but the reserve price was £' . $numsalesrow['reservePrice']. '<br>'.

      //     $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'];
      //   $email->addAddress($numsalesrow["email"]);
      //     sendEmail($email);

      //        }
            }

              if ($numsalesrow['bidValue'] <= $numsalesrow['reservePrice']){
                if ($numsalesrow["sellerID"] != $numsalesrow["bidderUserID"]){
                   $email = new PHPMailer(true);
          $email->Subject = "Selling notification";

          $email->Body    = 'Your item no. ' . $numsalesrow['itemID'] . ' was not sold. Highest bid was £' .
          $numsalesrow['bidValue'] . ' but the reserve price was £' . $numsalesrow['reservePrice']. '<br>';

          $email->AltBody = 'Your item no. ' . $numsalesrow['itemID'] . ' was not sold. Highest bid was £' .
          $numsalesrow['bidValue'] . ' but the reserve price was £' . $numsalesrow['reservePrice']. '<br>'.

          $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'];
        $email->addAddress($numsalesrow["email"]);
          sendEmail($email);
              }
            }
            


    } else {
            //If unsuccessful, write a note in the saleslogs.txt file with the error message
      echo "Error: " . $sql . "<br>" . $connection->error;
      $myfile = fopen("saleslogs.txt", "a");
      $txt = "Error: " . $sql . "<br>" . $connection->error . " on " . $nowDate;
      fwrite($myfile, "\n". $txt);
      fclose($myfile);
    }

    //If unsuccessful, write a note in the saleslogs.txt file with the error message
} else {
    echo "Error: " . $sql . "<br>" . $connection->error;
    $myfile = fopen("saleslogs.txt", "a");
    $txt = "Error: " . $sql . "<br>" . $connection->error . " on " . $nowDate;
    fwrite($myfile, "\n". $txt);
    fclose($myfile);

}

header('location:browse.php');
?>
