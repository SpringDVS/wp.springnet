<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
Plugin Name: SpringNet
Plugin URI:  http://spring-dvs.org
Description: Plugin for running a Spring Network node off the back of Wordpress
Version:     0.1.2
Author:      The Care Connections Initiative c.i.c
Author URI:  http://spring-dvs.org
License:     GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages
Text Domain: springnet
*/


defined( 'ABSPATH' ) or die( 'Error' );

define('SPRINGNET_MAIN', __FILE__);
define('SPRINGNET_DIR', __DIR__);
define('SPRINGNET_VERSION', '0.1.2');

require __DIR__.'/autoload.php';

if(is_admin()) {
	require __DIR__.'/plugin/admin/activation.php';
	require __DIR__.'/plugin/admin/update.php';
}

require __DIR__.'/plugin/models.php';
require __DIR__.'/plugin/models/class-repo-handler.php';
global $snrepo;
$snrepo = new Repo_Handler();

if(is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX) ) {
	require __DIR__.'/plugin/admin/controllers.php';
	require __DIR__.'/plugin/admin/views.php';
	require __DIR__.'/plugin/admin/actions.php';
} else {
	require __DIR__.'/plugin/admin/ajax-handlers.php';
	require __DIR__.'/plugin/front/actions.php';
}




?>
