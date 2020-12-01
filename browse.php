<?php include_once("header.php");
include_once("mysqli.php");

if (session_status() == PHP_SESSION_NONE){
  session_start();
};

?>

<?php require("utilities.php")?>

<?php

$query = "SELECT userID FROM Users LIMIT 1";
$result = mysqli_query($connection,$query)
or die('Error making select users query' .
mysql_error());

$row = mysqli_fetch_array($result);
while ($row = mysqli_fetch_array($result)) {
    echo(htmlentities($row["userID"]));

}
?>

<?php
if (isset($_SESSION["success"])){
  echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
  unset($_SESSION['success']);
}
if (isset($_SESSION["fail"])){
  echo '<p style="color:red">'.$_SESSION['fail']."</p>\n";
  unset($_SESSION['fail']);
}
?>
<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for = "keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" id = "keyword" name = "keyword" placeholder="Search for anything">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" id="cat" name="cat">
          <option selected value="%">All categories</option>
          <option value="CAT1" <?php if ((isset($_GET['cat'])) && ($_GET['cat'] == 'CAT1')) { ?>selected="true" <?php }; ?>>Class A Pharmaceuticals</option>
          <option value="CAT2" <?php if ((isset($_GET['cat'])) && ($_GET['cat'] == 'CAT2')) { ?>selected="true" <?php }; ?>>Class B Pharmaceuticals</option>
          <option value="CAT3" <?php if ((isset($_GET['cat'])) && ($_GET['cat'] == 'CAT3')) { ?>selected="true" <?php }; ?>>Class C Pharmaceuticals</option>
          <option value="CAT4" <?php if ((isset($_GET['cat'])) && ($_GET['cat'] == 'CAT4')) { ?>selected="true" <?php }; ?>>Class D Pharmaceuticals</option>
          <option value="CAT5" <?php if ((isset($_GET['cat'])) && ($_GET['cat'] == 'CAT5')) { ?>selected="true" <?php }; ?>>Class E Pharmaceuticals</option>
          <option value="CAT6" <?php if ((isset($_GET['cat'])) && ($_GET['cat'] == 'CAT6')) { ?>selected="true" <?php }; ?>>Electronics</option>
          <option value="CAT7" <?php if ((isset($_GET['cat'])) && ($_GET['cat'] == 'CAT7')) { ?>selected="true" <?php }; ?>>Equipment</option>
          <option value="CAT8" <?php if ((isset($_GET['cat'])) && ($_GET['cat'] == 'CAT8')) { ?>selected="true" <?php }; ?>>Other</option>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by" name="order_by">
          <option selected value="alphabetical">Products A-Z</option>
          <option value="pricelow" <?php if ((isset($_GET['order_by'])) && ($_GET['order_by']  == 'pricelow')) { ?>selected="true" <?php }; ?>>Price (low to high)</option>
          <option value="pricehigh" <?php if ((isset($_GET['order_by'])) && ($_GET['order_by'] == 'pricehigh')) { ?>selected="true" <?php }; ?>>Price (high to low)</option>
          <option value="date" <?php if ((isset($_GET['order_by'])) && ($_GET['order_by'] == 'date')) { ?>selected="true" <?php }; ?>>Soonest expiry</option>
          <option value="recent" <?php if ((isset($_GET['order_by'])) && ($_GET['order_by'] == 'recent')) { ?>selected="true" <?php }; ?>>Recently listed</option>

        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>

<div class="">
      <div class="form-inline">
        <label class="mx-2" for="results_pp">Results per Page</label>
        <select class="form-control" id="results_pp" name="results_pp">
          <option selected value="10">10</option>
          <option value="30" <?php if ((isset($_GET['results_pp'])) && ($_GET['results_pp'] == '30')) { ?>selected="true" <?php }; ?>>30</option>
          <option value="100" <?php if ((isset($_GET['results_pp'])) && ($_GET['results_pp'] == '100')) { ?>selected="true" <?php }; ?>>100</option>
        </select>
      </div>
    </div>
      </form>
</div> <!-- end search specs bar -->


</div>

