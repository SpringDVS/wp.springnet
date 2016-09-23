<div class="wrap">
<h1>Import Certificate</h1>
<?php if('success' == $status): ?>
	<div class="notice notice-success">Certificate imported successful.
	Go back to <a href="?page=springnet_keyring">Keyring</a></div>
<?php elseif ('error' == $status): ?>
	<div class="notice notice-error">There was an error importing certificate.
	Please contact <a href="mailto:spring@care-connections.org">
		spring@care-connections.org
		</a> 
	if it continues to fail</div>
<?php endif; ?>
<form method="post">
	<table class="form-table">
		<tr>
		
		<th valign="top">Certificate</th>
		
		<td>
			<textarea name="certificate" class="springnet-key-display" rows="12" cols="65"></textarea>
		</td>
		</tr>
	</table>	
	<input type="submit" value="Import" class="button button-primary">
		
		
	
</form>
</div>