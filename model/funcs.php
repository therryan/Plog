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
	// 'TRUE' doesn't specify the default db, since it hasn't yet been created
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
		$db->exec("CREATE TABLE IF NOT EXISTS master_posts (".
		                        "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,".
		                        "time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP".
		                        ") ENGINE=InnoDB");
		
		// Create the languages table
		// full_name is the language's complete name: "English"
		// short_name is a (preferably) two-letter abbreviation: "en"
		//		It is used when creating the table for the language: "en_posts"
		// default_lang is the language that is chosen 
		$db->exec("CREATE TABLE IF NOT EXISTS langs (".
		    "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,".
		    "full_name TEXT NOT NULL,".
		    "short_name CHAR(3) NOT NULL UNIQUE,".
		    "table_name CHAR(10) NOT NULL UNIQUE,".
		    "default_lang BOOL NOT NULL DEFAULT FALSE".
		    ") ENGINE=InnoDB");

		echo "Database and default tables successfully created!";
	} catch (PDOException $e) {
		die("ERROR: " . $e->getMessage());
	}
	
	$db = NULL;
}

// Merely calls both inits, i.e. when the program is first run
function fullInitialization()
{
	initializeConfigurationFile();
	initializeDatabase();
}

// Does the necessary database alterations and additions when adding a language:
//		- The language's own table to contain the actual content
//		- A row to the 'langs' table, detailing the language
//		- A column to 'master_posts', with references to the language's table
// Returns TRUE upon success
function addLanguage($name)
{
	// The short name of a language is its two-letter abbreviation (English -> en)
	$shortName = substr(strtolower($name), 0, 2);
	
	// The table name is what will be used for creating the new table,
	//		as well as for the column in master_posts
	// Note: we don't need to prepare queries that only include $tableName, 
	//		because it is so short that it doesn't allow SQL injections
	$tableName = $shortName . "_posts";
	$db = DBConnect();
	
	try {
		// 1. The creation of the new table
		$db->exec("CREATE TABLE IF NOT EXISTS $tableName (".
		    "id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,".
		    "master_id INT NOT NULL UNIQUE,".
		    "lang_id INT NOT NULL,".
            "time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,".
            "title TINYTEXT NOT NULL,".
            "content LONGTEXT NULL".
            ") ENGINE=InnoDB");
	} catch (PDOException $e) {
		// We don't have to drop the table in case an error is thrown, because
		//		that would automatically abort the operation
		die("ERROR in addLanguage(), table creation: <br />" . $e->getMessage());
	}
	
	try {
		$defaultLanguage = FALSE;
		
		// If this is the first language, we'll make it the default one
		if ($db->exec("SELECT * FROM langs") == 0) {
			$defaultLanguage = TRUE;
		}
		
		// The connection has to be reset, because otherwise the next execute()
		//		would throw an error about unbuffered queries
		$db = NULL; $db = DBConnect();
		
		// 2. Adding a row to 'langs'
		$addRowToLangs = $db->prepare("INSERT INTO plog.langs".
		    "(full_name, short_name, table_name, default_lang) VALUES ".
		    "(:name, :shortName, :tableName, :defaultLanguage)");
		$addRowToLangs->bindValue(":name", $name);
		$addRowToLangs->bindValue(":shortName", $shortName);
		$addRowToLangs->bindValue(":tableName", $tableName);
		$addRowToLangs->bindValue(":defaultLanguage", $defaultLanguage);
		$addRowToLangs->execute();
		
		// 3. Adding a column to master_posts
		$db->exec("ALTER TABLE master_posts ".
		          "ADD $tableName INT NOT NULL");
	} catch(PDOException $e) {
		// In case of error, let's roll back the changes
		die("ERROR in addLanguage(), in transaction: <br /> " . $e->getMessage());
	}

	$db = NULL;
	return TRUE;
}

// This function just reverses the changes made by the 'addLanguage' function:
//		- Drops the language's own table that contains the actual content
//		- Deletes the row in the 'langs' table
//		- Drops the column in 'master_posts'
// Returns TRUE upon success
function deleteLanguage($name)
{
	$shortName = substr(strtolower($name), 0, 2);
	$tableName = $shortName . "_posts";
	$db = DBConnect();
	
	try {
	// 1. Dropping the language's table
	$db->exec("DROP TABLE $tableName");
	
	// 2. Deleting the row in 'langs'
	$db->exec("DELETE FROM langs WHERE table_name = '$tableName'");
	
	// 3. Drops the column from 'master_posts'
	$db->exec("ALTER TABLE master_posts DROP $tableName");
	} catch(PDOException $e) {
		die("ERROR in deleteLanguage: <br />" . $e->getMessage());
	}
	$db = NULL;
	return TRUE;
}

?>