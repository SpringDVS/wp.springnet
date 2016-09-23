<div class="wrap">
<?php springnet_uri_tag(); ?>
<h1>Unlock Private Key</h1>
<?php if(!is_admin()):?>
<div class="notice notice-error"><p>
	User requires <strong>Administrator</strong> role to unlock the 
	node's private key.<a href="<?php echo $redirect; ?>">
		go back</a>.
</p></div></div>
<?php return; endif; ?>
<?php if($status == 'error'): ?>
<div class="notice notice-error"><p>
	Failed to perform action! Check your passphrase is correct or 
	<a href="<?php echo $redirect; ?>">
		go back</a>.
</p></div>
<?php elseif($status == 'success'): ?>
<div class="notice notice-success"><p>
	Performed action successfully! Please <a href="<?php echo $redirect; ?>">
		go back</a>.
</p></div>
<?php endif;?>

<div class="notice notice-info is-dismissible"><p>
	Reason for unlocking:
	<strong><?php echo $reason; ?></strong>
</p></div>

<form method="post">
<table class="form-table">
<tbody>
<tr>
	<th>Passphrase</th>
	<td><input type="password" name="passphrase"></td>
</tr>	
</tbody>
</table>
<input type="submit" value="Unlock" class="button button-primary">
</form>
</div>