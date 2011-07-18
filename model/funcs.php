<?php
require_once("conf.php");

// Connect to MySQL using PDO
function DBConnect($firstTime = FALSE)
{
	$address = getconf("Address");
	$user = getconf("Username");
	$password = getconf("Password");
	
	// If this is the first time connecting to the server, we cannot specify
	//		default db because it hasn't yet been  created
	if ($firstTime) {
		$dbName = "";
	} else {
		$dbName = getconf("Database");
	}
	
	try {
		$db = new PDO("mysql:host=$address;dbname=$dbName", $user, $password);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		include($_SERVER["DOCUMENT_ROOT"]."/plog/init.php");
		initializeDatabase();
		die("ERROR in DBConnect()!: <br /> " . $e->getMessage());
	}
	
	return $db;
}

// Creates a new configuration file, if none is present
function initializeConfigurationFile()
{
	
}

// Creates the necessary tables etc. in the database
function initializeDatabase()
{
	// Doesn't specify the default db, since it hasn't yet been created
	$db = DBConnect(TRUE);
	
	try {
		// Create the database itself
		// Note: requires CREATE privileges
		$db->exec("CREATE DATABASE IF NOT EXISTS plog");
		$db->exec("USE plog");
		// Grant full privileges to to database
		// Note: The user 'plog' doesn't need to grant privileges to other users,
		//		thus no WITH GRANT OPTION at the end
		$db->exec("GRANT ALL PRIVILEGES ON plog.* TO 'plog'@'localhost'");
		
		// Create the master_posts table
		// Note: The actual columns that reference translations will be created
		//		at the same time as a new language is added
		$db->exec("CREATE TABLE IF NOT EXISTS master_posts(".
		                        "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,".
		                        "time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP".
		                        ") ENGINE=InnoDB");
		
		// Create the languages table
		// full_name is the language's complete name: "English"
		// short_name is a (preferably) two-letter abbreviation: "en"
		//		It is used when creating the table for the language: "en_posts"
		// default_lang is the language that is chosen 
		$db->exec("CREATE TABLE IF NOT EXISTS langs(".
		                        "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,".
		                        "full_name TEXT NOT NULL,".
		                        "short_name CHAR(2) NOT NULL UNIQUE,".
		                        "default_lang BOOL NOT NULL DEFAULT FALSE".
		                        ") ENGINE=InnoDB");

		echo "Database and default tables successfully created!";
	} catch (PDOException $e) {
		die("ERROR: " . $e->getMessage());
	}
}

// Merely calls both inits, i.e. when the program is first run
function fullInitialization()
{
	initializeConfigurationFile();
	initializeDatabase();
}

?>