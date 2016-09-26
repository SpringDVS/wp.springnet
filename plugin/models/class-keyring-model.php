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
		if(!current_user_can('manage_options')) {
			return false;
		}
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
			if(!current_user_can('manage_options')) {
				return false;
			}
		}

		$owned = $status == 'owned' ? 1 : 0;
		$sigtext = implode(',', $sigs);

		if(!$this->get_uid_name($keyid)) {
			return $this->db->insert($this->table, array (
					'keyid' => $keyid,
					'uidname' => $name,
					'uidemail' => $email,
					'sigs' => $sigtext,
					'armor' => $armor,
					'owned' => $owned
				),array('%s','%s','%s','%s','%s','%d'));

		} else {

			$rows = $this->db->update($this->table, array(
				'sigs' => $sigtext,
				'armor' => $armor
				),
				array('keyid' => $keyid),
				array('%s','%s'),
				array('%s')
			);
			var_dump($this->db->last_query);
			return $rows;
		}


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
		if(!current_user_can('manage_options') || !$keyid || 'private' == $keyid) {
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
	
	public function perform_pull($uri) {
		if(substr($uri, 0, 9) != 'spring://') {
			$uri = "spring://$uri"; 
		}

		require_once SPRINGNET_DIR.'/plugin/models/class-http-service.php';
		$service = new HTTP_Service();
		$keyid = $this->get_node_keyid();
		try {
			
			$node = $service->dvsp_resolve($uri);
			
			if(!$node || !isset($node[0])) {
				return false;
			}
			$node = $node[0];
			$message = SpringDvs\Message::fromStr("service $uri/cert/pull/$keyid");
			
			$response = $service->dvsp_request($node->host(), $message);
			if($response->content()->code() != \SpringDvs\ProtocolResponse::Ok) {
				return false;
			}
			$response_array = json_decode($response->content()->content()->get(), true);
			$key = array_pop($response_array);
			$key = isset($key['key']) ?  $key['key'] : false;
			return $key;
		} catch(Exception $e) {
			return false;
		}
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