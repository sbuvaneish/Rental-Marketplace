<?php

session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
}

unset($_SESSION['product_id']);

if(isset($_SESSION['insertion_flag'])) {
  unset($_SESSION['insertion_flag']);
  echo '<script type="text/javascript">alert("Product successfully created!");</script>';
}

if(isset($_SESSION['updation_flag'])){
  unset($_SESSION['updation_flag']);
  echo '<script type="text/javascript">alert("Product successfully updated!");</script>';
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <style>
    /* Set height of the grid so .sidenav can be 100% (adjust if needed) */
    .row.content {height: 1500px}
    
    /* Set gray background color and 100% height */
    .sidenav {
      background-color: #f1f1f1;
      height: 100%;
    }
    
    /* Set black background color, white text and some padding */
    footer {
      background-color: #555;
      color: white;
      padding: 15px;
    }
    
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height: auto;} 
    }
    
    
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row content">
    <div class="col-sm-3 sidenav">
      <span style="float:right;color:green">Welcome, <?=$_SESSION['email']?></span>
      <a href="main.php"><h4 style="color:green">Rental Marketplace</h4></a>
      <ul class="nav nav-pills nav-stacked">
        <li class=<?= ($_GET['file'] == 'upload.php') ? 'active' : ''?>><a href="upload.php">Upload Product</a></li>
        <li class=<?= ($_GET['file'] == 'view.php') ? 'active' : ''?>><a href="view.php">View My Products</a></li>
        <li class=<?= ($_GET['file'] == 'search.php') ? 'active' : ''?>><a href="main.php?file=search.php">Search Product</a></li>
        <li class=<?= ($_GET['file'] == 'messaging.php') ? 'active' : ''?>><a href="messaging.php">Message</a></li>
        <li class=<?= ($_GET['file'] == 'index.php') ? 'active' : ''?>><a href="index.php">Logout</a></li>
        </ul><br>
    </div>

    <div class="col-sm-9">
      <?php include $_GET['file'] ?>
    </div>
  </div>
</div>


</body>
</html>
