<?php

session_start();

if(isset($_SESSION['user_id'])) {
	header("Location: /main.php");
}

require 'database.php';

if(!empty($_POST['email']) && !empty($_POST['password'])):
	
	$records = $conn->prepare('SELECT id,username,password FROM users WHERE username = :email');
	$records->bindParam(':email', $_POST['email']);
	$records->execute();
	$results = $records->fetchAll(PDO::FETCH_ASSOC);

	$message = '';

	if(empty($_POST['email']) or empty($_POST['password'])) {
		$message = 'Enter email and password';
	}
	else if(count($results) == 0) {
		$message = 'User does not exist';
	}
	else if(password_verify($_POST['password'], $results[0]['password'])){
		$_SESSION['email'] = $_POST['email'];
		$_SESSION['user_id'] = $results[0]['id'];
		header("Location: /main.php");
	} 
	else {
		$message = 'Sorry, those credentials do not match';
	}

endif;

?>

<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<form class="form-horizontal" action='' method="POST">
  <fieldset>
    <div id="legend">
      <legend class="" style="color:green">Rental Marketplace: It's fun to rent</legend>
    </div>
    
    <div>
    	<p style="color:blue">
    		 <?= $message ?>
    	</p>
    </div>
 
    <div class="control-group">
      <!-- E-mail -->
      <label class="control-label" for="email">E-mail</label>
      <div class="controls">
        <input type="text" id="email" name="email" placeholder="" class="input-xlarge" value=<?= $_POST['email'] ?>>
      </div>
    </div>
 
    <div class="control-group">
      <!-- Password-->
      <label class="control-label" for="password">Password</label>
      <div class="controls">
        <input type="password" id="password" name="password" placeholder="" class="input-xlarge">
      </div>
    </div>
    
    <div class="control-group">
      <!-- Button -->
      <div class="controls">
        <button class="btn btn-success">Login</button>
      </div>
    </div>
    
    
    <div class="control-group">
    	<div class="controls">
    		<a href="register.php">New user? Register here</a>
    	</div>
    </div>
    
  </fieldset>
</form>
