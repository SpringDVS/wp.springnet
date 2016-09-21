<?php
require_once __DIR__.'/class-keyring-model.php';

class PK_Service_Model {
	private $keyring;
	private $service;

	public function __construct() {
		$this->keyring = new Keyring_Model();
		//$this->service = 'http://ppks.zni.lan';
		$this->service = 'https://pkserv.spring-dvs.org/process';
	}
	
	public function generate_keypair($name, $email, $passphrase) {
		$body = "KEYGEN
$passphrase
$name
$email\n\n";
		
		return $this->perform_request($body);
	}
	
	public function import($armor, $subject = null) {
		$body = "IMPORT
PUBLIC {
$armor
}\n";
		if($subject) {
			$body .= "SUBJECT {\n$subject\n}\n";
		}
		
		$body .= "\n";

		return $this->perform_request($body);
		
		
	}
	
	public function keyring() {
		return $this->keyring;
	}
	
	private function perform_request($body) {
		
		$ch = curl_init($this->service);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_USERAGENT,      "WpSpringNet/0.1" );
		curl_setopt($ch, CURLOPT_POSTFIELDS,      $body);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
				'User-Agent: WpSpringNet/0.1'));
		$json = curl_exec($ch);
		
		if($json === false) {
			return false;
		}

		return json_decode($json, true);
	}
}