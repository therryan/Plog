<?php
// This file contains several dev-friendly tests


?>
<html>
	<head>
		
	</head>
	
	<body>
		<form action="act.php" method="post">
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="type" value="language" />
			Add new language: <input type="text" name="languageName" />
			<input type="submit" value="Add">
		</form>
		<form action="act.php" method="post">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="type" value="language" />
			Delete a language: <input type="text" name="languageName" />
			<input type="submit" value="Delete">
	</body>

</html>
