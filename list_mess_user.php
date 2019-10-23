<?php

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

$description_search = "";

if(isset($_POST['search'])) {
    $description_search = $_POST['search'];
}

$autofill_query = "";

$id = $_SESSION['user_id'];

if($description_search == ""){
    require 'database.php';
    $autofill_query = "select t1.username as u1, t2.username as u2, m.message, m.time from messages m, users t1, users t2 where (m.user_to = :u_id or m.user_from = :u_id) and t1.id = m.user_to and t2.id = m.user_from order by time desc;";
    $records = $conn->prepare($autofill_query);
    $records->bindParam(':u_id', $id);
}
else{
    $r_id = get_id_from_email($description_search);
    require 'database.php';
    $autofill_query = "select t1.username as u1, t2.username as u2, m.message, m.time from messages m, users t1, users t2 where ((m.user_to = :u_id and m.user_from = :r_id) or (m.user_to = :r_id and m.user_from = :u_id)) and t1.id = m.user_to and t2.id = m.user_from order by time desc;";
    $records = $conn->prepare($autofill_query);
    $records->bindParam(':u_id', $id);
    $records->bindParam(':r_id', $r_id);
}

$records->execute();
$row = $records->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>
<tr>
<th>From</th>
<th>To</th>
<th>Message</th>
<th>Time</th>
</tr>";

foreach ($row as $val){ 
    echo "<tr>";
    echo "<td>" . $val['u1'] . "</td>";
    echo "<td>" . $val['u2'] . "</td>";
    echo "<td>" . $val['message'] . "</td>";
    echo "<td>" . $val['time'] . "</td>";
} 

echo "</table>";
?>