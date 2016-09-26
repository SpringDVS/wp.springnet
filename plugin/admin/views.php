<?php

function springnet_settings_display() {
	include __DIR__."/views/plugin-settings.php";
}

function springnet_overview_display($posts, $notifications, $hidden) {
	include __DIR__."/views/plugin-overview.php";
}

function springnet_keyring_display($keyring, $count, $paged, $total_pages, $limit) {
	include __DIR__."/views/plugin-keyring.php";
}

function springnet_keyring_import_display($status, $uid_name) {
	include __DIR__."/views/keyring-import.php";
}

function springnet_keyring_unlock_display($reason, $status,$redirect) {
	include __DIR__."/views/keyring-unlock.php";
}

function springnet_keyring_cert_display($key, $status = 'none', $notice='') {
	include __DIR__."/views/keyring-certificate.php";
}

function springnet_keyring_pullreq_display($requests, $status, $message) {
	include __DIR__."/views/keyring-pull-requests.php";
}