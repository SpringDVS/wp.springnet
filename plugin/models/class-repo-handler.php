<?php
class Repo_Handler {
	private $db;
	private $table;
	public function __construct(){
		global $wpdb;
		$this->db = $wpdb;
		$this->table = $this->db->prefix . 'sn_repo';
	}
	
	public function get_data_from_tag($tag) {
		$prepared = $this->db->prepare("SELECT * FROM $this->table
				WHERE repo_tag=%s", $tag);
		return $this->db->get_results($prepared);
	}
	
	public function get_datum_from_id($tag, $id) {
		$prepared = $this->db->prepare("SELECT * FROM $this->table
				WHERE repo_tag=%s AND repo_id=%d", $tag, $id);
		$rows = $this->db->get_results($prepared);
		if(!$rows || !isset($rows[0])) {
			return null;
		}
		
		return $rows[0]; 
	}
	
	public function add_data($tag, $data, $notification = 0) {
		$this->db->insert($this->table,
				array(
						'repo_tag' => $tag,
						'repo_timestamp' => current_time('mysql', 1),
						'repo_notif' => $notification,
						'repo_data' => $data,
				));
		
		return $this->db->insert_id;
	}
	
	public function remove_data_with_id($tag, $id) {
		return $this->db->delete($this->table,
									array(
										'repo_id' => $id,
										'repo_tag' => $tag
									),
									array('%d', '%s')
								);
	}
	
	public function data_exists($tag, $data) {
		$prepared = $this->db->prepare("SELECT repo_id FROM $this->table
										WHERE repo_tag=%s AND repo_data=%s",
						$tag, $data
						);
		if(!$this->db->get_var($prepared)) {
			return false;
		}
		
		return true;
		
	}
	
}