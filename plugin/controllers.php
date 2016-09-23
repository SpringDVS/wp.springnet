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
			$name = '';
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
				}
			}
			
			springnet_keyring_import_display($status, $name);
			
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
		 }

		
	} else if( ($keyid = filter_input(INPUT_GET, 'keyid')) ) {
		$key = $keyring->get_resolved_certificate($keyid);
		
		$key = isset($key[0]) ? $key = $key[0] : null;
		
		springnet_keyring_cert_display($key);
	} else {
		$page = filter_input(INPUT_GET, 'page');
		$rows = $keyring->get_uid_list($page);
		springnet_keyring_display($rows);
	}

}