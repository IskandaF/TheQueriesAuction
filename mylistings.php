<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php 

if (session_status() == PHP_SESSION_NONE) {
  session_start();
};


?>
<div class="container">
<h2 class="my-3">My Listings</h2>

<?php if(isset ($_SESSION["logged_in"])) : ?>
<a href="create_auction.php" class="btn btn-outline-secondary btn-sm align-self-right" class="row" style="float: right;" >Create Auction</a>


<!--<h2 class="my-3">My listings</h2>
<div class="row">
<div class="col-sm-4 align-self-right">
</div>
	<html>
	<body>
	<form>
	<a href="create_auction.php" class="btn btn-outline-secondary btn-sm align-self-right"  >Create Auction</a>
	</body>
    </html>
	  -->



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

  $currentuserID = $_SESSION['userID'];
  //echo gettype($currentuserID);

  $sql = " SELECT i.itemID, i.reservePrice, i.title, i.closeDate, i.catID, b.bidValue FROM Items i, Bids b WHERE i.sellerID = $currentuserID AND i.highestbidID = b.bidID ";
  
  $sqla = " SELECT e.itemID, e.reservePrice, e.title, e.closeDate, e.catID, b.bidValue, e.saleSuccess FROM Bids b, ExpiredAuctions e WHERE e.sellerID = $currentuserID AND e.highestbidID = b.bidID ";

  $result = mysqli_query($connection, $sql);
  $resulta = mysqli_query($connection, $sqla);

  if ( mysqli_num_rows($result) == 0 AND mysqli_num_rows($resulta) == 0 )
  {
  	echo('You do not have any listings yet.');
	
  }

  elseif ( mysqli_num_rows($result) != 0 AND mysqli_num_rows($resulta) == 0 )	  
  {
	  echo('


 		 <!DOCTYPE html>

   <html>
   <title> 
   <head> Running Auctions </head>
   </title>
   <body>
  	 <table>
 			 
	          <table style="width:100%" border=\'1\'>
	          <caption> Running Auctions </caption>
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
	 
 elseif( mysqli_num_rows($result) == 0 AND mysqli_num_rows($resulta) != 0)
 {
		echo('
			 <!DOCTYPE html>

	  <html>
	  <title>   
	  <head> Expired Auctions </head>
	  </title>
	  <body>
	 	 <table>
		
				  <table style="width:100%" border=\'1\'>
				  <caption> Expired Auctions </caption>
			 	  <table style="width:100%" border=\'1\'>


			 <tr>
				 <th> Item ID </th>
				 <th> Title </th>
				 <th> Close Date </th>
				 <th> Category </th>
				 <th> Reserve Price </th>
				 <th> Highest Bid </th>
				 <th> Sale Success </th>


			 </tr>

	');
	   while ($row = mysqli_fetch_array($resulta)){
	   echo ('<tr><td>'.$row['itemID'].'</td>');
	   echo ('<td>'.$row['title'].'</td>');
	   echo ('<td>'.$row['closeDate'].'</td>');
	   echo ('<td>'.$row['catID'].'</td>');
	   echo ('<td>'.$row['reservePrice'].'</td>');
	   echo ('<td>'.$row['bidValue'].'</td>');
	   echo ('<td>'.$row['saleSuccess'].'</td></tr>');
	   }
 }	
		
 elseif( mysqli_num_rows($result) != 0 AND mysqli_num_rows($resulta) != 0 )
 {
  echo('


		 <!DOCTYPE html>

  <html>
  <title>  
  <head> Running auctions </head>
  </title>
  <body>
 	 <table>
	          <table style="width:100%" border=\'1\'>
	          <caption> Running Auctions </caption>
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

echo nl2br ('');
	echo('
		 <!DOCTYPE html>

  <html>
  <title>   
  <head> Expired Auctions </head>
  </title>
  <body>
 	 <table>
	 
			  <table style="width:100%" border=\'1\'>
			  <caption> Expired Auctions </caption>
		 	  <table style="width:100%" border=\'1\'>
		      
			 

		 <tr>
			 <th> Item ID </th>
			 <th> Title </th>
			 <th> Close Date </th>
			 <th> Category </th>
			 <th> Reserve Price </th>
			 <th> Highest Bid </th>
			 <th> Sale Success </th>


		 </tr>

');
   while ($row = mysqli_fetch_array($resulta)){
   echo ('<tr><td>'.$row['itemID'].'</td>');
   echo ('<td>'.$row['title'].'</td>');
   echo ('<td>'.$row['closeDate'].'</td>');
   echo ('<td>'.$row['catID'].'</td>');
   echo ('<td>'.$row['reservePrice'].'</td>');
   echo ('<td>'.$row['bidValue'].'</td>');
   echo ('<td>'.$row['saleSuccess'].'</td></tr>');
   }
 	
 }
	
		


  ?>




<?php else:
	  echo '<button style="color:white;background:green;margin-top:60px;margin-left:60px;" type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Please Login</button>';
	  ?>

<?php endif ?>
<?php include_once("footer.php")?>
