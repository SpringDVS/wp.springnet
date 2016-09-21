<?php
require SPRINGNET_DIR.'/plugin/handlers.php';

add_action( 'wp', 'springnet_service_hook' );

function springnet_service_hook() {
	if(is_page('spring')) {
		require __DIR__.'/controllers/service_controller.php';
		$response = springnet_service_request();
		echo $response;
	}
}

add_action( 'init', 'springnet_bulletin_post_type_register' );

function springnet_bulletin_post_type_register() {
	register_post_type( 'springnet_bulletin',
		array(
			'labels' => array(
				'name' => __('Spring Network Bulletins'),
				'singular_name' => __('Bulletin'),
			),
			'show_in_menu' => false,
			'public' => true,
			'has_archive' => true,
		)	
	);
}


add_action( 'admin_menu', 'springnet_menu');
function springnet_menu() {

	$slug = 'springnet_overview';
	add_menu_page( 'SpringNet Overview', 'SpringNet', 'edit_pages',
					'springnet_overview', 'springnet_overview_display');

	add_submenu_page( $slug, 'Bulletins', 'Bulletins', 'edit_pages',
					'edit.php?post_type=springnet_bulletin'
	);

	add_submenu_page( $slug, 'Keyring', 'Keyring', 'edit_pages',
					'springdvs_keyring', 'springdvs_keyring_display');
	
	if( is_admin() ) {
		add_options_page('SpringNet Options', 'SpringNet', 'manage_options',
				'springnet', 'springnet_settings_display');
		
		add_action( 'admin_init', 'springnet_register_settings' );
	}
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

}

add_action( 'admin_enqueue_scripts', 'springnet_enqueue_scripts');

function springnet_enqueue_scripts($hook) {
	if('settings_page_springnet' == $hook) {
		springnet_enqueue_script_plugin_settings();
	}
}

function springnet_enqueue_script_plugin_settings() {
	wp_enqueue_script( 'plugin_settings',
			plugins_url('springnet/res/js/plugin_settings.js'),
			array('jquery') );
	
	$nonce = wp_create_nonce('springnet_script_plugin_settings');
	wp_localize_script( 'plugin_settings', 'sn_settings', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => $nonce,
		) );
}