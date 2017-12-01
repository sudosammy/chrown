<?php
class err {
	/******************************************
		Construct
	******************************************/
	function __construct() {
		if (!is_array($this->error)) {
			$this->error = array();
		}
	}
	
	/******************************************
		Add result messages
	******************************************/
	function add ($message, $id = null) {
		if (empty($id)) {
			$keys = count($this->error);
			$id = ++$keys;
		}
		$this->error[$id] = $message;
	}
	
	/******************************************
		Load result messages
	******************************************/
	function load () {
		if (!empty($this->error)) {
			return $this->error;
		}
		return;
	}
}
