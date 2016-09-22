<?php
	defined( 'ABSPATH' ) or die( 'Error' );
	
	$tab = filter_input(INPUT_GET, 'tab');
	
?>
<div class="wrap">
<h1>SpringDVS Settings</h1>
 <?php settings_errors(); ?>
 
 <h2 class="nav-tab-wrapper">
 	<a href="?page=springnet_options" class="nav-tab
 		<?php echo null == $tab ? 'nav-tab-active' : '' ?>">Node</a>
    <a href="?page=springnet_options&tab=network" class="nav-tab
    	<?php echo 'network' == $tab ? 'nav-tab-active' : '' ?>">Network</a>
    <a href="?page=springnet_options&tab=certificate" class="nav-tab
    	<?php echo 'certificate' == $tab ? 'nav-tab-active' : '' ?>">Certificate</a>
</h2>
<?php 
	if(!$tab) {
		include(__DIR__.'/../prologues/settings_node.php');
		include(__DIR__.'/settings_node.php');
	} else if('network' == $tab) {
		include(__DIR__.'/settings_network.php');
	} else if('certificate' == $tab) {
		include(__DIR__.'/../prologues/settings_certificate.php');
		include(__DIR__.'/settings_certificate.php');
	}
?>
</div>

