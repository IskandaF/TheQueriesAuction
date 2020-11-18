

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

<?php
require_once ("mysqli.php"); 
// require_once ("sendEmail.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST"){
		if ((isset($_POST["email"],$_POST["password"])) && ((strlen($_POST["email"])>0)&&(strlen($_POST["password"])>0))) {
		$_SESSION['success'] = 'The values were set';
	    // $stmt = $connection->prepare("INSERT INTO Users (email, password) VALUES (?, ?)");
	    // $stmt->bind_param("ss", $email, $password);
	    $email=$_POST['email'];
	    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
	    // $password=$_POST['password'];
	    $stmt="INSERT INTO Users (email, password) VALUES ('".$email."', '".$password."')";
	    if ($connection->query($stmt) === TRUE) {
		  echo "New record created successfully";

		} else {
		  echo "Error: " . $stmt . "<br>" . $connection->error;
		}

		$connection->close();
	    $_SESSION['success'] = 'Record Added';
	    header( 'Location: browse.php' ) ;

	    return;
	}
	else{
		header('Location: registration.php') ;
		$_SESSION['fail']="Please enter values";
	}


}

else{
	echo("Doesn't work");
}

?>