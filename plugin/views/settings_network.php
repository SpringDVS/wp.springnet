<?php defined( 'ABSPATH' ) or die( 'Error' ); ?>

<form method="post" action="options.php">
<?php
settings_fields( 'springnet-network-options' );
do_settings_sections('springnet-network-options');
?>
<h3>Local Node</h3>

<table class="form-table">

	
	<tr valign="top">
	<th>Springname</th>
	<td><input type="text" name="node_springname" value="<?php echo esc_attr(
			get_option('node_springname') );?>" id="input-node-springname">
	</td>
	</tr>
	
	<tr valign="top">
	<th>Hostname</th>
	<td><input type="text" name="node_hostname" value="<?php echo esc_attr(
			get_option('node_hostname') );?>"></td>
	</tr>
</table>
	
<hr>

<h3>Network</h3>
	
<table class="form-table">	
	<tr valign="top">
	<th>GeoNetwork</th>
	<td><input type="text" name="geonet_name" value="<?php echo esc_attr(
			get_option('geonet_name') );?>" id="input-geonet-name">
			<button id="lookup-primary">Lookup</button>
	</td>
	</tr>
	<th>Spring URI</th>
	<td>
			<span id="info-spring-uri">
				spring://<?php echo esc_attr( get_option('node_uri') ); ?>
			</span>
			
			<input type="hidden" id="input-spring-uri-h" 
				name="node_uri" value="<?php echo esc_attr(
						get_option('node_uri') );?>" 
				id="input-geonet-hostname-h">
	</td>
					
	</tr>

	<th>Hostname</th>
	<td>
		<input type="text" value="<?php echo esc_attr(
			get_option('geonet_hostname') );?>" id="input-geonet-hostname"
			disabled>
		<input type="hidden" name="geonet_hostname" value="<?php echo esc_attr(
			get_option('geonet_hostname') );?>" id="input-geonet-hostname-h">				
	</td>
	</tr>
	
	<th>Address</th>
	<td>
		<input type="text" value="<?php echo esc_attr(
			get_option('geonet_address') );?>" id="input-geonet-address" 
			disabled>
		<input type="hidden" name="geonet_address" value="<?php echo esc_attr(
			get_option('geonet_address') );?>" id="input-geonet-address-h"></td>
	</tr>
	
	<th>Service Resource</th>
	<td>
		<input type="text" value="<?php echo esc_attr(
			get_option('geonet_resource') );?>" id="input-geonet-resource" 
			disabled>
		<input type="hidden" name="geonet_resource" value="<?php echo esc_attr(
			get_option('geonet_resource') );?>" id="input-geonet-resource-h"></td>
	</tr>
	
	<th>Validation Token</th>
	<td><textarea type="text" name="geonet_token"><?php echo esc_attr(
			get_option('geonet_token') );?></textarea></td>
	</tr>
	</table>
	
	<?php submit_button(); ?>
</form>