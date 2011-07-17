<?php
// The conf-functions form a front-end for editing and easily looking up settings in the program itself

// Mainly for internal use, checks whether the conf file is there or not
function checkconf()
{
	if (is_file("plog.conf"))
	{
		return TRUE;
	}
	else
	{
		die("<p>The configuration file is missing.</p>");
	}
}

function getconf($directive)
{
	checkconf();
	require("plog.conf");
	
	// If the given parameter (e.g. "test") represents a variable is the conf file ($test),
	// then return the value of that directive
	if (isset($$directive))
	{
		return $$directive;
	}
	else {die("Given directive $$directive does not exist. Please contact the system administrator.");}
}
?>