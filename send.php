<?php

session_start();


require_once('private_messaging_system.php');

function get_id_from_email($email) {
  require 'database.php';
  $autofill_query = "SELECT id from users where  username = :email";
  $records = $conn->prepare($autofill_query);
  $records->bindParam(':email', $email);
  $records->execute();
  $view_results = $records->fetchAll(PDO::FETCH_ASSOC);
  return $view_results[0]["id"];
}


$rec_email = $_POST['myEmail'];
$message = $_POST['myMessage'];
$sender_id = $_SESSION['user_id'];
$rec_id = get_id_from_email($rec_email);

if($rec_id == null){
  echo "Invalid Email ID.";
}

else{
  $obj = new Private_messaging_system; 
  $obj->send_message($rec_id, $message);
  echo "Message sent successfully";
}
$url =  "https://".$_SERVER['HTTP_HOST']."/messaging.php";
header("Refresh: 2;url=".$url);
?>
