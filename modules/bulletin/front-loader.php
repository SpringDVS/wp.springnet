<?php

require __DIR__.'/glob-loader.php';


add_action('wp_ajax_gateway_bulletin_request',
		'springnet_bulletin_request_gateway_handler');

add_action('wp_ajax_nopriv_gateway_bulletin_request',
		'springnet_bulletin_request_gateway_handler');

function springnet_bulletin_request_gateway_handler() {
	$uri = filter_input(INPUT_POST,'uri')
	? "spring://".filter_input(INPUT_POST,'uri')
	: wp_die();

	include SPRINGNET_DIR.'/plugin/models/class-gateway-handler.php';
	$nodes = Gateway_Handler::resolve_uri($uri);

	try {
		$message = \SpringDvs\Message::fromStr("service $uri");
	} catch(\Exception $e) {
		return ['status' => 'error', 'uri' => $uri];
	}


	$response = Gateway_Handler::outbound_first_response($message, $nodes);

	if($response === null) {
		return ['status' => 'error', 'uri' => $uri, 'reason' => 'Request failed'];
	}

	if($response->content()->type() != \SpringDvs\ContentResponse::ServiceText) {
		return ['status' => 'error', 'uri' => $uri, 'reason' => 'Invalid service response type'];
	}

	$v = explode('|', $response->content()->content()->get());
	$serviced = array();
	foreach($v as $k => $val) {
		if($val == "") continue;
		$serviced[$k] = json_decode($val);
	}

	$dec = [
			"status" => 'ok',
			"content" => $serviced
	];

	echo json_encode($dec);
	wp_die();
}