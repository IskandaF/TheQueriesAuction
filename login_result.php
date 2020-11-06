<!-- 

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect. -->


<?php
    session_start();
require_once ("pdo.php");

if ( isset($_POST['email']) && isset($_POST['password'])  ) {
    
 //          $sql = "SELECT password FROM Users 
 //        WHERE password = :pw";


 //    $stmt = $pdo->prepare($sql);
 //    $stmt->execute(array(
        
 //        ':pw' => $_POST['password']));
 //    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 //    var_dump($row);
 //    if ( $row === FALSE ) {
       
 //       $_SESSION["error"]="Incorrect password";
 //       error_log("Session ID= ".session_id()."  Error= ".$_SESSION["error"]);
 //       header("location:index.php");
 //       return;
 //    } else {
 //       $_SESSION["success"]="Login Sucess";
 //       error_log("Session ID= ".session_id()."  Error= ".$_SESSION["success"]);
 //       session_start();
	// $_SESSION['logged_in'] = true;
	// $_SESSION['username'] = $_POST["email"];
	// $_SESSION['account_type'] = "buyer";
	// echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');

	// Redirect to index after 5 seconds
	echo "Logged in";
	header("refresh:2;url=index.php");
	$_SESSION["success"]="You're logged in now";
	return;

    }

    else{
    	echo("Doesn't work");
    }

    ;





?>