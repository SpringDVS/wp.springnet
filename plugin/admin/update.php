<?php
add_filter ('pre_set_site_transient_update_plugins', 'springnet_update_plugin_check');
add_filter('plugins_api', 'springnet_plugin_update_info', 10, 3);

function springnet_update_plugin_check ($transient)
{
	if ( empty( $transient->checked ) ) {
		return $transient;
	}
	$response = wp_remote_get("http://spring-dvs.org/versions/wp.springnet.json");

	$json = wp_remote_retrieve_body($response);

	$info = json_decode($json, true);

	$data = get_plugin_data(SPRINGNET_DIR.'/springnet.php', false, false);
	$local = $data['Version'];

	if(version_compare($local, $info['version']) >= 0) {
		return $transient;
	}
	$obj = new stdClass();
	$obj->slug = 'springnet';
	$obj->plugin = 'springnet';
	$obj->new_version = $info['version'];
	$obj->url = 'http://www.spring-dvs.org';
	$obj->tested = $info['tested'];
	$obj->package = 'http://packages.spring-dvs.org/wp.springnet_'.$info['version'].".zip";
	$obj->sections = $info['sections'];
	$transient->response['springnet/springnet.php'] = $obj;

	return $transient;
}

function springnet_plugin_update_info($false, $action, $response) {

	if ( empty( $response->slug ) || $response->slug != 'springnet' ) {
    	return false;
	}

	$details = wp_remote_get("http://spring-dvs.org/versions/wp.springnet.json");
	$json = wp_remote_retrieve_body($details);
	$info = json_decode($json, true);

	
	$response->slug = 'springnet';
	$response->plugin = 'springnet';
	$response->new_version = $info['version'];
	$response->url = 'http://www.spring-dvs.org';
	$response->tested = $info['tested'];
	$response->package = 'http://packages.spring-dvs.org/wp.springnet_'.$info['version'].".zip";
	$response->sections = $info['sections'];
	$response->name = 'SpringNet Node Plugin';
	return $response;
}