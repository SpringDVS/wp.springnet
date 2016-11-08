<?php 
class Snetb_Category_Explorer extends WP_Widget {
	function __construct() {
	
		parent::__construct(
				'Springnet_Bulletins_Explorer',
				'Bulletin Category Explorer',
				array('description' => 'Display categories of bulletins on the Spring Network')
				);
	
		if (is_active_widget( false, false, $this->id_base )) {
			wp_enqueue_script('springnet_bulletin_popup_js', SPRINGNET_URL.'/modules/bulletin/widgets/bulletin_popup.js');
			wp_enqueue_script('springnet_bulletin_explorer_js', SPRINGNET_URL.'/modules/bulletin/widgets/explorer_client.js');
			wp_enqueue_style('springnet_bulletin_style_css', SPRINGNET_URL.'/modules/bulletin/widgets/bulletin_style.css');
			wp_enqueue_style('springnet_bulletin_explorer_css', SPRINGNET_URL.'/modules/bulletin/widgets/explorer_style.css');
	
			$nonce = wp_create_nonce('springnet_gateway_bulletin_explorer');
			wp_localize_script( 'springnet_bulletin_explorer_js', 'sn_gateway_explorer', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce' => $nonce,
			) );
		}
	}
	
	function form( $instance ) {
		$defaults = array(
				'network' => get_option('geonet_name').'.uk',
				'categories' => 'Events,Services',
		);
	
		$network = isset($instance['network']) ? $instance['network'] : get_option('geonet_name').'.uk';
		$categories = isset($instance['categories']) ? $instance['categories'] : 'Events,Services';

	
		?>
			<label for="<?php echo $this->get_field_id( 'network' ); ?>">Network:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'network' ); ?>" name="<?php echo $this->get_field_name( 'network' ); ?>" value="<?php echo esc_attr( $network ); ?>">
		
			<label for="<?php echo $this->get_field_id( 'categories' ); ?>">Categories:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'categories' ); ?>" name="<?php echo $this->get_field_name( 'categories' ); ?>" value="<?php echo esc_attr( $categories ); ?>">

			<?php
		} 
		
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['network'] = strip_tags($new_instance['network']);
			$instance['categories'] = strip_tags($new_instance['categories']);
			return $instance;
		}
		
		function widget( $args, $instance ) {
			extract( $args );
			echo $before_widget;
			$loader = "<img class='sdvs-loader' id='spring-explorer-loader' src='".SPRINGNET_URL."/res/img/load.gif'>";
			echo $before_title . 'Explore <em>' . $instance['network'] ."</em>$loader". $after_title;	
			
		
			$uri = $instance['network'];
			$categories = explode(",", $instance['categories']);
			if(empty($categories)) {
				$categories = array("Events","Services");
			}
			$selected = $categories[0];
			?>
			<div class="snetb-explorer snet-bulletin-widget" id="snetb-explorer-container">
			<h2 class="tabs">

			<?php foreach($categories as $cat): ?>
				<?php if($cat == $selected): ?>
					<div id="snetb-explorer-<?php echo $cat; ?>" class="tab-button tab-button-active">
					<a href="javascript:void(0);" onclick="SnetbExplorerClient.filterCat(<?php echo "'$cat'" ?>);"><?php echo $cat; ?></a>
					</div>
				<?php else: ?>
					<div id="snetb-explorer-<?php echo $cat; ?>" class="tab-button">
						<a href="javascript:void(0);" onclick="SnetbExplorerClient.filterCat(<?php echo "'$cat'" ?>);"><?php echo $cat; ?></a>
					</div>
				<?php endif; ?>
					
			
			<?php endforeach; ?>
			
			</h2> <!-- tabs -->
			<table class="listing">
				<tbody id="snetb-explorer-listing">
				</tbody>				
			</table>
			</div> <!-- snet-explorer -->
			<script type="text/javascript">SnetbExplorerClient.request(<?php echo "'$uri','$selected'" ?>);</script>
			<?php
			echo $after_widget;
		}
}