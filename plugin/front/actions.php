<?php

 add_action( 'springnet_service', 'springnet_process' );
 function springnet_process() {
	require SPRINGNET_DIR.'/plugin/front/service.php';
 	$response = springnet_service_request();

 	echo $response;
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

add_action( 'parse_request', 'springnet_service' );
function springnet_service( &$wp ) {

	if ( array_key_exists( 'springnet_hook', $wp->query_vars ) 
	|| (array_key_exists( 'pagename', $wp->query_vars) 
		&& $wp->query_vars['pagename'] == 'spring' ) ) {
		do_action('springnet_service');
		exit();
	}
	return;
}

add_action( 'init', 'springnet_url_rewrite' );
function springnet_url_rewrite() {
	add_rewrite_rule('^spring/?$', 'index.php?springnet_hook=1', 'top');
}

add_filter( 'query_vars', 'springnet_service_var' );
function springnet_service_var( $query_vars ) {
	$query_vars[] = 'springnet_hook';
	return $query_vars;
}