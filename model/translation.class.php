<?php

public class Translation
{
	/* --- Fields ---------------------------- */
	private $id;			// Translation's personal ID within 
	private $master_id;		// Referance back to the posting this translation belongs to
	private $language_id;	// Reference to the language the translation is written in
	private $language;		// The complete name of the language
	private $title;
	private $date;
	private $body;			// The actual content of the translation
	//private $tags;
}
?>