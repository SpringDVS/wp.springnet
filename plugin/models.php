<?php

function springnet_get_option($option) {
	global $wpdb;
	$table = $wpdb->prefix . 'sn_options';
	
	$prepared = $wpdb->prepare("SELECT optvalue FROM $table
										WHERE optname=%s", $option);
	return $wpdb->get_var($prepared);
}

function springnet_set_option($option, $value) {
	global $wpdb;
	$table = $wpdb->prefix . 'sn_options';

	$prepared = $wpdb->prepare("INSERT INTO $table
			(optname, optvalue) VALUES (%s, %s)
			ON DUPLICATE KEY UPDATE optvalue=%s", $option, $value, $value);
	return $wpdb->query($prepared);
}