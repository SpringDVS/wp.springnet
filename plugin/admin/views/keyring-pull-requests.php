
<div class="wrap">
<?php springnet_uri_tag(); ?>
<h1>
	Certificate Pull Requests
</h1>
<a href="?page=springnet_keyring">Back to keyring</a>
<?php if('success' == $status):?>
<div class="notice notice-success"><p><?php echo $message; ?></p></div>
<?php elseif('error' == $status): ?>
	<div class="notice notice-error"><p><?php echo $message; ?></p></div>
<?php endif; ?>

<table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
	<tbody class="the-list">
	<tr>
		<th class="manage-column">
			From
		</th>
		<th class="manage-column">

		</th>
	</tr>

	<?php if(!isset($requests[0])): ?>
	<tr>
			<td colspan="2">No pull requests</td>
	</tr>
	<?php else: ?>
		<?php foreach($requests as $row):?>
		<tr>
			<td>
					<?php echo $row->repo_data; ?>
			</td>
			<td style="text-align: right">
				<a href="?page=springnet_keyring&action=pullreq&method=accept&reqid=<?php echo $row->repo_id; ?>" class="page-title-action" >Accept</a>
				<a href="?page=springnet_keyring&action=pullreq&method=ignore&reqid=<?php echo $row->repo_id; ?>" 
					class="page-title-action" >
						Ignore
				</a>
			</td>
		</tr>
		
		<?php endforeach; ?>
	<?php endif; ?>
		
	<tr>
		<th class="manage-column">
			From
		</th>
		<th class="manage-column">

		</th>
	</tr>
	</tbody>
</table>
