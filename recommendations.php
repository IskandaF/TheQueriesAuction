<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("mysqli.php")?>
<?php require("login_result.php")?>
<?php
if (session_status() == PHP_SESSION_NONE){
  session_start();
}

?>


<div class="container">



<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up auctions they might be interested in.
  //Below,User is presented with the 3 Most bid for items currently listed
  echo "<h2 class='my-3'>Recommended for you</h2>";
if(isset($_SESSION['logged_in'])){


echo "<h3 class='my-3'>Popular Listings</h3>";

    $count_above_3 = "SELECT i.itemID, i.title, i.description, b.bidValue, i.closeDate, b.bidID FROM Items i JOIN Bids b on i.highestbidID = b.bidID WHERE b.ItemID IN (SELECT b.ItemID FROM Bids b GROUP BY (b.ItemID) HAVING COUNT(b.bidID) >= 3 ORDER BY COUNT(b.bidID) DESC) AND b.ItemID = i.itemID";

    $top_3 = "SELECT b.ItemID, COUNT(b.bidID) FROM Bids b GROUP BY (itemID) ORDER BY COUNT(b.bidID) DESC LIMIT 3";

    $get_count_above_3 = mysqli_query($connection, $count_above_3);

    $get_top_3 = mysqli_query($connection, $top_3);

    while ($row_search = $get_count_above_3->fetch_assoc()) { // Add a clause in this while loop that says 'AND b.bidderUserID = userID'

       $item_id_search = $row_search['itemID'];
       foreach($get_top_3 as $array){
           if($array['ItemID'] == $item_id_search){

          $stmt2_search = "SELECT COUNT(bidID) as c FROM Bids WHERE itemID = $item_id_search";
          $result2_search = mysqli_query($connection, $stmt2_search);
          $row2_search = mysqli_fetch_array($result2_search);

           $title = $row_search['title'];
           $description = $row_search['description'];
           $current_price = $row_search['bidValue'];
           $num_bids = $row2_search['c'];
           $end_time = new DateTime($row_search['closeDate']);

            // This uses a function defined in utilities.php

            print_listing_li($item_id_search, $title, $description, $current_price, $num_bids, $end_time);
        }
        }
        }
      // TODO: Loop through results and print them out as list items.
$current_userID = $_SESSION['userID'];   //Displays recommendations based off users activity if users have placed bids, otherwise the following recommendation sections are hidden
$mysqli_active_user = new mysqli("localhost","root","root","AuctionDB");
$active_user_query = $mysqli_active_user->prepare("SELECT bidID FROM Bids WHERE bidderUserID = ?");
$active_user_query->bind_Param("s", $current_userID);
$active_user_query->execute();
$active_user = $active_user_query->get_result();
$AU_result = $active_user->fetch_assoc();
echo empty($AU_result);
if(!empty($AU_result)){

    $mysqli_0 = new mysqli("localhost","root","root","AuctionDB");
    $cat_fy_query = $mysqli_0->prepare("SELECT DISTINCT(i.catID), COUNT(b.bidID) FROM Bids b JOIN Items i ON i.itemID = b.itemID
     WHERE b.itemID IN( SELECT DISTINCT(itemID) FROM Bids WHERE bidderuserID = ?) GROUP BY(i.catID) ORDER BY COUNT(b.bidID) DESC LIMIT 1");  //Present the user with the 3 most bid for items from the category that they have placed the most bids in

    $current_userID = $_SESSION['userID'];
    $cat_fy_query->bind_Param("s", $current_userID);
    $cat_fy_query->execute();

    $top_fy_cat = $cat_fy_query->get_result();

    $top_fy_cat_get = $top_fy_cat->fetch_assoc();

    $cat_fy = $top_fy_cat_get['catID'];

    $mysqli_cat_name = new mysqli("localhost","root","root","AuctionDB");
    $cat_name_query = $mysqli_cat_name->prepare("SELECT categoryDescription FROM Categories WHERE categoryID = ?");
    $cat_name_query->bind_Param("s", $cat_fy);
    $cat_name_query->execute();
    $cat_get = $cat_name_query->get_result();
    $cat_name_array = $cat_get->fetch_assoc();
    $cat_name = $cat_name_array['categoryDescription'];



    echo "<h3 class='my-3'>More From $cat_name</h3>";
    $mysqli_1 = new mysqli("localhost","root","root","AuctionDB");
    $mysqli_2 = new mysqli("localhost","root","root","AuctionDB");

    $for_you = $mysqli_1->prepare("SELECT i.itemID, i.title, i.description, b.bidValue, i.closeDate, b.bidID FROM Items i JOIN Bids b on i.highestbidID = b.bidID WHERE i.itemID IN (SELECT b.ItemID FROM Bids b GROUP BY (b.ItemID) HAVING COUNT(b.bidID) > 2 ORDER BY COUNT(b.bidID) DESC) AND i.catID = ?");
    $for_you-> bind_Param("s", $cat_fy);
    $for_you->execute();

    $fy_top_3 = $mysqli_2->prepare("SELECT i.itemID, COUNT(b.bidID) FROM Bids b JOIN Items i ON i.itemID = b.itemID WHERE i.catID = ? GROUP BY(i.itemID) ORDER BY COUNT(b.bidID) DESC LIMIT 3");
    $fy_top_3-> bind_Param("s", $cat_fy);
    $fy_top_3->execute();

    $get_for_you = $for_you->get_result();

    $get_fy_top_3 = $fy_top_3->get_result();

    while($row_search = $get_for_you->fetch_assoc()) { // Add a clause in this while loop that says 'AND b.bidderUserID = userID'

       $item_id_search = $row_search['itemID'];

        foreach($get_fy_top_3 as $array){
           if($array['itemID'] == $item_id_search){


              $stmt2_search = "SELECT COUNT(bidID) as c FROM Bids WHERE itemID = $item_id_search";
              $result2_search = mysqli_query($connection, $stmt2_search);
              $row2_search = mysqli_fetch_array($result2_search);

               $title = $row_search['title'];
               $description = $row_search['description'];
               $current_price = $row_search['bidValue'];
               $num_bids = $row2_search['c'];
               $end_time = new DateTime($row_search['closeDate']);

                // This uses a function defined in utilities.php

                print_listing_li($item_id_search, $title, $description, $current_price, $num_bids, $end_time);
            }
        }
    }

    echo "<h3 class='my-3'>What your Competitors are Looking at</h3>";


    $current_userID = strval($current_userID);

    $mysqlici = new mysqli("localhost","root","root","AuctionDB");

    $competitors = $mysqlici->prepare("SELECT itemID, COUNT(bidID) FROM Bids WHERE itemID IN(SELECT DISTINCT(itemID) FROM Bids
    WHERE bidderUserID IN (SELECT DISTINCT(bidderUserID) FROM Bids WHERE itemID IN(SELECT DISTINCT(itemID) FROM Bids
    WHERE bidderUserID = ? )) AND itemID NOT IN (SELECT DISTINCT(itemID) FROM Bids
    WHERE bidderUserID = ?)) GROUP BY(itemID) ORDER BY COUNT(bidID) DESC LIMIT 3;");
    $competitors->bind_Param("ss", $current_userID,$current_userID);


    $competitors->execute();
    $competitor_items = $competitors->get_result();




    $count_above_three = "SELECT i.itemID, i.title, i.description, b.bidValue, i.closeDate, b.bidID FROM Items i JOIN Bids b on i.highestbidID = b.bidID WHERE b.ItemID IN (SELECT b.ItemID FROM Bids b GROUP BY (b.ItemID) HAVING COUNT(b.bidID) >= 3 ORDER BY COUNT(b.bidID) DESC)";
    $count_above_three_query = mysqli_query($connection, $count_above_three);

    while ($row_search = $count_above_three_query->fetch_assoc()) { // Add a clause in this while loop that says 'AND b.bidderUserID = userID'

       $item_id_search = $row_search['itemID'];

           foreach($competitor_items as $array){
           if($array['itemID'] == $item_id_search){

          $stmt2_search = "SELECT COUNT(bidID) as c FROM Bids WHERE itemID = $item_id_search";
              $result2_search = mysqli_query($connection, $stmt2_search);
              $row2_search = mysqli_fetch_array($result2_search);

               $title = $row_search['title'];
               $description = $row_search['description'];
               $current_price = $row_search['bidValue'];
               $num_bids = $row2_search['c'];
               $end_time = new DateTime($row_search['closeDate']);

                // This uses a function defined in utilities.php

                print_listing_li($item_id_search, $title, $description, $current_price, $num_bids, $end_time);
        }
        }
        }

      // TODO: Loop through results and print them out as list items.
      }
      }
   else{
      echo '<button style="color:white;background:green;margin-top:60px;margin-left:60px;" type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Please Login</button>';
  

}
?>


