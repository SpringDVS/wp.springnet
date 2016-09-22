<div class="wrap">
	<h1>Organisation Profile</h1>
	<form method="post">
	<table class="form-table">
		<tr>
		<th>Organisation Name</th>
		<td>
				<input type="text" name="orgname"
					value="<?php echo springnet_get_option('orgprofile_name')?>">
		</td>
		</tr>
		
		<tr>
		<th>Website Address</th>
		<td>
				<input type="text" name="orguri"
					value="<?php echo springnet_get_option('orgprofile_uri')?>">
		</td>
		</tr>
		
		<tr>
		<th>Tags</th>
		<td>
				<input type="text" name="orgtags"
					value="<?php echo springnet_get_option('orgprofile_tags')?>">
		</td>
		</tr>

	</table>
	<?php submit_button(); ?>
	</form>
	
</div>
