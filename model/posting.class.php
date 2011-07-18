<?php
require("funcs.php");

public class Posting
{
	/* --- Fields ---------------------------- */
	private $master_id;
	private $timestamp;
	private $translation_ids = array();
	
	// Just fills in the variables from the db
	public function __construct($id)
	{
		$master_id = $id;
		
		$db = DBConnect();
		$stmt = $db->prepare("SELECT ")
		
	}
}
?>