<?php
defined( 'ABSPATH' ) or die( 'Error' );
require_once SPRINGNET_DIR.'/plugin/models/class-http-service.php';
require_once SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';

class Node_Model {
	private $primary_host;
	private $node_hostname;
	private $node_springname;
	private $reg_cache;
	
	public function __construct() {
		$this->primary_host = get_option('geonet_hostname');
		$this->node_hostname = get_option('node_hostname');
		$this->node_springname = get_option('node_springname');
		$this->reg_cache = null;
	}
	
	public function register() {
		$keyring = new Keyring_Model();
		$double = $this->node_springname.",".$this->node_hostname;
		$token = get_option('geonet_token');
		$armor = $keyring->get_node_public_key();
		
		$msg = \SpringDvs\Message::fromStr("register {$double};org;http;$token\n$armor");
		
		$response = HTTP_Service::dvsp_request($this->primary_host, $msg);
		
		if(!$response) {
			return false;
		}

		if(\SpringDvs\CmdType::Response != $response->cmd()
		&& \SpringDvs\ProtocolResponse::Ok != $response->content()->code()) {
			return false;
		}
		
		return true;
	}
	
	public function enable() {
		$msg = \SpringDvs\Message::fromStr("update {$this->node_springname} state enabled");
		$response = HTTP_Service::dvsp_request($this->primary_host, $msg);
		return $this->response_ok($response);
	}
	
	public function disable() {
		$msg = \SpringDvs\Message::fromStr("update {$this->node_springname} state disabled");
		$response = HTTP_Service::dvsp_request($this->primary_host, $msg);
		return $this->response_ok($response);
	}
	
	public function is_registered() {
		$msg = \SpringDvs\Message::fromStr("info node {$this->node_springname}");
		
		$response = HTTP_Service::dvsp_request($this->primary_host, $msg);

		if(!$response) {
			return false;
		}

		if(\SpringDvs\CmdType::Response == $response->cmd()
		&& \SpringDvs\ProtocolResponse::NetspaceError == $response->content()->code()) {
					return false;
		}

		$this->reg_cache = $response;
		return true;
	}
	
	public function is_enabled() {
		if(!$this->reg_cache) {
			$msg = \SpringDvs\Message::fromStr("info node {$this->node_springname} state");
			
			$response = HTTP_Service::dvsp_request($this->primary_host, $msg);
			
			if(!$response) {
				return false;
			}
		} else {
			$response = $this->reg_cache;
		}
		
		if(!$this->response_ok($response)) {
			return false;
		}

		if(\SpringDvs\ContentResponse::NodeInfo
			!= $response->content()->type()) {
			return false;
		}
		
		if(\SpringDvs\NodeState::Enabled
			!= $response->content()->content()->state()) {
			return false;
		}

		return true;
	}
	
	private function response_ok($response) {
		if(!$response) {
			return false;
		}
		
		if(\SpringDvs\CmdType::Response != $response->cmd()) {
			return false;
		}
		
		
		if(\SpringDvs\ProtocolResponse::Ok != $response->content()->code()) {
			return false;
		}
				return true;		
	}
}