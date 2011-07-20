<?php
require("funcs.php");

class Posting
{
	/* --- Fields ---------------------------- */
	private $master_id;
	private $timestamp;
	private $translation_ids = array();
	
	// Just fills in the variables from the db
	public function __construct($id)
	{
		$master_id = $id;
		
		try {
			$db = DBConnect();
			$stmt = $db->prepare("SELECT time FROM master_posts WHERE id = ?");
			$stmt->bindValue(1, $id);
			$stmt->execute();
			$result = $stmt->fetchObject();
		
			$this->timestamp = $result->time;
		} catch (PDOException $e) {
			die($e->getMessage);
		}
		$db = NULL;
	}
}
?>