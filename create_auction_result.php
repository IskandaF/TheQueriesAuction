<?php include_once("header.php");
include_once("mysqli.php");
if (session_status() == PHP_SESSION_NONE){
  session_start();
};
?>


<div class="container my-5">

<?php


$useridquery = "SELECT * FROM Users WHERE email = '" . $_SESSION['username'] . "' ";
$useridresult = mysqli_query($connection, $useridquery);
$useridrow = mysqli_fetch_array($useridresult);


$itemTitle = mysqli_real_escape_string($connection, $_POST['auctionTitle']);
$itemDesc = mysqli_real_escape_string($connection, $_POST['auctionDetails']);
$itemCat = $_POST['auctionCategory'];
$itemBid = $_POST["auctionStartPrice"];
$itemResCheck = $_POST['auctionReservePrice'];
$endDate = $_POST['auctionEndDate'];
$openDate = date("Y-m-d");
$userID = $useridrow['userID'];


if ($itemResCheck == "") {
  //echo 'empty reserve price';
  //echo '<br>';
  //if the reserve price is not set, set it to be the starting bid
  $itemRes = $itemBid;
  //echo $itemRes;
} else {
  $itemRes = $itemResCheck;
  //echo '<br>';
  //echo $itemRes;
}



//Value testing:
/*
echo 'Title: ' . $itemTitle . '<br>';
echo 'Description: ' . $itemDesc . '<br>';
echo 'Category Code: ' . $itemCat . '<br>';
echo 'Starting bid: ' . $itemBid . '<br>';
echo 'Reserve Price: ' . $itemRes . '<br>';
echo 'Open date: ' . $openDate . '<br>';
echo 'End date: ' . $endDate . '<br>';
echo 'Current userID: ' . $userID . '<br>';
//echo 'Reserve Price Type: ' . $itemResType;
*/


//check user is logged in
if (isset($_SESSION['logged_in'])) {

  //if compulsory values are empty...
  //empty starting bid is caught by html code in create_auction.php
  ///It would be nicer for the message to tell you which field you left blank...
  if (($itemTitle == "") or ($endDate == "") or ($itemCat == 'Choose...'))  {
      echo 'Please fill in compulsory information. <br>Redirecting...';
      header("refresh:2;url=create_auction.php");

  } else {

    //Update Items table
    $itemsUpdateQuery = "INSERT INTO Items (title, description, reservePrice, openDate, closeDate, sellerID, catID)
          VALUES ('".$itemTitle."', '".$itemDesc."', '".$itemRes."', '".$openDate."', '".$endDate."', '".$userID."', '".$itemCat."')";

    if ($connection->query($itemsUpdateQuery) === TRUE) {
      //echo 'New item inserted into Items table <br>';
    } else {
      echo "Error: " . $sql . "<br>" . $connection->error;
    }

    //Get new itemID from new row of itemsTable
    $itemIDQuery = "SELECT itemID FROM Items ORDER BY itemID DESC LIMIT 1";
    $itemIDResult = mysqli_query($connection, $itemIDQuery);
    $itemIDrow = mysqli_fetch_array($itemIDResult);

    $newitemID = $itemIDrow['itemID'];
    //echo 'new item ID: ' . $newitemID . '<br>';


    //Update Bids table
    $bidsUpdateQuery = "INSERT INTO Bids (bidderUserID, bidValue, bidDate, itemID)
          VALUES ('".$userID."', '".$itemBid."', '".$openDate."', '".$newitemID."')";

    if ($connection->query($bidsUpdateQuery) === TRUE) {
      //echo 'New bid inserted into Bids table for new item <br>';
    } else {
      echo "Error: " . $sql . "<br>" . $connection->error;
    }

    //Update highest bidder in Items table
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
      //echo "Highest bid value updated.";
    } else {
      //if highestBid in Items not updated
      echo "Error: " . $sql . "<br>" . $connection->error;
    }
    $itemurl = 'listing.php?item_id=' . $newitemID;
    echo('<div class="text-center">Auction successfully created! <a href="' . $itemurl . '">View your new listing.</a></div>');
  }

} else {
  echo 'Please log in.<br>Redirecting...';
  header("refresh:2;url=browse.php");
}


?>

</div>


<?php include_once("footer.php")?>