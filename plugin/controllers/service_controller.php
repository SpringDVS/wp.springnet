<?php


function springnet_service_request() {
	
	$request = trim(file_get_contents('php://input'));
	
	define('SPRING_IF', true);
	return springnet_protocol_handler($request);
}

function springnet_protocol_handler($request) {
	try {
		$msg = \SpringDvs\Message::fromStr($request);
	} catch(Exception $e) {
		return "104"; // MalformedContent
	}
	$args = array( 'post_type' => 'springnet_bulletin', 'posts_per_page' => 10 );
	$loop = new WP_Query( $args );
	
	while ( $loop->have_posts() ) : $loop->the_post();
		the_title();
	
		the_content();
	
	endwhile;
	
	return "200 6 foobar";
}