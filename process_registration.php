
<?php


// TO DO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation
// options.

require_once ("mysqli.php");
// require_once ("sendEmail.php");




if ($_SERVER["REQUEST_METHOD"] == "POST"){
	/*$stmt1 = "SELECT * FROM Users WHERE email = ($_POST["email"])";*/
	/*$result1 = mysqli_query($stmt1);*/
		if ((isset($_POST["email"],$_POST["password"])) && ((strlen($_POST["email"])>0)&&(strlen($_POST["password"])>0))) {
			/*if ((mysqli_num_rows($result1))) >= 1 {
				echo 'This email already exists.' ;
			} else {*/
		$_SESSION['success'] = 'The values were set';
	    // $stmt = $connection->prepare("INSERT INTO Users (email, password) VALUES (?, ?)");
	    // $stmt->bind_param("ss", $email, $password);
	    $email=$_POST['email'];
	    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
	    // $password=$_POST['password'];
	    $stmt="INSERT INTO Users (email, password) VALUES ('".$email."', '".$password."')";
	    if ($connection->query($stmt) === TRUE) {
		  echo "New record created successfully. ";

		} else {
		  echo "Error: " . $stmt . "<br>" . $connection->error;
		}
	/*}*/

		$connection->close();
		echo "It's all fine. ";
	    $_SESSION['success'] = 'Record Added';
			header("refresh:2;url=index.php");
	    // header( 'Location: login.php' ) ;

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
