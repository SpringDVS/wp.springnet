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
			if( ($certificate = filter_input(INPUT_POST, 'certificate')) ) {
				$response = $service->import($certificate);
				
				// Check to see if we have the certificate already
				if( ($subject = $keyring->get_certificate($response['keyid'])) ) {
					$response = $service->import($certificate, $subject);
				}
				
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
			
			springnet_keyring_import_display($status);
		}
	} else if( ($keyid = filter_input(INPUT_GET, 'keyid')) ) {
		$rows = $keyring->get_certificate($keyid);
	} else {
		$page = filter_input(INPUT_GET, 'page');
		$rows = $keyring->get_uid_list($page);
		springnet_keyring_display($rows);
	}

}