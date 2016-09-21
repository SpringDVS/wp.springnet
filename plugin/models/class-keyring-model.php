<?php

class Keyring_Model {
	private $db;
	private $table;
	
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->table = $this->db->prefix . '_snkr_certificates';
	}
	
	public function get_node_public_key() {
		return $this->db->get_var("SELECT armor FROM $table_name 
									WHERE owned=1");
	}

	public function get_node_private_key() {
		if(!is_admin()) return false;
		return $this->db->get_var("SELECT armor FROM $this->table
				WHERE owned=1 AND keyid='private'");
	}

	public function get_node_certificate() {
		$table_name = $this->db->prefix . '_snkr_certificates';
		return $this->db->get_var("SELECT * FROM $this->table
									WHERE owned=1");
	}
	
	public function set_node_certificate($keyid, $email, $sigs, $armor) {
		if(!is_admin()) return false;
		
		$table = $this->db->prefix . '_snkr_certificates';
		$name = get_option('node_uri');
		return $this->set_certificate($keyid, $name, $email, $sigs, $armor);
	}
	
	public function set_node_private($armor) {
		// is_admin checked inside set_certificate for 'private'
		return $this->set_certificate('private', 'private', 'private', '',
										$armor, 'owned');
	}

	public function set_certificate($keyid, $name, $email, $sigs, 
									$armor, $status = 'other')
	{
		if('private' == $keyid) {
			if(!is_admin()) return false;
		}

		$owned = $state == 'owned' ? 1 : 0;
		$sigtext = implode(',', $sigs);
		$prepared = $this->db->prepare(
						"INSERT INTO $this->table
						 (keyid, uidname, uidemail, sigs, armor, owned)
						 VALUES
						 (%s,%s,%s,%s,%s,%d)
						 ON DUPLICATE KEY
						 UPDATE sigs=%s,armor=%s",
						$keyid, $name, $email, $sigtext, $armor, $owned,
						$sigtext, $armor);
		
		$this->db->query($prepared);
	}
	
	public function get_certificate($keyid) {
		if('private' == $keyid) {
			return null;
		}

		$prepared = $this->db->prepare("SELECT * FROM $this->table
										WHERE keyid=%s", $keyid);
		return $this->db->query($prepared);
	}
	
	public function has_private_key() {
		if(!$this->get_node_private_key()) {
			return false;
		}
		return true;
	}

	public function has_certificate() {
		if(!$this->get_node_public_key()) {
			return false;
		}
		return true;
	}
}