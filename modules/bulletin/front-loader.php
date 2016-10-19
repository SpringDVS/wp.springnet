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

add_action('wp_ajax_gateway_bulletin_explore',
		'springnet_bulletin_explore_handler');

add_action('wp_ajax_nopriv_gateway_bulletin_explore',
		'springnet_bulletin_explore_handler');

function springnet_bulletin_explore_handler() {
	$network = filter_input(INPUT_POST,'network') ?
					filter_input(INPUT_POST,'network') : "";
	
	$category = filter_input(INPUT_POST,'category') ?
					filter_input(INPUT_POST,'category') : "";
	
	$uid = filter_input(INPUT_POST,'uid') ?
					filter_input(INPUT_POST,'uid') : "";
	
	$profile = filter_input(INPUT_POST,'profile') ?
					filter_input(INPUT_POST,'profile') : "";
					
	include SPRINGNET_DIR.'/plugin/models/class-gateway-handler.php';
	
	$uri = "spring://$network";
	$message = null;
	$out = null;
	

		
		if($profile != "") {
			$uri = "spring://$profile";
			$message = \SpringDvs\Message::fromStr("service $uri/orgprofile/");
		} else if($uid == "") {
			$message = \SpringDvs\Message::fromStr("service $uri/bulletin/?categories=$category");
		} else {
			$message = \SpringDvs\Message::fromStr("service $uri/bulletin/$uid");
		} 
		
		$nodes = Gateway_Handler::multicast_service_array(
							Gateway_Handler::request_uri_first_response($uri, $message)
					);
		
		if(!$nodes) {
			echo json_encode(array('status'=>'error on response'));
			wp_die();
		}

		
		if($profile == "") {
			if($uid == "") {
				$out = springnet_bulletin_explore_listing($nodes, true);
			} else {
				$out = springnet_bulletin_explore_listing($nodes, false);
			}
		} else {
			$out = springnet_bulletin_explore_profile($nodes);
		}

	echo json_encode(array('status'=>'ok','content'=>$out));

	wp_die();
}

function springnet_bulletin_explore_listing($nodes, $isMulti) {
	$listing = array();
	foreach($nodes as $node) {
		if(strlen($node) == 0) continue;
			
		$j = json_decode($node,true);
			
			
		foreach($j as $k => &$v) {
			if($isMulti) {
				foreach($v as &$p){ $p['node'] = $k; }
				$listing = array_merge($listing, $v);
			} else {
				$v['node'] = $k;
				$listing = $v;
			}
		}
	}
	return $listing;
}

function springnet_bulletin_explore_profile($nodes) {
	$listing = null;
	foreach($nodes as $response) {
		
		$s = json_decode($response,true);
		foreach($s as $node => &$profile) {
			$profile['node'] = $node;
			$listing = $profile;
		}
	}
	return $listing;
}
