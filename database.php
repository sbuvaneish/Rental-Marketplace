<?php
$server = 'localhost';
$username = 'admin';
$password = '';
$database = 'marketplace';

try{
	$conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
} catch(PDOException $e){
	die( "Connection failed: " . $e->getMessage());
}
