<?php
require_once SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';

$keyring = new Keyring_Model();

$key = $keyring->get_node_certificate();


if(!isset($key[0])) {
	return array('certificate' => 'error');
}

if(isset($query['keyonly']) && 1 == $query['keyonly']) {
	return array('key' => $key[0]->armor);
}

return array('certificate' => array(
		'name' => $key[0]->uidname,
		'email' => $key[0]->uidemail,
		'keyid' => $key[0]->keyid,
		'sigs' => explode(",", $key[0]->sigs),
		'armor' => $key[0]->armor,
));