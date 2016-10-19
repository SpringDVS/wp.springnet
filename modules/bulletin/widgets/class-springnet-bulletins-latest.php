<?php

class SpringNet_Bulletins_Latest extends WP_Widget {
	function __construct() {

		parent::__construct(
			'SpringNet_Bulletins_Latest',
			'Latest Bulletins',
			array('description' => 'Display lastest bulletins on the Spring Network')
			);
		
			if (is_active_widget( false, false, $this->id_base )) {
				wp_enqueue_script('springnet_bulletin_lastest_js', plugins_url('springnet/modules/bulletin/widgets/latest_client.js'));
				wp_enqueue_style('springnet_bulletin_lastest_css', plugins_url('springnet/modules/bulletin/widgets/latest_style.css'));
				
				$nonce = wp_create_nonce('springnet_gateway_bulletin');
				wp_localize_script( 'springnet_bulletin_lastest_js', 'sn_gateway_bulletin', array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce' => $nonce,
				) );
			}
	}
	
	function form( $instance ) {
		$defaults = array(
			'network' => get_option('geonet_name').'.uk',
			'query' => '',
		);
		
		$network = isset($instance['network']) ? $instance['network'] : get_option('geonet_name').'.uk';
		$query = isset($instance['query']) ? $instance['query'] : '';

		?>
		<label for="<?php echo $this->get_field_id( 'network' ); ?>">Network:</label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'network' ); ?>" name="<?php echo $this->get_field_name( 'network' ); ?>" value="<?php echo esc_attr( $network ); ?>">
		<label for="<?php echo $this->get_field_id( 'query' ); ?>">Filter:</label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'query' ); ?>" name="<?php echo $this->get_field_name( 'query' ); ?>" value="<?php echo esc_attr( $query ); ?>">
		<?php
	} 
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['network'] = strip_tags($new_instance['network']);
		$instance['query'] = strip_tags($new_instance['query']);
		return $instance;
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		$loader = "<img class='sdvs-loader' id='spring-bulletin-loader' src=".plugins_url('springnet/res/img/load.gif').">";
		echo $before_title . 'Latest Bulletins on <em>' . $instance['network'] ."</em>$loader". $after_title;	
		
		$uri = $instance['network'];
		$query = $instance['query'];
		?>
		<div class="spring-bulletin">
			<div>Filter: <span id='sdvs-bulletin-list-filter'>none</span> <a href='javascript:void(0);' onclick='SNetBulletinsLatestCli.rerequest("")' class='reset'>reset</a></div>
			<table class="wp-list-table widefat  striped main">
				<tbody class="the-list" id="sdvs-bulletin-list-body">
				</tbody>
			</table>
		</div>
		<script type="text/javascript">SNetBulletinsLatestCli.request(<?php echo "'$uri','$query'" ?>);</script>
		<?php
		echo $after_widget;
	}
}


