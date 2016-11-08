<?php
add_action('wp_ajax_settings_lookup_primary',
		'springnet_settings_lookup_primary_handler');
function springnet_settings_lookup_primary_handler() {
	$geonet = filter_input(INPUT_POST, 'geonetwork');
	$response = wp_remote_get("https://resolve.spring-dvs.org/geosubs/$geonet");
	echo wp_remote_retrieve_body($response);
	wp_die();
}

add_action('wp_ajax_settings_generate_certificate',
		'springnet_settings_generate_certificate_handler');
function springnet_settings_generate_certificate_handler() {

	if( !current_user_can('manage_options') ) { echo "Error"; wp_die(); }

	require SPRINGNET_DIR.'/plugin/models/class-pk-service-model.php';
	$service = new PK_Service_Model();

	$passphrase = filter_input(INPUT_POST, 'passphrase');
	$email = filter_input(INPUT_POST, 'email');
	$name = get_option('node_uri');
	$keypair = $service->generate_keypair($name, $email, $passphrase);

	

	if(!$keypair
	|| "" == $keypair['private']
	|| "" == $keypair['public']) {
		echo '{"result" : "error", "reason" : "Server error -- Recieved malformed keys.  Please try again or contact spring@care-connections.org"}';
		wp_die();
	}
	$service->keyring()->set_node_private($keypair['private']);
	$key = $service->import($keypair['public']);
	
	
	
	if(!$key) {
		$service->keyring()->reset_node_keys();
		echo '{"result" : "error", "reason" : "Server error -- Recieved blank import. Please try again or contact spring@care-connections.org"}';
		wp_die();
	}

	foreach($key as $item) {
		if("" == $item) {
			$service->keyring()->reset_node_keys();
			echo '{"result" : "error", "reason" : "Server error -- Recieved malformed import. Please try again or contact spring@care-connections.org"}';
			wp_die();
		}
	}

	$service->keyring()->set_node_certificate($key['keyid'],
			$key['email'],
			$key['sigs'],
			$key['armor']);

	echo '{"result":"ok"}';
	wp_die();
}


add_action('wp_ajax_settings_node_register',
		'springnet_settings_node_register');
function springnet_settings_node_register() {
	if( !current_user_can('manage_options') ) { echo "Error"; wp_die(); }
	

	require SPRINGNET_DIR.'/plugin/models/class-node-model.php';
	$node = new Node_Model();
	if($node->register()) {
		echo '{"result":"ok"}';
	} else {
		echo '{"result":"error"}';
	}
	wp_die();
}

add_action('wp_ajax_settings_node_state',
		'springnet_settings_node_state');
function springnet_settings_node_state() {

	if( !current_user_can('manage_options') ) { echo "Error"; wp_die(); }

	require SPRINGNET_DIR.'/plugin/models/class-node-model.php';
	
	$node = new Node_Model();
	$state = filter_input(INPUT_POST, 'state');

	if('enabled' == $state) {
		$node->enable();
	} else {
		$node->disable();
	}
	
	
	
	wp_die();
}


add_action('wp_ajax_settings_certificate_reset',
		'springnet_settings_certificate_reset');
function springnet_settings_certificate_reset() {

	if( !current_user_can('manage_options') ) { echo "Error"; wp_die(); }

	require SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';

	$springname = filter_input(INPUT_POST, 'validation');

	if($springname == get_option('node_springname')) {
		$keyring = new Keyring_Model();
		$keyring->reset_node_keys();
		echo '{"result":"ok"}';
	} else {
		echo '{"result":"error"}'; 
	}

	wp_die();
}