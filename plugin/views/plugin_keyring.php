
<div class="wrap">

<h1>
	Keyring
	<a href="?page=springnet_keyring&action=import" class="page-title-action">
		Import
	</a>
</h1>

<table class="wp-list-table widefat fixed striped">
	<tbody class="the-list">
	<tr>
		<th class="manage-column">
			Name
		</th>
		<th class="manage-column">
			Email
		</th>
		
		<th class="manage-column">
			Key ID
		</th>
	</tr>

	<?php foreach($keyring as $row):?>
	<tr>
		<td><a href="?page=springnet_keyring&keyid=<?php echo $row->keyid; ?>">
				<?php echo $row->uidname; ?>
			</a>
		</td>
					
		<td><?php echo $row->uidemail; ?></td>
		<td><span class="springnet-key-id"><?php echo $row->keyid; ?></span></td>
	</tr>
	<?php endforeach; ?>
	
	<tr>
		<th class="manage-column">
			Name
		</th>
		<th class="manage-column">
			Email
		</th>
		<th class="manage-column">
			Key ID
		</th>
	</tr>
	</tbody>
</table>

</div>