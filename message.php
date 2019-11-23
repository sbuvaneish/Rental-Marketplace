<?php

session_start();

if(!isset($_SESSION['user_id'])) {
	header("Location: /login.php");
}

require 'database.php';
require_once('private_messaging_system.php');

function get_id_from_email($email) {
  global $conn;
  $autofill_query = "SELECT id from users where  username = :email";
  $records = $conn->prepare($autofill_query);
  $records->bindParam(':email', $email);
  $records->execute();
  $view_results = $records->fetchAll(PDO::FETCH_ASSOC);
  return $view_results[0]["id"];
}

$message = "";

if(isset($_POST['sendMessage'])) {
	$rec_id = get_id_from_email($_POST['myEmail']);
	
	if($rec_id == null) {
		$message = "Invalid Email";
	}
	
	else if(empty($_POST['myMessage'])) {
		$message = "Empty Message";
	}
	
	if(empty($message)) {
		$obj = new Private_messaging_system; 
		$obj->send_message($rec_id, $_POST['myMessage']);
		echo "Message Sent";
		$url =  "https://".$_SERVER['HTTP_HOST']."/messaging.php";
		header("Refresh: 2;url=".$url);
	}
	
}

?>

<!DOCTYPE html>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

<br>
<a href="main.php">Back to main page</a>
<br><br><br>

	<div>
    	<p style="color:blue">
    		 <?= $message ?>
    	</p>
    </div>

<div class="container">
	<form method="POST" action="message.php">
		<div><label>Receiver Email:</label></div>
		<div><input type="text" name="myEmail" class="form-control" placeholder="email" value="<?= $_POST['myEmail'] ?>"/></div>
		
		<div><label>Message:</label></div>
		<div><textarea cols="40" rows="5" name="myMessage" class="form-control"></textarea></div>
		<div class="float-right mt-2">
			<input type="submit" value="send" name="sendMessage" class="btn btn-primary" />
		</div>
	</form>
</div>

