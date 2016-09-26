<?php

add_action( 'wp', 'springnet_service_hook' );
function springnet_service_hook() {
	if(is_page('spring')) {
		require SPRINGNET_DIR.'/plugin/front/service.php';
		$response = springnet_service_request();

		echo $response;
	}
}



add_filter( 'template_include', 'springnet_service_hook_template' );
function springnet_service_hook_template( $page_template )
{
	if ( is_page( 'spring' ) ) {
		$page_template = __DIR__ . '/views/service-hook-template.php';
	}
	return $page_template;
}




add_action( 'init', 'springnet_front_load_modules', 0 );
function springnet_front_load_modules() {
	$dirs = array_filter(glob(SPRINGNET_DIR.'/modules/*'), 'is_dir');

	foreach($dirs as $dir) {
		$path = $dir.'/front-loader.php';
		if(!file_exists($path)) continue;
		include $path;
	}
}

// --- Hooks ---
add_action( 'init', 'springnet_init');
function springnet_init() {
	do_action('springnet_init');
}