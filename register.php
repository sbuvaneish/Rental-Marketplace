<?php

require 'database.php';

$message = '';

if(!empty($_POST['email'])):
	
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$message = "Invalid email";
	}
	else if(strlen($_POST['password']) < 4) {
		$message = "Password must be atleast 4 characters long";
	}
	else if(strcmp($_POST['password'], $_POST['password_confirm']) != 0) {
		$message = "Passwords do not match";
	}
	else {
		
		//check if the user is already registered
		$check_query = "SELECT * FROM users where username = :email";
		$records = $conn->prepare($check_query);
		$records->bindParam(':email', $_POST['email']);
		$records->execute();
		$results = $records->fetchAll(PDO::FETCH_ASSOC);
		
		if(count($results) == 0)
		{
			// Enter the new user in the database
			$insert_query = "INSERT INTO users (username, password) VALUES (:email, :password)";
			$stmt = $conn->prepare($insert_query);
		
			$stmt->bindParam(':email', $_POST['email']);
			$stmt->bindParam(':password', password_hash($_POST['password'], PASSWORD_BCRYPT));
		
			if( $stmt->execute() ):
				$message = 'Successfully created new user';
			else:
				$message = 'Sorry there must have been an issue creating your account';
			endif;
		}
		else
		{
			$message = 'User already exists';
		}	
		
		
	}

endif;

?>

<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<body background="">
	<form class="form-horizontal" action='' method="POST">
	  <fieldset>
	    <div id="legend">
	      <legend class="" style="color:green">Register and begin renting!!</legend>
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
	        <p class="help-block">Please provide your E-mail</p>
	      </div>
	    </div>
	 
	    <div class="control-group">
	      <!-- Password-->
	      <label class="control-label" for="password">Password</label>
	      <div class="controls">
	        <input type="password" id="password" name="password" placeholder="" class="input-xlarge">
	        <p class="help-block">Password should be at least 4 characters</p>
	      </div>
	    </div>
	 
	    <div class="control-group">
	      <!-- Password -->
	      <label class="control-label"  for="password_confirm">Password (Confirm)</label>
	      <div class="controls">
	        <input type="password" id="password_confirm" name="password_confirm" placeholder="" class="input-xlarge">
	        <p class="help-block">Please confirm password</p>
	      </div>
	    </div>
	 
	    <div class="control-group">
	      <!-- Button -->
	      <div class="controls">
	        <button class="btn btn-success">Register</button>
	      </div>
	    </div>
	    
	    
	    <div class="control-group">
	    	<div class="controls">
	    		<a href="login.php">Go to login</a>
	    	</div>
	    </div>
	    
	  </fieldset>
	</form>	
</body>
