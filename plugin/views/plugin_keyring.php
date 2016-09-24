
<div class="wrap">
<?php springnet_uri_tag(); ?>
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

<div class="tablenav bottom">
	<div class="tablenav-pages">
		<span class=displaying-num"><?php echo $count; ?> items</span>
		<span class="pagination-links">
			<?php if($paged > 1):?>
				<a class="next-page" href="?page=springnet_keyring<?php echo $limit != 10 ? '&limit='.$limit : '';?>">
					<span class="screen-reader-text">First page</span>
					<span aria-hidden="true">&laquo;</span>
				<a class="last-page" href="?page=springnet_keyring&paged=<?php echo $paged-1; echo $limit != 10 ? '&limit='.$limit : '';?>">
					<span class="screen-reader-text">Previous page</span>
					<span aria-hidden="true">&lsaquo;</span></a>
				</a>
			<?php else: ?>
					<span class="tablenav-pages-navspan">&laquo;</span>
					<span class="tablenav-pages-navspan">&lsaquo;</span></a>
			<?php endif; ?>
			<span class="screen-reader-text">Current Page</span>
			<span class="paging-input">
				<span class="tablenav-paging-text">
					<?php echo $paged; ?> of
					<span class="total-pages">
						<?php echo $total_pages; ?>
					</span>
				</span>
			</span>
			<?php if($paged < $total_pages):?>
				<a class="next-page" href="?page=springnet_keyring&paged=<?php echo $paged+1; echo $limit != 10 ? '&limit='.$limit : '';?>">
					<span class="screen-reader-text">Next page</span>
					<span aria-hidden="true">&rsaquo;</span></a>
				<a class="last-page" href="?page=springnet_keyring&paged=<?php echo $total_pages; echo $limit != 10 ? '&limit='.$limit : '';?>">
					<span class="screen-reader-text">Last page</span>
					<span aria-hidden="true">&raquo;</span>
				</a>
			<?php else: ?>
					<span class="tablenav-pages-navspan">&rsaquo;</span>
					<span class="tablenav-pages-navspan">&raquo;</span>
			<?php endif; ?>
		</span> 
	</div>
</div>
</div>