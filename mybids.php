<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php include_once("mysqli.php")?>
<?php 

if(!isset($_COOKIE["PHPSESSID"]))
{
  session_start();
} 

?>

<div class="container">

<h2 class="my-3">My bids</h2>

<?php
  // This page is for showing a user the auctions they've bid on.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.


  // TODO: Check user's credentials (cookie/session).

  // TODO: Perform a query to pull up the auctions they've bidded on.

  // TODO: Loop through results and print them out as list items.

  if (isset($_SESSION['userID'])){
  $currentuserID = $_SESSION['userID'];
  //echo gettype($currentuserID);

  $sql = " SELECT * FROM Bids INNER JOIN Items ON Bids.itemID = Items.itemID WHERE bidderUserID = $currentuserID ";
  //echo $currentuserID ;

  $result = mysqli_query($connection, $sql);




  if(mysqli_num_rows($result) == 0)
  {
  	echo('You do not have any biddings yet.');
  }

  else {

   	  while ($row = mysqli_fetch_array($result)){

      $highestbidderquery="SELECT b.bidderUserID from Bids b,Items i where i.highestbidID=b.bidID and i.itemID='".$row["itemID"]."'";
      $resulthighestbidder = mysqli_query($connection, $highestbidderquery);
      $highestbidderrow=mysqli_fetch_array($resulthighestbidder);

      if ($highestbidderrow["bidderUserID"]==$_SESSION['userID']){
        $biddingmessage=("<p style='color:green'>You are the highest bidder</p>");
      }
      else{
        $biddingmessage=("<p style='color:red'>You are not the highest bidder</p>");
      }

      print_listingg_li($row['itemID'], $row['title'], $row['description'], $row['bidValue'], new DateTime($row['closeDate']),$biddingmessage);



	}
}
  }
  else{
    echo("Log in");
  }
	?>



<?php include_once("footer.php")?>
