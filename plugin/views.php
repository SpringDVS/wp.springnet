<?php
add_filter( 'template_include', 'springnet_service_hook_template' );

function springnet_service_hook_template( $page_template )
{
	if ( is_page( 'spring' ) ) {
		$page_template = __DIR__ . '/views/service-hook-template.php';
	}
	return $page_template;
}

function springnet_settings_display() {
	
	include __DIR__."/views/plugin_settings.php";
}

function springnet_overview_display() {
	include __DIR__."/views/plugin_overview.php";
}