<?php include_once("mysqli.php")?>

<?php

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


//Iskander, this shows whether the highest bid was above the reserve price or not for each ended auction.
//I've printed these statements out for now, but you can use them to send emails instead.
//You can notify the seller that the auction has failed.
while ($row = mysqli_fetch_array($numsalesresult)) {
  if ($numsalesrow['bidValue'] >= $numsalesrow['reservePrice']) {
    echo 'Item no. ' . $numsalesrow['itemID'] . ' was successfully sold by seller ' . $numsalesrow['sellerID'] . ' to ' .
    $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'] . '<br>';



    // Sending an email to the seller

    $email = new PHPMailer(true);
          $email->Subject = "Selling notification";

          $email->Body    = 'Your item no. ' . $numsalesrow['itemID'] . ' was successfully to ' .
          $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'] . '<br>';

          $email->AltBody = 'Your item no. ' . $numsalesrow['itemID'] . ' was successfully to ' .
          $numsalesrow['bidderUserID'] . ' for £' . $numsalesrow['bidValue'];
        $email->addAddress($numsalesrow["email"]);
          sendEmail($email);
    // End of sending an email to the seller

  } else {
    echo 'Item no. ' . $numsalesrow['itemID'] . ' was not sold by seller ' . $numsalesrow['sellerID'] . '. Highest bid was £' .
    $numsalesrow['bidValue'] . ' but the reserve price was £' . $numsalesrow['reservePrice'] . '<br>';

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

    //echo 'itemID: ' . $numsalesrow['itemID'] . '<br>';
    //echo 'reservePrice: ' . $numsalesrow['reservePrice'] . '<br>';
    //echo 'bidValue: ' . $numsalesrow['bidValue'] . '<br>';

}

$nowDate = date("Y-m-d");



//echo $numsales;

//Add the auctions to Sales table if endDate has expired
$salesQuery = "INSERT INTO ExpiredAuctions (catID, closeDate, description, highestbidID, itemID, openDate, reservePrice, sellerID, title)
                SELECT catID, closeDate, description, highestbidID, itemID, openDate, reservePrice, sellerID, title
                FROM Items
                WHERE closeDate <= CURDATE()";



if ($connection->query($salesQuery) === TRUE) {

    echo $numsales . " new record(s) added to ExpiredAuction table successfully <br>";

    //If successful, remove the ended auctions from the Items table
    $removeQuery = "DELETE FROM Items
                      WHERE closeDate <= CURDATE()";

    if ($connection->query($removeQuery) === TRUE) {
      echo $numsales . " old record(s) removed from ExpiredAuction table successfully <br>";


      //Update saleSuccess column with 'y' if bidValue >= reservePrice, else 'n'
      $successfulUpdate = "UPDATE ExpiredAuctions e, Bids b SET e.saleSuccess = 'y'
                            WHERE e.highestbidID = b.bidID AND e.reservePrice <= b.bidValue";
      $connection->query($successfulUpdate);

      $unsuccessfulUpdate = "UPDATE ExpiredAuctions e, Bids b SET e.saleSuccess = 'n'
                              WHERE e.highestbidID = b.bidID AND e.reservePrice > b.bidValue";
      $connection->query($unsuccessfulUpdate);


      //Write a note in the saleslogs.txt file to confirm the transaction
      $myfile = fopen("saleslogs.txt", "a");
      $txt = "Added " . $numsales . " items to Sales table on " . $nowDate;
      fwrite($myfile, "\n". $txt);
      fclose($myfile);


      







      ///Iskander, add email notification function for successful sale here

      // Email reporting to the user
      
      // Email reporting to the user

      header('Location: '.$_SERVER['PHP_SELF']);
      //If unsuccessful, write a note in the saleslogs.txt file with the error message
    } else {
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



?>
