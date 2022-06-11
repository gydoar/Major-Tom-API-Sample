<?php

/**
 * 
 * Plugin Name:       Major Tom API
 * Plugin URI:        https://andrevega.com
 * Description:       Custom RestAPI endpoint.
 * Version:           0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Andrés Vega
 * Author URI:        https://andrevega.com/
 */

// .../wp-json/mt/v1/posts/{category_name}/{number_posts}/{order = ASC | DESC}

function mt_posts($request)
{

    $category = $request['category'];
    $npage = $request['npage'];
    $order = $request['order'];

    $args = [
        'post_type' => 'post',
        'category_name' => $category,
        'numberposts' => 99999,
        'posts_per_page' => $npage,
        'meta_key'  => 'event_date',
        'orderby'   => 'meta_value_num',
        'order'     => $order,
    ];

    $posts = get_posts($args);
    $data = [];
    $i = 0;

    foreach ($posts as $post) {
        $data[$i]['date'] = get_field("event_date", $post->ID);
        $data[$i]['id'] = $post->ID;
        $data[$i]['title'] = $post->post_title;
        $data[$i]['category'] = $post->post_category;
        $data[$i]['content'] = $post->post_content;
        $data[$i]['slug'] = $post->post_name;
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($post->ID, 'large');
        $i++;
    }

    return $data;
}


add_action('rest_api_init', function () {

    register_rest_route('mt/v1', 'posts/(?P<category>[a-zA-Z0-9-]+)/(?P<npage>[a-zA-Z0-9-]+)/(?P<order>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'mt_posts',
    ));
});
