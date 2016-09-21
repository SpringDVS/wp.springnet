<?php


function springnet_network_service_request_path($service) {
	$path = SPRINGNET_DIR.'/modules/'.$service.'/request.php';
	if(!file_exists($path)) {
		return null;
	}
	
	return $path;
}

function springnet_network_service_info($service) {
	$path = SPRINGNET_DIR.'/modules/'.$service.'/info.php';
	return include $path;
}

function springnet_service_request() {
	
	$request = trim(file_get_contents('php://input'));
	
	define('SPRING_IF', true);
	return springnet_protocol_handler($request);
}

function springnet_protocol_handler($request) {
	try {
		$msg = \SpringDvs\Message::fromStr($request);
	} catch(Exception $e) {
		return "104"; // MalformedContent
	}
	
	switch($msg->cmd()) {
		case \SpringDvs\CmdType::Service:
			return springnet_protocol_service($msg);
		default:
			return "121";
	}
}

function springnet_protocol_service($msg) {
	$uri = $msg->content()->uri();
	$resource_path = $uri->res();
	$query = array();
	
	parse_str($uri->query(), $query);

	if(!isset($resource_path[0])) {
		return "104";
	}
	
	
	$service = $resource_path[0];
	array_shift($resource_path);
		
	$inc = springnet_network_service_request_path($service);
	
	if(!$inc) {
		return "122";
	}
	$info = springnet_network_service_info($service);
	$response = include $inc;
	
	if($info['encoding'] == 'json') {
		$out = array( get_option('node_uri') => $response);
		$text = "service/text ".json_encode($out);
		$len = strlen($text);
		return "200 $len $text";
	} else {
		return "122";
	}
}