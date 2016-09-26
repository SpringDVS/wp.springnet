<?php
	defined( 'ABSPATH' ) or die( 'Error' );
	
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
	<a href="?page=springnet_options&tab=general" class="nav-tab
    	<?php echo 'general' == $tab ? 'nav-tab-active' : '' ?>">General</a>
</h2>
<?php 
	if(!$tab) {
		include(__DIR__.'/settings-node.php');
	} else if('network' == $tab) {
		include(__DIR__.'/settings-network.php');
	} else if('certificate' == $tab) {
		include(__DIR__.'/settings-certificate.php');
	} else if('general' == $tab) {
		include(__DIR__.'/settings-general.php');
	}
?>
</div>

