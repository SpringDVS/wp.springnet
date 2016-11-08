<?php

function springnet_admin_module_controller() {
	$module = filter_input(INPUT_GET, 'module');
	
	if(!$module) {
		return springnet_overview_display();
	}
	$file = SPRINGNET_DIR.'/modules/'.$module.'/admin.php';
	if(!file_exists($file)) {
		echo "Error";
		return;
	}
	
	include $file;
	
	do_action('springnet_module_admin');
}

function springnet_keyring_controller() {
	require_once SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';
	require_once SPRINGNET_DIR.'/plugin/models/class-pk-service-model.php';
	
	$rows = null;
	$keyring = new Keyring_Model();
	
	
	if(($action = filter_input(INPUT_GET, 'action')) ) {
		
		if('import' == $action) {
			
			$service = new PK_Service_Model();
			$status = 'none';
			$reason = '';
			$name = '';
			$key = '';
			if( ($certificate = filter_input(INPUT_POST, 'certificate')) ) {
				$response = $service->import($certificate);
				
				// Check to see if we have the certificate already
				if( ($subject = $keyring->get_certificate($response['keyid'])) ) {
					$response = $service->import($certificate, $subject->armor);
				}
				
				if($keyring->set_certificate(
						$response['keyid'],
						$response['name'],
						$response['email'],
						$response['sigs'],
						$response['armor'])) {
					$status = 'success';
					$name = $response['name'];
				} else {
					$status = 'error';
					$reason = 'There was an error importing certificate.
								Please contact
								<a href="mailto:spring@care-connections.org">
									spring@care-connections.org
								</a> if it continues to fail';
				}
			} else if($uri = filter_input(INPUT_POST, 'request-uri')) {
				
				include SPRINGNET_DIR.'/plugin/models/class-gateway-handler.php';
				
				if(substr($uri, 0,10) != "spring://") {
					$uri = "spring://$uri";
				}
				
				$msg = \SpringDvs\Message::fromStr("service $uri/cert/key");
				$response = Gateway_Handler::request_uri_first_response($uri, $msg);

				if(!$response) {
					$status = 'error';
					$reason = 'Failed to perform request -- does node exist?';					
				} else {
					$json = '';
					try {
						
						$json = $response->getContentResponse()->getServiceText()->get();
						$tmp = json_decode($json, true);
						$o = array_shift($tmp);
						if($o['key'] == 'error') {
							$status = 'error';
							$reason = 'Certificate request resulted in an error';
						
						} else {
							$key = $o['key'];
						}
						
					} catch(\Exception $e) {
						$status = 'error';
						$reason = 'Unexpected response from request of certificate';
					}
					
				
				}
			}
			
			springnet_keyring_import_display($status, $name, $reason, $key);
			
		 }  elseif('sign' == $action) {
		 	
		 	$service = new PK_Service_Model();
		 	$keyid = filter_input(INPUT_GET, 'keyid');
		 	$status = 'none';
		 	$reason = 'Sign a certificate';
		 	
		 	$redirect = '?page=springnet_keyring&keyid='.$keyid;
		 	if( ($passphrase = filter_input(INPUT_POST, 'passphrase')) ) {
		 		if(is_admin()) {
		 			$public = $keyring->get_key($keyid);
		 			$private = $keyring->get_node_private_key();
		 			$signed = $service->sign($public, $private, $passphrase);
		 			if(!isset($signed['public']) || '' == $signed['public']) {
		 				$status = 'error';
		 			} else {
		 				$response = $service->import($signed['public']);
			 			if($keyring->set_certificate(
								$response['keyid'],
								$response['name'],
								$response['email'],
								$response['sigs'],
								$response['armor'])) {
							$status = 'success';			
						} else {
							$status = 'error';
						}
		 			}
		 		}
		 	}
		 	
		 	springnet_keyring_unlock_display($reason, $status, $redirect);
		 	
		 } elseif('remove' == $action) {
		 	
		 		$status = 'none';
		 		$notice = '';
		 		$key = null;
		 		
	 			if(is_admin()) {
	 				$keyid = filter_input(INPUT_GET, 'keyid');
					if($keyring->remove_certificate($keyid)) {
						$status = 'success';
						$notice = 'Removed certificate successfully!';
					} else {
						$status = 'error';
						$notice = 'Failed to remove key!';
						$key = $keyring->get_resolved_certificate($keyid);
						$key = isset($key[0]) ? $key = $key[0] : null;						
					}
	 			}
	 			
	 			springnet_keyring_cert_display($key,$status,$notice);
		 } elseif('pullreq' == $action) {
		 	global $snrepo;
		 	
		 	$status = 'none';
		 	$message = '';
		 	
		 	if(($method = filter_input(INPUT_GET, 'method'))) {
		 		
		 		if('ignore' == $method) {
		 			$reqid = filter_input(INPUT_GET, 'reqid');
		 			$data = $snrepo->get_datum_from_id('cert_pullreq', $reqid);

		 			if($data && $snrepo->remove_data_with_id('cert_pullreq', $reqid)) {
		 				include SPRINGNET_DIR.'/plugin/models/class-notification-handler.php';
		 				$handler = new Notification_Handler();
		 				$handler->resolve_notification_id($data->repo_notif);
		 				$status = 'success';
		 				$message = "Ignored pull request from $data->repo_data";
		 			} else {
		 				$status = 'error';
		 				$message = "An error occured ignoring pull request -- does request still exist?";
		 			}

		 		} else if('accept' == $method) {
		 			
		 			include SPRINGNET_DIR.'/plugin/models/class-notification-handler.php';
		 			$reqid = filter_input(INPUT_GET, 'reqid');
		 			$data = $snrepo->get_datum_from_id('cert_pullreq', $reqid);
		 			$pulled = $keyring->perform_pull($data->repo_data); 
		 			if(!$pulled) {
		 				$status = 'error';
		 				$message = 'Something went wrong with the pull';
		 			}
		 			
		 			$service = new PK_Service_Model();
		 			$handler = new Notification_Handler();

		 			$node_certificate = $keyring->get_node_public_key();
		 			$response = $service->import($pulled, $node_certificate);
		 			
		 			if(!$response) {
		 				$status = 'error';
		 				$message = 'Failed to perform import service request';
		 			} elseif($keyring->set_node_certificate(
								$response['keyid'],
								$response['email'],
								$response['sigs'],
								$response['armor'])) {
						$status = 'success';
						$message = 'Performed pull from ' . $data->repo_data;
						$snrepo->remove_data_with_id('cert_pullreq', $reqid);
						$handler->resolve_notification_id($data->repo_notif);
					} else {
						$status = 'error';
						$message = 'Failed to update node certificate';
					}
		 		}

		 	} 
		 	$requests = $snrepo->get_data_from_tag('cert_pullreq');
		 	$requests = $requests ? $requests : array();
		 	springnet_keyring_pullreq_display($requests, $status, $message);
		 } else if('requestpull' == $action && ($keyid = filter_input(INPUT_GET, 'keyid'))) {
		 	$key = $keyring->get_resolved_certificate($keyid);
		 	$key = isset($key[0]) ? $key = $key[0] : null;
		 	$status = 'none';
		 	$reasons = '';
		 	
		 	if($key) {
		 		include SPRINGNET_DIR.'/plugin/models/class-gateway-handler.php';
		 		$uri = "spring://{$key->uidname}";
		 		$source = get_option('node_uri');
		 		$msg = \SpringDvs\Message::fromStr("service $uri/cert/pullreq?source={$source}");
		 		$response = Gateway_Handler::request_uri_first_response($uri, $msg);
		 		if($response) {
		 			try {
		 				$t = json_decode($response->getContentResponse()->getServiceText()->get(), true);
		 				$o = array_shift($t);
		 				$result = null;
		 				$result = isset($o['request']) ? $o['request']
		 							: ( isset($o['result']) ? $o['result']
		 								: null);
		 				
		 				
		 				if('ok' == $result) {
		 					$status = 'requested';
		 					$reason = 'Pull request made with node';
		 				} else if('queued' == $result) {
		 					$status = 'information';
		 					$reason = 'There is already a Pull Request queued at the node';		 					
		 				} else if('error' == $result) {
		 					$status = 'error';
		 					$reason = 'Node responded with an error';
		 				} else {
		 					$status = 'error';
		 					$reason = 'Node gave unexpected result from request';
		 				}
		 			} catch(\Exception $e) {
		 				$status = 'error';
		 				$reasons = 'Unexpected response from request';
		 			}
		 		} else {
		 			$status = 'error';
		 			$reasons = 'Failed to perform request';		 			
		 		}
		 	}
		 	
		 	springnet_keyring_cert_display($key, $status, $reason);
		 }

		
	} else if( ($keyid = filter_input(INPUT_GET, 'keyid')) ) {
		$key = $keyring->get_resolved_certificate($keyid);
		$key = isset($key[0]) ? $key = $key[0] : null;
		springnet_keyring_cert_display($key);
	} else {
		
		$paged = filter_input(INPUT_GET, 'paged');
		$paged = $paged ? $paged : 1;
		
		$limit = filter_input(INPUT_GET, 'limit');
		$limit = $limit ? $limit : 10;
		
		$rows = $keyring->get_uid_list($paged, $limit);
		$count = $keyring->get_certificate_count();
		$total_pages =  ceil($count / $limit);
		$total_pages = $total_pages < 1 ? 1 : $total_pages;
		
		
		springnet_keyring_display($rows, $count, $paged, $total_pages, $limit);
	}

}

