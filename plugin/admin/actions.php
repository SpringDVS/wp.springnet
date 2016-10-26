<?php
define('SN_MENU_SLUG', 'springnet');
define('SN_MODULE_ADMIN', '?page=springnet&module=');


add_action( 'admin_menu', 'springnet_menu');
function springnet_menu() {

	$slug = SN_MENU_SLUG;
	add_menu_page( 'SpringNet Overview', 'SpringNet', 'edit_pages',
					$slug, 'springnet_overview_controller');


	// Keyring is built in
	add_submenu_page( $slug, 'Keyring', 'Keyring', 'edit_pages',
					'springnet_keyring', 'springnet_keyring_controller');
	
	if( is_admin() ) {
		add_options_page('SpringNet Options', 'SpringNet', 'manage_options',
				'springnet_options', 'springnet_settings_controller');
		
		add_action( 'admin_init', 'springnet_register_settings' );
	}
	
	do_action('springnet_menu');
}

function springnet_register_settings() {

	register_setting('springnet-network-options', 'node_springname');
	register_setting('springnet-network-options', 'node_hostname');
	register_setting('springnet-network-options', 'node_enabled');
	register_setting('springnet-network-options', 'node_registered');
	register_setting('springnet-network-options', 'node_uri');

	register_setting('springnet-network-options', 'geonet_name');
	register_setting('springnet-network-options', 'geonet_hostname');
	register_setting('springnet-network-options', 'geonet_address');
	register_setting('springnet-network-options', 'geonet_resource');
	register_setting('springnet-network-options', 'geonet_token');
	
	register_setting('springnet-certificate-options', 'cert_accept_pull');
	
	register_setting('springnet-general-options', 'springnet_news_display');

}

add_action( 'admin_enqueue_scripts', 'springnet_enqueue_scripts');

function springnet_enqueue_scripts($hook) {
	if('settings_page_springnet_options' == $hook) {
		springnet_enqueue_script_plugin_settings();
	}
	
	wp_enqueue_style( 'springnet_admin',
			SPRINGNET_URL.'/res/css/springnet_admin.css');
}


function springnet_enqueue_script_plugin_settings() {
	wp_enqueue_script( 'plugin_settings',
			SPRINGNET_URL.'/res/js/plugin_settings.js',
			array('jquery') );
	
	$nonce = wp_create_nonce('springnet_script_plugin_settings');
	wp_localize_script( 'plugin_settings', 'sn_settings', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => $nonce,
		) );
}



add_action( 'init', 'springnet_load_modules', 0 );
function springnet_load_modules() {
	$dirs = array_filter(glob(SPRINGNET_DIR.'/modules/*'), 'is_dir');
	
	foreach($dirs as $dir) {
		$path = $dir.'/admin-loader.php';
		if(!file_exists($path)) continue;
		include $path;
	}	
}


// --- Hooks ---
add_action( 'init', 'springnet_init');
function springnet_init() {
	do_action('springnet_init');
}