<?php
  // Retrieve these from the URL
  if (!isset($_GET['keyword'])) {
    // TODO: Define behavior if a keyword has not been specified.
    $keyword = "";
  }
  else {
    $keyword = $_GET['keyword'];
    $keyword = strtolower($keyword);
  }

  if (!isset($_GET['cat'])) {
    $category = "%%";

  }
  else {
    $category = $_GET['cat'];

  }

  if (!isset($_GET['order_by'])) {
    // TODO: Define behavior if an order_by value has not been specified.
    $ordering = "i.title ASC";

  }
  else {
   if($_GET['order_by'] == "alphabetical"){
 $ordering = "i.title ASC";
 }
   if($_GET['order_by'] == "pricelow"){
 $ordering = "b.bidvalue ASC";
 }
 if($_GET['order_by'] == "pricehigh"){
 $ordering = "b.bidvalue DESC";
}
 if($_GET['order_by'] == "date"){
 $ordering = "i.closeDate ASC";
 }
 if($_GET['order_by'] == "recent"){
   $ordering = "i.itemID DESC";
 }
;
  }

  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }

  if (!isset($_GET['results_pp'])) {
      $limit = "10";
    }
    else {
      $limit = (int)$_GET['results_pp'];
    }
  $offset = $limit * ((int) $curr_page-1);

//echo $keyword . '<br>';
//echo $category. '<br>';
//echo $ordering. '<br>';
// Below, we count the total number of items that match the users search

 $mysqli = new mysqli("localhost","root","root","AuctionDB");
 if (!isset($_GET['keyword'])){
  $_GET['keyword']="%%";
}
 $search_count = $mysqli->prepare("SELECT i.itemID, i.title, i.description, b.bidValue, i.closeDate, b.bidID FROM Items i, Bids b
 WHERE i.highestbidID = b.bidID AND i.title LIKE ? AND i.catID LIKE ?;");
 $keyword_SQL = "%" . $_GET['keyword'] . "%";
 $search_count -> bind_Param("ss", $keyword_SQL, $category);
 $search_count -> execute();
 $search_got_count = $search_count->get_result();
 echo mysqli_num_rows($search_got_count) . ' results found.';

/* The below retrieves all rows where the item title contains the phrase the user searches,
  belong to the category the user selects and are ordered by the users preference from the options. If no category
  or search term is entered then they are, by default, set to %% which when used with the WHERE + LIKE
  Clause selects all remaining options. Pagination means only the user selected number of items are return by each query,
   not the total number of items */

 $search = $mysqli->prepare("SELECT i.itemID, i.title, i.description, b.bidValue, i.closeDate, b.bidID FROM Items i, Bids b
 WHERE i.highestbidID = b.bidID AND i.title LIKE ? AND i.catID LIKE ? ORDER BY $ordering LIMIT $limit OFFSET $offset;");



 $keyword_SQL = "%" . $_GET['keyword'] . "%";

 $search -> bind_Param("ss", $keyword_SQL, $category);

 $search -> execute();

 $search_got = $search->get_result();


 if(empty(mysqli_num_rows($search_got))){
 echo ' Sorry, there are no listings that match your search, please alter your search criteria or return to:  <a class="page-link" href = "browse.php">Browse Catalog</a>';
 }




 while ($row_search = $search_got->fetch_assoc()) { // Add a clause in this while loop that says 'AND b.bidderUserID = userID'

     $item_id_search = $row_search['itemID'];


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
 $search_got->close();
 $search->close();



  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */




  $num_results = mysqli_num_rows($search_got_count);
  $results_per_page = 10;
  $max_page = ceil($num_results / $limit);


?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

<?php



// Elina - copy this code to use in mylistings.php


// end of browse code for mylistings.php

?>

<?php
  // Demonstration of what listings will look like using dummy data.

  /*
  $item_id = "516";
  $title = "Different title";
  $description = "Very short description.";
  $current_price = 13.50;
  $num_bids = 3;
  $end_date = new DateTime('2020-11-02T00:00:00');
  print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
  */
?>

</ul>

<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">

<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }

  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);

  if ($curr_page != 1 and $num_results != 0) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');

  }

  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }

    // Do this in any case
    echo('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }

  if ($curr_page != $max_page and $num_results != 0) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


</div>



<?php include_once("footer.php");
include_once("sales_function.php");
?>
