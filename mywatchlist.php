<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once("mysqli.php")?>
<?php 
if (session_status() == PHP_SESSION_NONE){
session_start(); 
};

?>

<div class="container">

<h2 class="my-3">My watchlist</h2>

<?php

//get current userID
$currentuserID = $_SESSION['userID'];

//Get items on watchlist that match with current userID and this itemID
$watchquery = "SELECT i.itemID, i.title, i.description, b.bidValue, i.closeDate, b.bidID
          FROM Items i, Bids b, Watchlist w
          WHERE i.highestbidID = b.bidID
          AND w.itemID = i.itemID AND w.userID = $currentuserID
          ORDER BY itemID DESC";
$watchresult = mysqli_query($connection, $watchquery)
or die('Error making select users query' .
mysql_error());

//check user is logged in
if (isset($_SESSION['logged_in'])) {


  //loop through the watchlist items
      while ($watchrow = $watchresult->fetch_assoc()) {

        //echo gettype($watchrow);

        $item_id = $watchrow['itemID'];

        $stmt2 = "SELECT COUNT(bidID) as c FROM Bids WHERE itemID = $item_id";
        $result2 = mysqli_query($connection, $stmt2);
        $row2 = mysqli_fetch_array($result2);

        $title = $watchrow['title'];
        $description = $watchrow['description'];
        $current_price = $watchrow['bidValue'];
        $num_bids = $row2['c'];
        $end_time = new DateTime($watchrow['closeDate']);


        // This uses a function defined in utilities.php
          print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_time);
      }

} else {
  echo 'Please log in.';
  header("refresh:2;url=browse.php");
}
?>











<?php include_once("footer.php")?>
