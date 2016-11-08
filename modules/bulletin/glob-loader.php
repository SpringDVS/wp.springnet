<?php

require __DIR__.'/widgets/class-springnet-bulletins-latest.php';
require __DIR__.'/widgets/class-snetb-category-explorer.php';

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
					'taxonomies' => array('post_tag','category')
			)
			);
}

add_action('widgets_init', 'springnet_bulletin_register_widgets');
function springnet_bulletin_register_widgets() {
	register_widget('SpringNet_Bulletins_Latest');
	register_widget('Snetb_Category_Explorer');
}
