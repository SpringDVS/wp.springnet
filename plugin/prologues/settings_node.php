<?php
require SPRINGNET_DIR.'/plugin/models/class-node-model.php';
$node = new Node_Model();
$is_registered =  $node->is_registered();
$is_enabled = false;
if($is_registered) {
	$is_enabled = $node->is_enabled();
}