function springnet_settings_controller() {
	$tab = filter_input(INPUT_GET, 'tab');
	switch($tab) {
		case 'certificate':
			return springnet_settings_certificate_controller();
		case 'network':
			return springnet_settings_network_controller();
		case 'general':
			return springnet_settings_general_controller();
		default:
			return springnet_settings_node_controller();
	}
}

function springnet_settings_node_controller() {
	require SPRINGNET_DIR.'/plugin/models/class-node-model.php';
	require_once SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';
	
	$tab = '';
	
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
	include __DIR__."/views/plugin-settings.php";
}

function springnet_settings_certificate_controller() {
	require_once SPRINGNET_DIR.'/plugin/models/class-keyring-model.php';
	$tab = 'certificate';
	$has_public_cert = true;
	
	$keyring = new Keyring_Model();
	$private_key = $keyring->get_node_private_key();
	$public_key = $keyring->get_node_public_key();
	
	if(null == $public_key) {
		$has_public_cert = false;
	}
	include __DIR__."/views/plugin-settings.php";
}

function springnet_settings_network_controller() {
	$tab = 'network';
	if(!get_option('node_hostname')) {
		update_option('node_hostname',  $_SERVER['HTTP_HOST']);  
	};
	include __DIR__."/views/plugin-settings.php";
}

function springnet_settings_general_controller() {
	$tab = 'general';
	include __DIR__."/views/plugin-settings.php";
}

function springnet_overview_controller() {
	include SPRINGNET_DIR.'/plugin/models/class-notification-handler.php';
	
	$notif = new Notification_Handler();
	$hidden = get_option('springnet_news_display') == 'hidden' ? true : false;

	if(!$hidden) {
		$response = wp_remote_get("http://spring-dvs.org/wp-json/wp/v2/posts?per_page=5&filter[category_name]=Network");
		$posts = json_decode(wp_remote_retrieve_body($response));
		$posts = !$posts ? array() : $posts;
	} else {
		$posts = array();	
	}

	$notifications = $notif->get_notifications(1);
	return springnet_overview_display($posts, $notifications, $hidden);
}

