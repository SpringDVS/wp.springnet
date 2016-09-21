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

	if( !is_admin() ) { echo "Error"; wp_die(); }

	require SPRINGNET_DIR.'/plugin/models/class-pk-service-model.php';
	$service = new PK_Service_Model();

	$passphrase = filter_input(INPUT_POST, 'passphrase');
	$email = filter_input(INPUT_POST, 'email');
	$name = get_option('node_uri');
	$keypair = $service->generate_keypair($name, $email, $passphrase);

	if(!$keypair) echo "-1";
	$service->keyring()->set_node_private($keypair['private']);
	$key = $service->import($keypair['public']);
	//echo json_encode($response);

	$service->keyring()->set_node_certificate($key['keyid'],
			$key['email'],
			$key['sigs'],
			$key['armor']);

	echo '{"result":"ok"}';
	wp_die();
}
