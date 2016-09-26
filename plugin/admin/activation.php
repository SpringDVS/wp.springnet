<?php

register_activation_hook(SPRINGNET_MAIN, 'springnet_activation_install');

function springnet_activation_install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'sn_certificates';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
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
	$wpdb->query($sql);
	
	$table2_name = $wpdb->prefix . 'sn_options';
	$sql2 = "CREATE TABLE IF NOT EXISTS $table2_name (
			optid		MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			optname		VARCHAR(128) NOT NULL,
			optvalue	LONGTEXT     NOT NULL,
			PRIMARY KEY       (optid),
			UNIQUE  KEY keyid (optname)
			) $charset_collate;";
	$wpdb->query($sql2);
	
	
	$table3_name = $wpdb->prefix . 'sn_notifications';
	$sql3 = "CREATE TABLE IF NOT EXISTS $table3_name (
			notif_id				MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			notif_title				VARCHAR(128) NOT NULL,
			notif_action			VARCHAR(320) NOT NULL,
			notif_source			VARCHAR(64)  NOT NULL,
			notif_active			BOOLEAN NOT NULL DEFAULT 0,
			notif_description		TEXT,
			PRIMARY KEY (notif_id)
			) $charset_collate;";
	$wpdb->query($sql3);
	$table4_name = $wpdb->prefix . 'sn_repo';
	$sql4 = "CREATE TABLE IF NOT EXISTS $table4_name (
			repo_id					MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
			repo_tag				VARCHAR(64) NOT NULL,
			repo_timestamp			TIMESTAMP,
			repo_notif				MEDIUMINT(9),
			repo_data				MEDIUMTEXT,
			PRIMARY KEY (repo_id),
			INDEX repo_tag_id (repo_tag,repo_id)
			) $charset_collate;";
	$wpdb->query($sql4);
}
