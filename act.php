<?php
// This file is not supposed to be directly reached by users
// However, if someone does that, they're returned to the front page
if (count($_POST) == 0) {
	header("Location: index.php");
}
require("model/funcs.php");

// For debugging
foreach ($_POST as $key => $value)
{
	echo $key."::".$value."<br />";
}

switch ($_POST["action"]) {
	case "add":
		switch ($_POST["type"]) {
			case "language":
				if (!empty($_POST["languageName"])) {
					if (addLanguage($_POST["languageName"])) {
						echo "Succesfully added a new language!";
					}
				}
				break;
			default:
				break;
		}
		break;
	case "delete":
		switch ($_POST["type"]) {
			case "language":
				if (!empty($_POST["languageName"])) {
					if (deleteLanguage($_POST["languageName"])) {
						echo "Succesfully deleted language!";
					}
				}
				break;
			default:
				break;
		}
		break;
	default:
		break;
}
?>