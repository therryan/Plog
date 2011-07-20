<?php
require("model/posting.class.php");

// List all Postings
$db = DBConnect();
$result = $db->query("SELECT * FROM master_posts");

while ($row = $result->fetch()) {
	echo $row["id"] . "<br />". $row["time"] . "<br />";
}
addLanguage("French");

$db = NULL;
?>
<!DOCTYPE html>
<html>
	<head>
	</head>
	
	<body>
	</body>
</html>