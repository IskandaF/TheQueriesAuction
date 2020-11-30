
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// TO DO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation
// options.

require_once ("mysqli.php");
// require_once ("sendEmail.php");


// Check if email is registered


if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if ((isset($_POST["email"],$_POST["password"])) && ((strlen($_POST["email"])>0)&&(strlen($_POST["password"])>0)))  {

	  $query = "SELECT email,password FROM Users
	WHERE password = ? AND email=?";



$stmt = $connection->prepare("SELECT * FROM Users WHERE email = ?");
// $stmt->bind_param(":email",$email);
$stmt->bind_param("s",$email);

$email=strval($_POST['email']);

$stmt->execute();
$result = $stmt->get_result();

$row = mysqli_fetch_array($result);
if($row){
$_SESSION["fail"]="This user already exists, please log in";
header("Location:register.php");
return;
}
	}};

if ($_SERVER["REQUEST_METHOD"] == "POST"){

	
	/*$stmt1 = "SELECT * FROM Users WHERE email = ($_POST["email"])";*/
	/*$result1 = mysqli_query($stmt1);*/
		if ((isset($_POST["email"],$_POST["password"])) && ((strlen($_POST["email"])>0)&&(strlen($_POST["password"])>0))) {
			/*if ((mysqli_num_rows($result1))) >= 1 {
				echo 'This email already exists.' ;
			} else {*/
		$password = $_POST['password'];
		$cpassword = $_POST['passwordConfirmation'];
		if ($password==$cpassword){
	    // $stmt = $connection->prepare("INSERT INTO Users (email, password) VALUES (?, ?)");
	    // $stmt->bind_param("ss", $email, $password);
	    $email=$_POST['email'];
	    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
	    // $password=$_POST['password'];
	    $stmt="INSERT INTO Users (email, password) VALUES ('".$email."', '".$password."')";
	    if ($connection->query($stmt) === TRUE) {
				echo "New record created successfully. <br>";
          header("refresh:0.01;url=browse.php");
		} else {
		  echo "Error: " . $stmt . "<br>" . $connection->error;
		}
		$connection->close();
		echo "It's all fine.";
	    $_SESSION['success'] = 'Record Added';
	    // header( 'Location: login.php' ) ;
		return;
	
	}
		else{
			$_SESSION["fail"]="Passwords don't match";
			header('Location: register.php');
			$_SESSION["fail"]="Passwords don't match";

			exit();
			return;
			$_SESSION["fail"]="Passwords don't match";
			

		};
	}
	else{
		
		$_SESSION["fail"]="Please enter values";
		header('Location: register.php') ;
		$_SESSION["fail"]="Please enter values";

		
		return;
		$_SESSION["fail"]="Passwords don't match";

	}


}

else{
	echo("Doesn't work");
}
;
?>
