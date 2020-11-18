<?php include_once("header.php");


include_once("mysqli.php");
if (session_status() == PHP_SESSION_NONE){
session_start();
}
?>
<?php require("utilities.php")?>
<?php

$query = "SELECT userID FROM Users";
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
if (isset($_SESSION["fail"])) {
  echo '<p style="color:red">'.$_SESSION['fail']."</p>\n";;
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
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" id="keyword" placeholder="Search for anything">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" id="cat">
          <option selected value="all">All categories</option>
          <option value="fill">Fill me in</option>
          <option value="with">with options</option>
          <option value="populated">populated from a database?</option>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by">
          <option selected value="pricelow">Price (low to high)</option>
          <option value="pricehigh">Price (high to low)</option>
          <option value="date">Soonest expiry</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>

<?php
  // Retrieve these from the URL
  if (!isset($_GET['keyword'])) {
    // TODO: Define behavior if a keyword has not been specified.
  }
  else {
    $keyword = $_GET['keyword'];
  }

  if (!isset($_GET['cat'])) {
    // TODO: Define behavior if a category has not been specified.
  }
  else {
    $category = $_GET['cat'];
  }

  if (!isset($_GET['order_by'])) {
    // TODO: Define behavior if an order_by value has not been specified.
  }
  else {
    $ordering = $_GET['order_by'];
  }

  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }

  /* TODO: Use above values to construct a query. Use this query to
     retrieve data from the database. (If there is no form data entered,
     decide on appropriate default value/default query to make. */

  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */



  //Elina - copy this query to use in mylistings.php
  $stmt1 = "SELECT i.itemID, i.title, i.description, b.bidValue, i.closeDate, b.bidID FROM Items i, Bids b WHERE i.highestbidID = b.bidID ORDER BY itemID DESC";
  $result1 = mysqli_query($connection, $stmt1);

  echo mysqli_num_rows($result1) . ' results found.';


  $num_results = mysqli_num_rows($result1);
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

<?php



// Elina - copy this code to use in mylistings.php
  while ($row1 = $result1->fetch_assoc()) { // Add a clause in this while loop that says 'AND b.bidderUserID = userID'
    $item_id = $row1['itemID'];

    $stmt2 = "SELECT COUNT(bidID) as c FROM Bids WHERE itemID = $item_id";
    $result2 = mysqli_query($connection, $stmt2);
    $row2 = mysqli_fetch_array($result2);

    $title = $row1['title'];
    $description = $row1['description'];
    $current_price = $row1['bidValue'];
    $num_bids = $row2['c'];
    $end_time = new DateTime($row1['closeDate']);

    // This uses a function defined in utilities.php
    print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_time);
  };

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

  if ($curr_page != 1) {
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

  if ($curr_page != $max_page) {
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



<?php include_once("footer.php")?>
