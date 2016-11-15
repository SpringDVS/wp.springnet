<?php

use SpringDvs\ContentResponse;

require __DIR__.'/glob-loader.php';


add_action('wp_ajax_gateway_bulletin_request',
		'springnet_bulletin_request_gateway_handler');

add_action('wp_ajax_nopriv_gateway_bulletin_request',
		'springnet_bulletin_request_gateway_handler');

function springnet_bulletin_request_gateway_handler() {
	
	$network =($n = filter_input(INPUT_POST,'network')) ?
		"spring://".$n
		: null;
	$node = ($n = filter_input(INPUT_POST,'node')) ? 
		"spring://$n"
		: null;

	$uid = ($u = filter_input(INPUT_POST,'uid')) ?
		"$u"
		: null;
	
	
	if(!$network && !$node) { wp_die('{"status":"error"}'); }
	
	$hier = $network ? $network : $node;
	
	$qobj = array();
	if($t = filter_input(INPUT_POST,'tags')) { $qobj['tags'] = $t; };
	if($t = filter_input(INPUT_POST,'limit')) { $qobj['limit'] = $t; };
	

	include SPRINGNET_DIR.'/plugin/models/class-gateway-handler.php';
	$nodes = Gateway_Handler::resolve_uri($hier);

	$service = $network || ($node && $uid) ?
		"$hier/bulletin/{$uid}?".http_build_query($qobj)
		: "$hier/orgprofile/?".http_build_query($qobj);


	try {
		$message = \SpringDvs\Message::fromStr("service $service");
		$response = Gateway_Handler::outbound_first_response($message, $nodes);
	} catch(\Exception $e) {

		wp_die(['status' => 'error', 'uri' => $service]);
	}

	if(!$response) {
		wp_die(json_encode(['status' => 'error', 'uri' => $hier, 'reason' => 'Request failed']));
	}

	$serviced = array();
	

	switch($response->getContentResponse()->type()) {
	
		case \SpringDvs\ContentResponse::ServiceText:	
			$jobj = json_decode($response->getContentResponse()->getServiceText()->get(), true);
			$v = reset($jobj);
			$k = key($jobj);
			$serviced[] = array($k => $v);
			break;
		
		
		case \SpringDvs\ContentResponse::ServiceMulti:
			foreach($response->getContentResponse()->getServiceParts() as $msg) {
				$jobj = json_decode($msg->getContentResponse()->getServiceText()->get(), true);
				$v = reset($jobj);
				$k = key($jobj);
			
				$serviced[] = array($k => $v); 
			}
			break;
			
		default: break;
	}


	$dec = [
			"status" => 'ok',
			"content" => $serviced
	];

	wp_die(json_encode($dec));
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
		
		$response = Gateway_Handler::request_uri_first_response($uri, $message);

		
		if(!$response) { wp_die(json_encode(array('status'=>'error on response'))); }


		if($profile == "") {
			if($uid == "") { // Getting broadcast listings
				$out = springnet_bulletin_explore_listing($response);
			} else { // Getting specific listing from node
				$out = springnet_bulletin_explore_listing($response);
			}
		} else {
			$out = springnet_bulletin_explore_profile($response);
		}

	echo json_encode(array('status'=>'ok','content'=>$out));

	wp_die();
}

function springnet_bulletin_explore_listing(\SpringDvs\Message $response) {
	
	$listing = array();
	$type = $response->getContentResponse()->type();
	if($type == \SpringDvs\ContentResponse::ServiceText) {
		$json = $response->getContentResponse()->getServiceText()->get();
		$jobj = json_decode($json,true);
		$listing = reset($jobj);
		$listing['node'] = key($jobj);
	} elseif ($type == \SpringDvs\ContentResponse::ServiceMulti) {
		foreach($response->getContentResponse()->getServiceParts() as $msg) {
			$json = $msg->getContentResponse()->getServiceText()->get();
			$jobj = json_decode($json,true);
			$nlst = reset($jobj);
			$node = key($jobj);
			foreach($nlst as &$item) { $item['node'] = $node; }
			
			$listing = array_merge($listing, $nlst);
		}
	}

	return $listing;
}

function springnet_bulletin_explore_profile($response) {
	$profile = array();
	$type = $response->getContentResponse()->type();
	if($type == \SpringDvs\ContentResponse::ServiceText) {
		$json = $response->getContentResponse()->getServiceText()->get();
		$jobj = json_decode($json,true);
		$profile = reset($jobj);
		$profile['node'] = key($jobj);
	}
	
	return $profile;
}
