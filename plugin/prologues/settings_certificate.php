<?php
require_once SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';
$has_public_cert = true;

$keyring = new Keyring_Model();
$private_key = $keyring->get_node_private_key();
$public_key = $keyring->get_node_public_key();

if(null == $public_key) {
	$has_public_cert = false;
}