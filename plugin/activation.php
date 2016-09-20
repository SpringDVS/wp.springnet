<?php

register_activation_hook(SPRINGNET_MAIN, 'springnet_activation_install');

function springnet_activation_install() {
	
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'snkr_certificates';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
				certid 		MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
				keyid		VARCHAR(32) NOT NULL,
				uidname		VARCHAR(255) NOT NULL,
				uidemail	VARCHAR(255) NOT NULL,
				sigs		TEXT,
				armor		TEXT NOT NULL,
				owned		BOOLEAN NOT NULL DEFAULT 0,
				PRIMARY KEY       (certid),
				UNIQUE  KEY keyid (keyid)
			) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql );
}