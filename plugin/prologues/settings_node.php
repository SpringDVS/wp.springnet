<?php
require SPRINGNET_DIR.'/plugin/models/class-node-model.php';
require_once SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';

$node = new Node_Model();
$keyring = new Keyring_Model();

$has_public_cert = $keyring->has_certificate();
$has_uri = get_option('node_uri') != '' ? true : false;
$has_token = get_option('geonet_token') != '' ? true : false;
$is_registered = $node->is_registered();
$is_enabled = false;


if($is_registered) {
	$is_enabled = $node->is_enabled();
}
