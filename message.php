<!DOCTYPE html>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
<div class="container">
	<form method="POST" action="send.php">
		<div><label>Receiver Email:</label></div>
		<div><input type="text" name="myEmail" class="form-control" /></div>
		
		<div><label>Message:</label></div>
		<div><textarea cols="40" rows="5" name="myMessage" class="form-control"></textarea></div>
		<div class="float-right mt-2">
			<input type="submit" value="Send" class="btn btn-primary" />
		</div>
	</form>
</div>

