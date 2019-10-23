<?php
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

session_start();

$rec_email = $_POST['myEmail'];
$message = $_POST['myMessage'];
$sender_id = $_SESSION['user_id'];
$rec_id = get_id_from_email($rec_email);

$obj = new Private_messaging_system; 
$obj->send_message($rec_id, $message);
?>
