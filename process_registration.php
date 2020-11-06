

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

<?php
require_once ("pdo.php");
session_start(); 
if ($_SERVER["REQUEST_METHOD"] == "POST"){
		echo($_POST['submit']);
		echo ($_POST["email"]);
		$_SESSION['success'] = 'The values were set';
	    $sql = "INSERT INTO Users (email, password)
	              VALUES (:email, :password)";
	    $stmt = $pdo->prepare($sql);
	    $_SESSION['success'] = 'The values were prepared';
	    $stmt->execute(array(
	        
	        ':email' => $_POST["email"],
	        ':password' => $_POST["password"]
	    	));
	    $_SESSION['success'] = 'Record Added';
	    header( 'Location: browse.php' ) ;
	    return;


}

else{
	echo("Doesn't work");
}

?>