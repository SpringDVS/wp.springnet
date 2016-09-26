<?php

class Notification_Handler {
	private $db;
	private $table;
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->table = $this->db->prefix.'sn_notifications';
	}
	
	public function add_notification($title, $action, $source, $description) {
		
		$this->db->insert($this->table, array(
				'notif_title' => $title,
				'notif_action' => $action,
				'notif_source' => $source,
				'notif_description' => $description,
				'notif_active' => '0'
		));
		
		return $this->db->insert_id;
		
	}
	
	public function get_notifications($paged = 1) {
		$total = $this->get_notification_count();
		$paged = $paged < 1 ? 1 : $paged;
		$limit = 10;
		$from = ($paged-1) * $limit;
		
		$prepared = $this->db->prepare("SELECT * FROM $this->table
				WHERE notif_active = 1 ORDER BY notif_id DESC
				LIMIT %d,%d
				",
				$from, $limit);
		return $this->db->get_results($prepared);
	}
	
	public function resolve_notification_id($id) {
		$prepared = $this->db->prepare("DELETE FROM $this->table 
				WHERE notif_id = %d",
				$id);
		return $this->db->query($prepared);
	}
	public function resolve_notification_action($action) {
		$prepared = $this->db->prepare("DELETE FROM $this->table
				WHERE notif_action = %s",
				$action);
		return $this->db->query($prepared);
	}
	
	public function activate_notification($id) {
		return $this->db->update($this->table,
				array(
					'notif_active' => 1
				),
				array(
					'notif_id' => $id
				),
				array('%d'),
				array('%d')
				
			);
	}
	
	private function get_notification_count() {
		return $this->db->get_var("SELECT COUNT(*) FROM $this->table");
	}
}