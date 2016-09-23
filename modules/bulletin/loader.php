<?php
add_action('springnet_init', 'springnet_bulletin_post_type_register');
function springnet_bulletin_post_type_register() {

	register_post_type( 'springnet_bulletin',
			array(
					'labels' => array(
							'name' => __('Spring Network Bulletins'),
							'singular_name' => __('Bulletin'),
					),
					'show_in_menu' => false,
					'public' => true,
					'has_archive' => true,
					'taxonomies' => array('post_tag')
			)
			);
}

add_action('springnet_menu', 'springnet_bulletin_menu');
function springnet_bulletin_menu() {
	add_submenu_page( SN_MENU_SLUG, 'Bulletins', 'Bulletins', 'edit_pages',
			'edit.php?post_type=springnet_bulletin'
			);
}

include __DIR__.'/widgets/latest.php';

add_action('widgets_init', 'springnet_bulletin_register_widget');
function springnet_bulletin_register_widget() {
	register_widget('SpringNetBulletinsLatest');
}

add_action('wp_ajax_gateway_bulletin_request',
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