<?php
require("model/funcs.php");

// Creates a new configuration file, if none is present
function initializeConfigurationFile()
{
	
}

// Creates the necessary tables etc. in the database
function initializeDatabase()
{
	$db = DBConnect();
	
	try
	{
		// Create the database itself
		// Note: requires CREATE privileges
		$db->exec("CREATE DATABASE IF NOT EXISTS ptest");
		$db->exec("USE ptest");
		// Grant full privileges to to database
		// Note: The user 'plog' doesn't need to grant privileges to other users,
		//		thus no WITH GRANT OPTION at the end
		$db->exec("GRANT ALL PRIVILEGES ON ptest.* TO 'plog'@'localhost'");
		
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
		$db->exec("CREATE TABLE langs(".
		                        "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,".
		                        "full_name TEXT NOT NULL,".
		                        "short_name CHAR(2) NOT NULL UNIQUE,".
		                        "default_lang BOOL NOT NULL DEFAULT FALSE".
		                        ") ENGINE=InnoDB");

		echo "Success!";
	}
	catch (PDOException $e)
	{
		die("ERROR: " . $e->getMessage());
	}
}

// Merely calls both inits, i.e. when the program is first run
function fullInitialization()
{
	initializeConfigurationFile();
	initializeDatabase();
}

initializeDatabase();

?>
<!DOCTYPE html>
<html>
	<head>
	</head>
	
	<body>
	</body>
</html>