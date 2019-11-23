<?php

function get_id_from_email($email) {
  require 'database.php';
  $autofill_query = "SELECT id from users where  username LIKE CONCAT('%', :email, '%')";
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

if(isset($_POST['Send'])) {
    header("Location: /message.php");
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

// echo "<table border='1'>
// <tr>
// <th>From</th>
// <th>To</th>
// <th>Message</th>
// <th>Time</th>
// </tr>";

// foreach ($row as $val){ 
//     echo "<tr>";
//     echo "<td>" . $val['u2'] . "</td>";
//     echo "<td>" . $val['u1'] . "</td>";
//     echo "<td>" . $val['message'] . "</td>";
//     echo "<td>" . $val['time'] . "</td>";
// } 

// echo "</table>";
?>

<script type="text/javascript">
  function submitForm(action) {
    var form = document.getElementById('form1');
    form.action = action;
    form.submit();
  }
</script>

<!DOCTYPE html>
<html>
<title>W3.CSS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    * {
  box-sizing: border-box;
}

/* Style the search field */
form.example input[type=text] {
  padding: 10px;
  font-size: 17px;
  border: 1px solid grey;
  float: left;
  width: 80%;
  background: #f1f1f1;
}

/* Style the submit button */
form.example button {
  float: left;
  width: 20%;
  padding: 10px;
  background: #2196F3;
  color: white;
  font-size: 17px;
  border: 1px solid grey;
  border-left: none; /* Prevent double borders */
  cursor: pointer;
}

form.example button:hover {
  background: #0b7dda;
}

/* Clear floats */
form.example::after {
  content: "";
  clear: both;
  display: table;
}
</style>
<body>
  <br>
  <br>


<br>
<a href="main.php" style="color:#0000FF">Back to main page</a>
<br><br><br>
  
  
  <!--<form id="form1" >-->
<!--    <input type="button" onclick="submitForm('list_messages.php')" value="List" style="padding: 50x 100px"/>
-->   
<!--<input type="button" onclick="submitForm('message.php')" value="Send" style="padding: 50x 100px"/>-->
    <!-- The form -->
    <form class="example" action="messaging.php" method = "POST">

      <input type="text" placeholder="Search.." name="search" value="<?=$_POST['search']?>"> 
      <input type="submit" value="Submit" name="submit"><!--<i class="fa fa-search"></i> -->
      <br>
    </form>
    <form class ='write_message' action = "message.php" method="POST">
      <input type="submit" action="message.php" method = "POST" name = "Send" value="Send A Message" style="padding: 50x 100px"/>
  </form>
</body>

<div class="w3-container">
  <h2>All My Messages</h2>
  <table class="w3-table-all w3-hoverable" style="table-layout: fixed; width: 100%">
    <tr>
        <th>From</th>
        <th>To</th>
        <th>Message</th>
        <th>Time</th>
    </tr>
    <?php
    foreach ($row as $val){ ?>
        <tr>
        <td>"<?=$val['u2']?>"</td>
        <td>"<?=$val['u1']?>"</td>
        <td style="word-wrap: break-word">"<?=$val['message']?>"</td>
        <td>"<?=$val['time']?>"</td>
        </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>




