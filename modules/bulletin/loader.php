<?php
add_action('springnet_init', 'springnet_bulletin_post_type_register');
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
					'taxonomies' => array('post_tag')
			)
			);
}

add_action('springnet_menu', 'springnet_bulletin_menu');
function springnet_bulletin_menu() {
	add_submenu_page( SN_MENU_SLUG, 'Bulletins', 'Bulletins', 'edit_pages',
			'edit.php?post_type=springnet_bulletin'
			);
}