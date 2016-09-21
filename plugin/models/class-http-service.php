<?php

class HTTP_Service {
	
	public static function post_request($host, $message) {

		$ch = curl_init('http://'.$host.'/spring/');
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_USERAGENT,      "WpSpringNet/0.1" );
		curl_setopt($ch, CURLOPT_POSTFIELDS,      $message);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
				'User-Agent: WpSpringNet/0.1'));
		return curl_exec($ch);
	}
	
	/**
	 * Perform a DVSP request
	 * 
	 * Returns a \SpringDvs\Message on success else it returns a null
	 * @param String $host The hostname of the node
	 * @param mixed $message Message as String or \SpringDvs\Message
	 * @return \SpringDvs\Message
	 */
	public static function dvsp_request($host, $message) {
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