<?php
defined( 'ABSPATH' ) or die( 'Error' );

$method = filter_input(INPUT_GET, 'method');
?>
<div class="notice notice-error" id="error-banner" style="display:none;"></div>
<?php

if(!$has_public_cert): ?>
	<div class="notice notice-error" id="error-banner">
		<p>Node does not have a public certificate! Please use form to generate certificate.</p>
	</div>	
	<?php if(!$method): ?>
	
		<table class="form-table">
		<tr valign="top">
		<th>Node</th>
		<td><span id="info-spring-uri">
			<?php echo esc_attr( get_option('node_uri') ); ?>
		</span></td>
		</tr>
		
		<tr valign="top">
		<th>Contact Email</th>
		<td><input type="text" id="input_cert_email"></td>
		</tr>
		
		<tr valign="top">
		<th>Passphrase</th>
		<td><input type="password" id="input_cert_passphrase"></td>
		</tr>
		
		<tr valign="top">
		<th>Recheck Passphrase</th>
		<td><input type="password" id="input_cert_passcheck"></td>
		</tr>
		
		</table>
		<button class="button button-primary" id="cert-generate-button">
			Generate Certificate
		</button>
		
		<?php return; ?>
	<?php endif; ?>
<?php endif; ?>

<form method="post" action="options.php">
<?php 
settings_fields( 'springnet-certificate-options' );
//do_settings_sections('springnet-network-options');
$pull = get_option('cert_accept_pull');

$pull = $pull ? $pull : 'accept';
?>
<table class="form-table">
	<tr valign="top">
		<th>Certificate Pull Requests</th>
		<td>
		<select type="checkbox" name="cert_accept_pull">
			<option value="accept" <?php if('accept' == $pull){echo 'selected';}?>>Automatically Accept</option>
			<option value="notification" <?php if('notification' == $pull){echo 'selected';}?>>Notify</option>
		</select>
	</tr>
</table>
<?php submit_button(); ?>
<!-- <input type="submit" value="Save settings" class="button button-primary"> -->
</form>
<h2>Public Key</h2>
<textarea rows="12" cols="65" class="springnet-key-display"><?php echo $public_key; ?></textarea>

<h2>Private Key</h2>
<textarea rows="12" cols="65" class="springnet-key-display"><?php echo $private_key; ?></textarea>
