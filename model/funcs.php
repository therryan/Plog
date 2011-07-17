<?php
require_once("conf.php");

// Connect to MySQL using PDO
function mysqliConnect()
{
	$address = getconf("Address");
	$user = getconf("Username");
	$password = getconf("Password");
	$dbName = getconf("Database");
	
	try
	{
		$db = new PDO("mysql:host=$address;dbname=$dbName", $user, $password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (PDOException $e)
	{
		echo "Connection failed: " . $e->getMessage();
	}
	
	return $db;
}
?>