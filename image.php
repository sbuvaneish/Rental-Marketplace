<?php
  $host = "us-cdbr-iron-east-02.cleardb.net";
  $username = "b7e32c44881d1e";
  $password = "5550f55b";
  $dbname = "heroku_2f6114e386eb6eb";
  $dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $dbh->exec("SET NAMES utf8");
  $q = $dbh->prepare("select image from products where product_id = 2");
  $q->execute();
  $row = $q->fetch(PDO::FETCH_BOTH);
  echo '<img src="data:image/jpeg;base64,' . $row["image"] . '" />';
?>
