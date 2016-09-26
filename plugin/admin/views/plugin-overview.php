<div class="wrap">
	<?php springnet_uri_tag(); ?>
	<h1>Overview</h1>
	
	<div class="metabox-holder">
		<div style="width:50%;" class="postbox-container">
			<div style="margin: 5px;" class="meta-box-sortables ui-sortable">


				<div class="postbox">	
					<h2 class="hndle ui-sortable-handle">
						<span>Notifications</span>
					</h2>
					
					<div class="inside">
					<?php if(!$notifications): ?>
						<div class="activity-block">
						No notifications
						</div>
					<?php else: ?>
						<div class="activity-block">
						<ul class="springnet-notification-list">
						<?php foreach($notifications as $notif): ?>
							<li>
								<a href="?<?php echo $notif->notif_action; ?>&_actionid=<?php echo $notif->notif_id; ?>">
									<?php echo $notif->notif_title; ?>
								</a>	<br>
								<span class="source"><?php echo $notif->notif_source; ?></span>
								<div class="description"><?php echo $notif->notif_description; ?></div>
							</li>
						<?php endforeach;?>
						</div>
						</ul>
					<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
		

		<div style="width:50%;" class="postbox-container">
			<div style="margin: 5px;" class="meta-box-sortables ui-sortable">
			
			<?php if(!$hidden): ?>
				<div class="postbox">
					<h2 class="hndle ui-sortable-handle">
						<span>Network News</span>
					</h2>
					
					<div class="inside">
						<div class="activity-block">
						<div class="rss-widget">
						<ul>			
						<?php
							$index = 0;
							foreach($posts as $post) {
								echo '<li><a style="text-decoration:none;" target="_BLANK" class="rsswidget" href="'.$post->link.'">';
								echo $post->title->rendered;
								
								echo '</a><br><span class="rss-date">'.
									date(get_option('date_format'), strtotime($post->date)).
								'</span>';
								if($index == 0) {
									echo '<div class="rssSummary">';
									echo $post->excerpt->rendered; 
									echo '<hr>';
									echo '</div>';
									
								}
								echo '</li>';
								$index++;
							}
						?>
						</ul>
						
						</div>
						</div>
					</div>
				</div>
				<?php endif; ?>

		
				<div class="postbox">
					<h2 class="hndle ui-sortable-handle">
						<span>Details</span>
					</h2>
					
					<div class="inside">
						<div class="activity-block">
						<strong>Version:</strong> <?php echo SPRINGNET_VERSION; ?>
						</div>
					</div>
				</div>
		
			</div>
		</div>	
	
	</div>
</div>



