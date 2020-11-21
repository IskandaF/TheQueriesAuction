<?php
include_once("mysqli.php");
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
    if (($bidValue > $_SESSION['currentPrice']) and ($bidValue > $_SESSION['reservePrice'])) { //In hindsight, reservePrice shouldn't stop you from placing a lower bid. It should just cancel the final sale if the final bid is lower
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
        //if bid lower than previous highest bid or reserve price
        echo 'Bid too low. Please bid higher than £' . $_SESSION['currentPrice'];
        header("refresh:2;url=listing.php" . "?item_id=" . $itemID);
      }
    } else {
      echo 'You cannot bid on your own item!';
      header("refresh:2;url=listing.php" . "?item_id=" . $itemID);
    }

  } else {
    //if user not logged in
    echo 'Please log in.';
    header("refresh:2;url=browse.php");
  }

  mysqli_close($connection);
  header("refresh:2;url=listing.php" . "?item_id=" . $itemID);


?>
