<?php
require_once __DIR__.'/class-keyring-model.php';

class PK_Service_Model {
	private $keyring;
	private $service;

	public function __construct() {
		$this->keyring = new Keyring_Model();
		$this->service = 'https://pkserv.spring-dvs.org';
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
		return perform_request($body);
	}
	
	private function perform_request($body) {
		$args = array(
				'headers' => array( "Content-type:application/plaintext"),
				'body' => $body,
				);

		$response = wp_remote_post($this->service, $args);
		$json = wp_remote_retrieve_body($response);
		return json_decode($json, true);
	}
}