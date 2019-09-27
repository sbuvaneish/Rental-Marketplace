<?php
$server = 'us-cdbr-iron-east-02.cleardb.net';
$username = 'b7e32c44881d1e';
$password = '5550f55b';
$database = 'heroku_2f6114e386eb6eb';

try{
	$conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
} catch(PDOException $e){
	die( "Connection failed: " . $e->getMessage());
}
