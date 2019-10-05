<?php
  $host = "$servername = "us-cdbr-iron-east-02.cleardb.net";
  $username = "b7e32c44881d1e";
  $password = "5550f55b";
  $dbname = "heroku_2f6114e386eb6eb";";
  $dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $dbh->exec("SET NAMES utf8");
  $q = $dbh->prepare("select image from images where id = 2");
  $q->execute(array(':id'=>$_GET['id']));
  $row = $q->fetch(PDO::FETCH_BOTH);
  echo '<img src="data:image/png;base64,' . $row["image"] . '" />';
?>
