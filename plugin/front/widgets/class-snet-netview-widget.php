<?php 
class Snet_Netview_Widget extends WP_Widget {
	function __construct() {
	
		parent::__construct(
				'Springnet_Network_View',
				'SpringNet View',
				array('description' => 'Display topography of a network')
				);
	
		if (is_active_widget( false, false, $this->id_base )) {
			
			wp_enqueue_script('springnet_netview_widget_js', SPRINGNET_URL.'/res/js/snet-netview.js');
//			wp_enqueue_style('springnet_bulletin_explorer_css', plugins_url('springnet/modules/bulletin/widgets/explorer_style.css'));
	
			$nonce = wp_create_nonce('springnet_gateway_netview');
			wp_localize_script( 'springnet_netview_widget_js', 'sn_gateway_netview', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce' => $nonce,
			) );
		}
	}
	
	function form( $instance ) {
		$defaults = array(
				'network' => get_option('geonet_name').'.uk'
		);
	
		$network = isset($instance['network']) ? $instance['network'] : get_option('geonet_name').'.uk';
	
		?>
			<label for="<?php echo $this->get_field_id( 'network' ); ?>">Network:</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'network' ); ?>" name="<?php echo $this->get_field_name( 'network' ); ?>" value="<?php echo esc_attr( $network ); ?>">
			<?php
		} 
		
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['network'] = strip_tags($new_instance['network']);
			return $instance;
		}
		
		function widget( $args, $instance ) {
			extract( $args );
			echo $before_widget;
			$loader = "<img class='sdvs-loader' id='spring-netview-loader' src='".SPRINGNET_URL."/res/img/load.gif'>";
			echo $before_title . 'Live Topography of <em>' . $instance['network'] ."</em>$loader". $after_title;	
			
		
			$uri = $instance['network'];

			?>
			<div id="snet-netview-container" style="width: 100%;">
				<canvas id="snet-network-widget-viewport" width="550" height="550"></canvas> 
			</div>
			<script type="text/javascript">SnetNetViewClient.requestTopography(<?php echo "'$uri'" ?>);</script>
			<?php
			echo $after_widget;
		}
}