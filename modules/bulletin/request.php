<?php


if(isset($resource_path[0]) && !empty($resource_path[0])) {
	$uid = $resource_path[0];
	$post = get_post($uid);
	$out = array();

	if($post->post_type != 'springnet_bulletin') {
		return array();
	}

	$out['title'] = $post->post_title;
	$out['content'] = $post->post_content;
	$out['uid'] = $uid;
	$out['tags'] = array();
	foreach(wp_get_post_tags($uid) as $tag) {
		$out['tags'][] = $tag->name;
	}
	return $out;
}

$limit = isset($query['limit']) ? intval($query['limit']) : 10;

$args = array( 'post_type' => 'springnet_bulletin', 'posts_per_page' => $limit );

if(isset($query['tags'])) {
	$args['tag'] = $query['tags'];
}

$loop = new WP_Query( $args );
$out = array();
while ( $loop->have_posts() ) : $loop->the_post();
	$post = array();
	$uid = get_the_ID();
	$post['title'] = the_title('','', false);
	$post['uid'] = "$uid";
	$post['tags'] = array();
	
	foreach(wp_get_post_tags($uid) as $tag) {
		$post['tags'][] = $tag->name;
	}
	$out[] = $post;
endwhile;

return $out;