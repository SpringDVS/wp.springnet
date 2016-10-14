<div class="wrap">
<?php springnet_uri_tag(); ?>
<h1>Import Certificate</h1>
<?php if('success' == $status): ?>
	<div class="notice notice-success"><p>Certificate for <em><?php echo $uid_name; ?></em> imported successful.
	Go back to <a href="?page=springnet_keyring">Keyring</a></p></div>
<?php elseif ('error' == $status): ?>
	<div class="notice notice-error"><p><?php echo $reason; ?></p></div>
<?php endif; ?>
<form method="post">
	<table class="form-table">
		<tr>
		
		<th valign="top">Certificate</th>
		
		<td>
			<textarea name="certificate" class="springnet-key-display" rows="20" cols="65"><?php echo $key; ?></textarea>
		</td>
		</tr>
	</table>
	<input type="submit" value="Import" class="button button-primary">
</form>
<form method="post">
	<table class="form-table">
		<tr>
		
		<th valign="top">Node URI</th>
		
		<td>
			<input type="text" name="request-uri" id="input-request-uri"> <input type="submit" id="button-request-uri" value="Request" class="button button-primary">
		</td>
		</tr>
	</table>
	

<form>

</form>
</div>