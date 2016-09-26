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
		
		$prepared = $this->db->prepare("INSERT INTO $this->table
				(notif_title,notif_action,notif_source,notif_description)
				VALUES (%s,%s,%s,%s)", $title, $action, $source, $description);

		return $this->db->query($prepared);
	}
	
	public function get_notifications($paged = 1) {
		$total = $this->get_notification_count();
		$paged = $paged < 1 ? 1 : $paged;
		$limit = 10;
		$from = ($paged-1) * $limit;
		
		$prepared = $this->db->prepare("SELECT * FROM $this->table
				ORDER BY notif_id DESC
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
	
	private function get_notification_count() {
		return $this->db->get_var("SELECT COUNT(*) FROM $this->table");
	}
}