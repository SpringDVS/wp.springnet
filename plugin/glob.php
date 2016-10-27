<?php

require SPRINGNET_DIR.'/plugin/front/widgets/class-snet-netview-widget.php';

add_action('widgets_init', 'springnet_register_widgets');
function springnet_register_widgets() {
	register_widget('Snet_Netview_Widget');
}