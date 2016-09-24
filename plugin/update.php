<?php
add_filter ('pre_set_site_transient_update_plugins', 'springnet_update_plugin_check');
add_filter('plugins_api', 'springnet_plugin_update_info', 10, 3);

function springnet_update_plugin_check ($aggregate)
{
	$response = wp_remote_get("http://spring-dvs.org/versions/wp.springnet.json");
	$json = $response['body'];
	$info = json_decode($json, true);

	$data = get_plugin_data(SPRINGNET_DIR.'/springnet.php', false, false);
	$local = $data['Version'];

	if(version_compare($local, $info['version']) >= 0) {
		return $aggregate;
	}
	$obj = new stdClass();
	$obj->slug = 'springnet';
	$obj->plugin = 'springnet';
	$obj->new_version = $info['version'];
	$obj->url = 'http://www.spring-dvs.org';
	$obj->tested = $info['tested'];
	$obj->package = 'http://packages.spring-dvs.org/wp.springnet_'.$info['version'].".zip";
	$obj->sections = $info['sections'];
	$aggregate->response['springnet/springnet.php'] = $obj;

	return $aggregate;
}

function springnet_plugin_update_info() {
	$response = wp_remote_get("http://spring-dvs.org/versions/wp.springnet.json");
	$json = $response['body'];
	$info = json_decode($json, true);

	$obj = new stdClass();
	$obj->slug = 'springnet';
	$obj->plugin = 'springnet';
	$obj->new_version = $info['version'];
	$obj->url = 'http://www.spring-dvs.org';
	$obj->tested = $info['tested'];
	$obj->package = 'http://packages.spring-dvs.org/wp.springnet_'.$info['version'].".zip";
	$obj->sections = $info['sections'];
	$obj->name = 'SpringNet Node Plugin';
	return $obj;
}