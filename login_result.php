<!--
// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.
// For now, I will just set session variables and redirect. -->


<?php
    if (session_status() == PHP_SESSION_NONE){
  session_start();
};
require_once ("mysqli.php");

if ($_SERVER["REQUEST_METHOD"] == "POST"){
		if ((isset($_POST["email"],$_POST["password"])) && ((strlen($_POST["email"])>0)&&(strlen($_POST["password"])>0)))  {

          $query = "SELECT email,password FROM Users
        WHERE password = ? AND email=?";



$stmt = $connection->prepare("SELECT * FROM Users WHERE email = ?");
// $stmt->bind_param(":email",$email);
$stmt->bind_param("s",$email);

$email=strval($_POST['email']);
$password=$_POST['password'];

$stmt->execute();
$result = $stmt->get_result();

$row = mysqli_fetch_array($result);
if(password_verify($password, $row["password"])){
	echo "Logged in";
	header("refresh:0.01;url=index.php");
	$_SESSION["success"]="You're logged in now";
    $_SESSION['logged_in'] = true;
	$_SESSION['username'] = $_POST["email"];
	$_SESSION['userID'] = $row["userID"];
	//$_SESSION['account_type'] = "buyer";
	return;
}

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

else{
    header("location:browse.php");
    	   $_SESSION["fail"]="Wrong email or password";


    }
}}


else{
	"Nothing";
}


?>
