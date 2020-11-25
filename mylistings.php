<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php 

if(!isset($_COOKIE["PHPSESSID"]))
{
  session_start();
} 
 

?>
<div class="container">

<h2 class="my-3">My listings</h2>



  <?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.


  // TODO: Check user's credentials (cookie/session).

  // TODO: Perform a query to pull up their auctions.

  // TODO: Loop through results and print them out as list items.

  //echo "Connected.";
  //echo 'gamo ti zoi mou';

  $servername = "localhost";
  $username = "root";
  $password = "root";
  $database = "AuctionDB";

  $connection = mysqli_connect($servername, $username, $password, $database);
  if (!$connection) {
	  die("Connection Failed: ".mysql_connect_error());
  }
if (isset($_SESSION['userID'])){
  $currentuserID = $_SESSION['userID'];
  //echo gettype($currentuserID);

  $sql = " SELECT i.itemID, i.reservePrice, i.title, i.closeDate, i.catID, b.bidValue FROM Items i, Bids b WHERE i.sellerID = $currentuserID AND i.highestbidID = b.bidID ";

  $result = mysqli_query($connection, $sql);

  //$row = mysqli_fetch_array($result);


  if(mysqli_num_rows($result) == 0)
  {
  	echo('You do not have any listings yet.');
  }

  else {
	  echo('


 		 <!DOCTYPE html>

   <html>
   <title>
   <head> Fetch Data From Database </head>
   </title>
   <body>
  	 <table>
 			  <table style="width:100%" border=\'1\'>


 		 <tr>
 			 <th> Item ID </th>
 			 <th> Title </th>
 			 <th> Close Date </th>
 			 <th> Category </th>
 			 <th> Reserve Price </th>
 			 <th> Highest Bid </th>


 		 </tr>

	');
	while ($row = mysqli_fetch_array($result)){
	echo ('<tr><td>'.$row['itemID'].'</td>');
	echo ('<td>'.$row['title'].'</td>');
	echo ('<td>'.$row['closeDate'].'</td>');
	echo ('<td>'.$row['catID'].'</td>');
	echo ('<td>'.$row['reservePrice'].'</td>');
	echo ('<td>'.$row['bidValue'].'</td></tr>');
	}
 }

		/*
 	<?php

    	  while ($row = mysqli_fetch_array($result)){
 	?>

 	   <tr>
 			<td><?php echo $row['itemID']; ?></td>
 			<td><?php echo $row['title']; ?></td>
 			<td><?php echo $row['closeDate']; ?></td>
 			<td><?php echo $row['catID']; ?></td>
 			<td><?php echo $row['reservePrice']; ?></td>
 			<td><?php echo $row['bidValue']; ?></td>
 		</tr>


	  <?php
	  	 }
	  ?>

 	  </table>
   </body>

	*/
		}
		echo '<button style="color:white;background:green;margin-top:60px;margin-left:60px;" type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Please Login</button>';


  ?>





<?php include_once("footer.php")?>
