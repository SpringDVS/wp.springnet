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