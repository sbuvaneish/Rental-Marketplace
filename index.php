<?php

session_start();
$_SESSION = array();
session_destroy();

?>

<!DOCTYPE html>
<html>
<head>
	<title>Welcome to your Web App</title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
</head>
	<style>
	img {
	  opacity: 0.2;
  	  filter: alpha(opacity=25);
	}
	.centered {
	  position: absolute;
	  top: 50%;
	  left: 50%;
	  transform: translate(-50%, -50%);
	}
</style>

<body>
	<div class="container">
		<img src="img.jpg" alt="Pineapple" width="1500" height="818">
		<div class="centered">
			<a href="/">Rental Marketplace</a>
			
	
				<h1>Please Login or Register</h1>
				<a href="login.php">Login</a> or
				<a href="register.php">Register</a>
	
			
		</div>
	</div>
</body>
</html>


