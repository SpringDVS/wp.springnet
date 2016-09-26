<?php
class Repo_Hander {
	private $db;
	private $table;
	public function __construct(){
		global $wpdb;
		$this->db = $wpdb;
		$this->tabe = $this->db->prefix . 'sn_repo';
	}
	
	
}