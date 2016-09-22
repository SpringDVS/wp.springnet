<?php
add_action('springnet_menu', 'springnet_orgprofile_menu');
function springnet_orgprofile_menu() {
	
	add_submenu_page( SN_MENU_SLUG, 'OrgProfile', 'Organisation Profile', 'edit_pages',
			'springnet_orgprofile', 'springnet_orgprofile_controller'
			);
}

function springnet_orgprofile_controller() {
	if( ($orgname = filter_input(INPUT_POST, 'orgname')) ) {
		$orguri = filter_input(INPUT_POST, 'orguri');
		$orgtags = filter_input(INPUT_POST, 'orgtags');
		springnet_set_option('orgprofile_name', $orgname);
		springnet_set_option('orgprofile_uri', $orguri);
		springnet_set_option('orgprofile_tags', $orgtags);
	}

	springnet_orgprofile_display();
}

function springnet_orgprofile_display() {
	include __DIR__.'/view_admin.php';
}