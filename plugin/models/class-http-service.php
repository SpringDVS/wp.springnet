<?php

class HTTP_Service {
	
	public static function post_request($host, $message, $secure = false) {

		$response = self::run_request('https://'.$host.'/spring/', $message);
		if(!$response && !$secure) {
			$response = self::run_request('http://'.$host.'/spring/', $message);
		}
		
		return $response;
	}
	
	private static function run_request($uri, $message) {
		$ch = curl_init($uri);
		$len = strlen($message);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_USERAGENT,      "WpSpringNet/0.1" );
		curl_setopt($ch, CURLOPT_POSTFIELDS,      $message);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		return curl_exec($ch);
	}
	
	/**
	 * Perform a DVSP request
	 * 
	 * Returns a \SpringDvs\Message on success else it returns a null
	 * @param String $host The hostname of the node
	 * @param mixed $message Message as String or \SpringDvs\Message
	 * @param boolean $secure Flag whether to trip on HTTPS failure
	 * @return \SpringDvs\Message
	 */
	public static function dvsp_request($host, $message, $secure = false) {
		$string = "";
		if( is_string($message) ) {
			$string = $message;
		} else if(is_object($message)) {
			$string = $message->toStr();
		}
		$response = self::post_request($host, $string);
		try {
			return \SpringDvs\Message::fromStr($response);
		} catch(Exception $e) {
			return null;
		}

		return null;
	}
}