<?php
require_once("conf.php");

// Connect to MySQL using PDO
function DBConnect()
{
	$address = getconf("Address");
	$user = getconf("Username");
	$password = getconf("Password");
	//$dbName = getconf("Database");
	$dbName = "";
	
	try
	{
		$db = new PDO("mysql:host=$address;dbname=$dbName", $user, $password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (PDOException $e)
	{
		die("ERROR in DBConnect()!: <br /> " . $e->getMessage());
	}
	
	return $db;
}
?>