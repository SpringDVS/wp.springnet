<?php 

add_action('wp_ajax_gateway_snet_netview',
		'springnet_netview_request_gateway_handler');

add_action('wp_ajax_nopriv_gateway_snet_netview',
		'springnet_netview_request_gateway_handler');

function springnet_netview_request_gateway_handler() {
	
	include SPRINGNET_DIR.'/plugin/models/class-gateway-handler.php';
	$response = Gateway_Handler::request_uri_first_response("spring://".filter_input(INPUT_POST,"network"), \SpringDvs\Message::fromStr("info network"));
			
	$network  = $response->getContentResponse()->getNetwork();
	$out = array();
	foreach($network->toJsonArray() as $node) {
		$out[] = array('name' => $node['spring']); 
	}

	wp_die(json_encode($out));
}