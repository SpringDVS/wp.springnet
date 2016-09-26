<?php
require_once SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';
$res = $uri->res();

if(isset($res[1])) {
	if('pullreq' == $res[1]) {
		if(!isset($query['source'])) {
			return array('request' => 'error');
		}
		
		$on_request = get_option('cert_accept_pull');
		if('notification' == $on_request) {
			global $snrepo;
			if($snrepo->data_exists('cert_pullreq', $query['source'])) {
				return array('request' => 'queued');
			}
			$handler = new Notification_Handler();
			$notif = $handler->add_notification('Certificate Pull Request',
										'page=springnet_keyring&action=pullreq',
										'Certificates',
							"{$query['source']} is requesting an update to your
							public certificate");
			
			
			$snrepo->add_data('cert_pullreq', $query['source'], $notif);
			
			$handler->activate_notification($notif);
			return array('request' => 'ok');
		}
		
		return array('request' => 'ok'); 
	} elseif('pull' == $res[1]) {
		if(!isset($res[2]) || $res[2] == 'private') {
			return array('key' => 'error');
		}
		$keyring = new Keyring_Model();
		
		$key = $keyring->get_key($res[2]);
		
		$key = $key ? $key : 'error';
		return array('key' => $key);		
	}
}


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