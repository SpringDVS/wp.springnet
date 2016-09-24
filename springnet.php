<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/*
Plugin Name: SpringNet
Plugin URI:  http://spring-dvs.org
Description: Plugin for running a Spring Network node
Version:     0.1.0
Author:      The Care Connections Initiative c.i.c
Author URI:  http://spring-dvs.org
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: springnet
*/

defined( 'ABSPATH' ) or die( 'Error' );

define('SPRINGNET_MAIN', __FILE__);
define('SPRINGNET_DIR', __DIR__);
define('SPRINGNET_VERSION', '0.1.0');

require __DIR__.'/autoload.php';

require __DIR__.'/plugin/activation.php';
require __DIR__.'/plugin/update.php';

require __DIR__.'/plugin/models.php';
require __DIR__.'/plugin/controllers.php';
require __DIR__.'/plugin/views.php';
require __DIR__.'/plugin/actions.php';

?>
