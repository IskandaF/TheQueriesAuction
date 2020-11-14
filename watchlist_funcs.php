 <?php
 include_once("mysqli.php");
 session_start();


if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
//$item_id = $_POST['arguments']; // This just returns the datatype ('Array'), not sure how to extract the variable itself
$item_id = $_SESSION['itemID'];  // This works though
$userID = $_SESSION['userID'];

if ($_POST['functionname'] == "add_to_watchlist") {
  //TODO: Update database and return success/failure.
    $addwatchlistquery = "INSERT INTO Watchlist (userID, itemID)
             VALUES ('".$userID."', '".$item_id."')";

    $connection->query($addwatchlistquery);

  $res = "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
    $removewatchlistquery = "DELETE FROM Watchlist WHERE userID = '".$userID."'
                            AND itemID = '".$item_id."' ";
    $connection->query($removewatchlistquery);

  $res = "success";
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>
