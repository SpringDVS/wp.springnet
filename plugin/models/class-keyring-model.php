<?php

class Keyring_Model {
	private $db;
	private $table;
	
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->table = $this->db->prefix . 'sn_certificates';
	}
	
	public function get_node_public_key() {
		return $this->db->get_var("SELECT armor FROM $this->table 
									WHERE owned=1 AND keyid != 'private'");
	}

	public function get_node_private_key() {
		if(!is_admin()) return false;
		return $this->db->get_var("SELECT armor FROM $this->table
				WHERE owned=1 AND keyid='private'");
	}

	public function get_node_certificate() {
		return $this->db->get_results("SELECT * FROM $this->table
									WHERE owned=1 AND keyid != 'private'");
	}
	
	public function get_node_keyid() {
		return $this->db->get_var("SELECT keyid FROM $this->table
				WHERE owned=1 AND keyid != 'private'");
	}
	
	public function set_node_certificate($keyid, $email, $sigs, $armor) {
		if(!is_admin()) return false;
		
		$name = get_option('node_uri');
		return $this->set_certificate($keyid, $name, $email, $sigs, 
										$armor,'owned');
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

		$owned = $status == 'owned' ? 1 : 0;
		$sigtext = implode(',', $sigs);
		$prepared = $this->db->prepare(
						"INSERT INTO $this->table
						 (keyid, uidname, uidemail, sigs, armor, owned)
						 VALUES
						 (%s,%s,%s,%s,%s,%s)
						 ON DUPLICATE KEY
						 UPDATE sigs=%s,armor=%s",
						$keyid, $name, $email, $sigtext, $armor, $owned,
						$sigtext, $armor);
		
		return $this->db->query($prepared);
	}
	
	public function get_certificate($keyid) {
		if('private' == $keyid) {
			return null;
		}

		$prepared = $this->db->prepare("SELECT * FROM $this->table
										WHERE keyid=%s", $keyid);
		return $this->db->get_results($prepared);
	}
	
	public function remove_certificate($keyid) {
		if(!is_admin() || !$keyid || 'private' == $keyid) {
			return false;
		}
		
		$prepared = $this->db->prepare("DELETE FROM $this->table
				WHERE keyid=%s", $keyid);
		return $this->db->query($prepared);
		
	}
	
	public function get_key($keyid) {
		$key = $this->get_certificate($keyid);
		if(!$key) {
			return false;
		}
		
		return $key[0]->armor;
	}
	
	public function get_resolved_certificate($keyid) {
		$node_id = $this->get_node_keyid();
		
		$key = $this->get_certificate($keyid);
		
		if(!$key) {
			return null;
		}
		
		$list = explode(",", $key[0]->sigs);
		$sigs = array();
		$signed = false;
		foreach($list as $id) {
			$name = $this->get_uid_name($id);
			$name = $name != null ? $name : 'unknown';
			if($id == $node_id) {
				$signed = true;
			}
			$sigs[] = array(
				'keyid' => $id,
				'name' => $name
			);
		}
		
		$key[0]->sigs = $sigs;
		$key[0]->signed = $signed;
		return $key;
	}
	
	public function get_uid_list($page, $limit = 10) {
		$page = $page < 1 ? 0 : $page - 1;
		$limit = $limit < 1 ? 1 : $limit;
		
		$from = $page * $limit; 
		$prepared = $this->db->prepare("SELECT keyid, uidname, uidemail FROM $this->table
				WHERE keyid != 'private' ORDER BY uidname LIMIT %d,%d", $from,$limit);
		$keys = $this->db->get_results($prepared);
		if(!$keys) {
			return array();
		}
		
		return $keys;
	}
	
	public function get_uid_name($keyid) {
		if('private' == $keyid) {
			return false;
		}
		$prepared = $this->db->prepare("SELECT uidname FROM $this->table
				WHERE keyid = %s", $keyid);
		return $this->db->get_var($prepared);
	}
	
	public function has_private_key() {
		if(!$this->get_node_private_key()) {
			return false;
		}
		return true;
	}

	public function has_certificate() {
		if(!$this->db->get_var("SELECT certid FROM $this->table where keyid='private'")) {
			return false;
		}
		return true;
	}
	
	public function get_certificate_count() {
		$count = $this->db->get_var("SELECT COUNT(*) FROM $this->table");
		if($this->has_private_key()) {
			$count--;
		}
		
		return $count;
	}
}