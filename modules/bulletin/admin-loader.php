<?php
require __DIR__.'/glob-loader.php';

add_action('springnet_menu', 'springnet_bulletin_menu');
function springnet_bulletin_menu() {
	add_submenu_page( SN_MENU_SLUG, 'Bulletins', 'Bulletins', 'edit_pages',
			'edit.php?post_type=springnet_bulletin'
			);
}

