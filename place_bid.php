<?php
include_once("mysqli.php");
include_once("mail/bid_placed.php");
session_start();
?>


<?php
//Elina - here's an example of how to get the userID for the current user so you can link it up to mylistings.php
$useridquery = "SELECT * FROM Users WHERE email = '" . $_SESSION['username'] . "' ";
$useridresult = mysqli_query($connection, $useridquery);
$useridrow = mysqli_fetch_array($useridresult);
?>


<?php
/*
Test variables
echo $useridrow['userID'];
echo ($_POST['bid']);
echo $_SESSION['username'];
echo $_SESSION['itemID'];
echo date("Y-m-d");
echo 'reserveprice: ' . $_SESSION['reservePrice'];
echo 'current price:' . $_SESSION['currentPrice'];
echo $_SESSION['sellerID'];
*/
?>

<?php

//extract userID from login cookie
$userID = $useridrow['userID'];

// extract bid value using $_POST
$bidValue = $_POST['bid'];

// current date
$bidDate = date("Y-m-d");

//itemID from session variable
$itemID = $_SESSION['itemID'];


//Check user is logged in
if (isset($_SESSION['logged_in'])) {

  //Check the user is not the seller
  if ($userID != $_SESSION['sellerID']){


    //Check bid is higher than previous highest bid and reserve price
    if ($bidValue > $_SESSION['currentPrice'])  { //In hindsight, reservePrice shouldn't stop you from placing a lower bid. It should just cancel the final sale if the final bid is lower
      $stmt4 = "INSERT INTO Bids (bidderUserID, bidValue, bidDate, itemID)
                VALUES ('".$userID."', '".$bidValue."', '".$bidDate."', '".$itemID."')";

      //add new bid to Bids table
      if ($connection->query($stmt4) === TRUE) {
        echo "Bid placed successfully. ";
        echo "<br>";

        // update highest bid value in Items table
        $updatehighbid = "UPDATE Items AS i
                          SET i.highestbidID = (SELECT c.bidID
                          FROM
                          (SELECT a.mv, a.itemID, b.bidID
                          FROM
                          (SELECT max(bidValue) as mv, itemID
                          FROM Bids
                          GROUP BY itemID) as a
                          LEFT OUTER JOIN Bids b
                          ON a.itemID = b.itemID AND
                          a.mv = b.bidValue) AS c
                          WHERE c.itemID = i.itemID)";
          if ($connection->query($updatehighbid) === TRUE) {
            echo "Highest bid value updated.";



            $email->addTo($_SESSION['username'], "Example User");

			$email->addContent("text/plain", $bidValue);
			$email->addContent("text/html", ("You just have put the highest bid of £".$bidValue." for the ".$_SESSION["itemdescription"] ));
      sendBidPlacedEmail($email,$sendgrid);

      // getting list of other bidders to update them

      $getotherbidders='SELECT email
      FROM Watchlist w
      join users u on w.userID=u.userID
      WHERE w.itemID='.$_SESSION['itemID'];
      $otherbidders=$connection->query($getotherbidders);
      while($row = mysqli_fetch_array($otherbidders)){
        if ($row["email"]!=$_SESSION['username']){
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("auctionthequeries@gmail.com", "The Queries Auction");
        $email->setSubject("The maximum bid for the item ".$_SESSION["itemdescription"]." has just been updated to ".$bidValue);
        $email->addTo($row["email"], "Example User");
        $email->addContent("text/plain", "The maximum bid for the item ".$_SESSION["itemdescription"]." has just been updated to ".$bidValue);
        $email->addContent("text/html", "<strong>The maximum bid for the item ".$_SESSION["itemdescription"]." has just been updated to ".$bidValue."</strong>");
        try {
          sendBidPlacedEmail($email,$sendgrid);
      } catch (Exception $e) {
          echo 'Caught exception: '. $e->getMessage() ."\n";
      }
        }

      }
// end of updating other users
// sending an email to seller
{
  $query='SELECT email from Users where userID='.$_SESSION['sellerID'];
  $result=$connection->query($query);
  $row = mysqli_fetch_array($result);
  $email = new \SendGrid\Mail\Mail();
  $email->setFrom("auctionthequeries@gmail.com", "The Queries Auction");
  $email->setSubject("The maximum bid for your item ".$_SESSION["itemdescription"]." has just been updated to ".$bidValue);
  $email->addTo($row["email"], "Example User");
  $email->addContent("text/plain", "The maximum bid for your item ".$_SESSION["itemdescription"]." has just been updated to ".$bidValue);
  $email->addContent("text/html", "<strong>The maximum bid for your item ".$_SESSION["itemdescription"]." has just been updated to ".$bidValue."</strong>");
  sendBidPlacedEmail($email,$sendgrid);

}



header("refresh:2;url=listing.php" . "?item_id=" . $itemID);

      unset($_SESSION["itemdescription"]);
      unset($_SESSION['itemID']);


          } else {
            //if highestBid in Items not updated
            echo "Error: " . $sql . "<br>" . $connection->error;
            header("refresh:2;url=listing.php" . "?item_id=" . $itemID);
          }


        } else {
          //if new bid not added to Bids
          echo "Error: " . $sql . "<br>" . $connection->error;
        }
      } else {
        //if bid lower than previous highest bid
        echo 'Bid too low. Please bid higher than £' . $_SESSION['currentPrice'];
        header("refresh:2;url=listing.php" . "?item_id=" . $itemID);
      }
    } else {
      echo 'You cannot bid on your own item!';
      header("refresh:2;url=listing.php" . "?item_id=" . $itemID);
    }

/*
  } else {
    //if user not logged in
    echo 'Please log in.';
    header("refresh:2;url=browse.php");
  }
*/

  mysqli_close($connection);
  header("refresh:2;url=listing.php" . "?item_id=" . $itemID);


?>
