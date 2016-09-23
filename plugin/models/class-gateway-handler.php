<?php
/**
 * Handler for gateway requests.
 *
 * As nodes act as gateways to the Spring network, this is used to
 * handle certain requests made of the node, and perform that request
 * internally on the network, providing the response once received.
 *
 * Each node provides particular interfaces to the network through
 * gateway services, and these are what the external system works
 * through. Gateway services will likely have a complimentary
 * network service for which they are providing the interface.
 *
 */

require_once __DIR__.'/class-http-service.php';

class Gateway_Handler {

	/**
	 * Perform a resolution of a Spring URI.
	 *
	 * If there is an error then it returns false otherwise
	 * it will return an array of objects that implement the
	 * INodeNetInterface
	 *
	 * @param string $uri
	 * @return array(\SpringDvs\INodeNetInterface) | false
	 */
	public static function resolve_uri($uri) {
		$message = SpringDvs\Message::fromStr("resolve $uri");

		$response = HTTP_Service::dvsp_request(get_option('geonet_hostname'),
												$message);

		if(!$response
				|| $response->cmd() != \SpringDvs\CmdType::Response
				|| $response->content()->code() != \SpringDvs\ProtocolResponse::Ok) {
					return false;
				}
				$type = $response->content()->type();
				switch($type) {
					case \SpringDvs\ContentResponse::Network:
						return $response->content()->content()->nodes();
					case \SpringDvs\ContentResponse::NodeInfo:
						return array($response->content()->content());
					default:
						return false;
				}

	}

	/**
	 * Perform a request and accept first response
	 *
	 *  This method takes an array of potential target nodes
	 *  and if the request fails, it moves onto the next one.
	 *
	 *  If there is no valid response then the entire method
	 *  fails by returning null.
	 *
	 * @param \SpringDvs\Message $msg
	 * @param array $nodes
	 * @return mixed \SpringDvs\Message on success | null on failure
	 */
	public static function outbound_first_response(\SpringDvs\Message $msg, array $nodes) {

		foreach($nodes as $node) {
			$response = Http_Service::send($node->host(), $msg);

			if($response === false
					|| $response->cmd() != \SpringDvs\CmdType::Response
					|| $response->content()->code() != \SpringDvs\ProtocolResponse::Ok) {
						continue;
					}
					return $response;
		}

		return null;
	}
}
