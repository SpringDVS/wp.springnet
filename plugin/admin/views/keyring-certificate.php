<div class="wrap">
<?php springnet_uri_tag(); ?>

<?php if('success' == $status):?>
	<h1>Certificate</h1>
	
	<div class="notice notice-success"><p><?php echo $notice; ?></p></div>
	</div>
	<a href="?page=springnet_keyring">Back to keyring</a>
<?php return; endif;?>



<?php if(!$key):?>
	<h1>Certificate</h1>
	<div class="notice notice-error">Unable to load certificate</div>
	
	</div>
	<a href="?page=springnet_keyring">Back to keyring</a>
<?php return; endif;?>



<h1>
	Certificate
	<?php if(!$key->signed):?>
		<a href="?page=springnet_keyring&keyid=<?php echo $key->keyid; ?>&action=sign"
			class="page-title-action">
				Sign Certificate
		</a>
	<?php else: ?>
		<a href="?page=springnet_keyring&keyid=<?php echo $key->keyid; ?>&action=requestpull"
			class="page-title-action">
				Make Pull Request
		</a>
	<?php endif; ?>
	
</h1>

<?php if('information' == $status):?>
	<div class="notice notice-info is-dismissible"><p><?php echo $notice; ?></p></div>
	</div>
<?php endif;?>

<?php if('error' == $status):?>
	<div class="notice notice-error is-dismissible"><p><?php echo $notice; ?></p></div>
	</div>
<?php endif;?>

<?php if('requested' == $status):?>
	<div class="notice notice-success is-dismissible"><p><?php echo $notice; ?></p></div>
	</div>
<?php endif;?>


<a href="?page=springnet_keyring">Back to keyring</a>
<table class="form-table">
		<tr>
			<th valign="top">Name</th>
			
			<td>
				<?php echo $key->uidname; ?>
			</td>
			
		</tr>

		<tr>
			<th valign="top">Email</th>
			
			<td>
				<?php echo $key->uidemail; ?>
			</td>
			
		</tr>

		<tr>
			<th valign="top">Signatures</th>
			
			<td>
				<ul>
				<?php foreach($key->sigs as $sig): ?>
				
				<li>
					<span class="springnet-key-id">
						<?php echo $sig['keyid']; ?>&nbsp; &nbsp;
					</span> 
					(<em><?php echo $sig['name']; ?></em>)
				
				<?php endforeach; ?>
				</ul>
			</td>
			
		</tr>

		<tr>
			<th valign="top">Public Key</th>
			
			<td>
				<textarea name="certificate" class="springnet-key-display" rows="12" cols="65"><?php echo $key->armor; ?></textarea>
			</td>
		</tr>
	</table>
</div>

<a href="?page=springnet_keyring&keyid=<?php echo $key->keyid; ?>&action=remove"
	class="delete springnet-risky button-primary">
				Remove Certificate
</a>