<?php
/**
 * Get WordPress site options
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 */
function ngwp_get_options( $data ) {
    $options = new stdClass();

    $front_page_id = get_option('page_on_front');
    $front_page = get_post($front_page_id);
    $front_page_post_name = $front_page->post_name;

    $options->wpurl = get_bloginfo('wpurl');
    $options->url = get_bloginfo('url');
    $options->name = get_bloginfo('name');
    $options->rss_url = get_bloginfo('rss_url');
    $options->front_page = new stdClass();
    $options->front_page->ID = $front_page_id;
    $options->front_page->page_name = $front_page_post_name;

    $options = apply_filters('ngwp_get_options', $options);

    return $options;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'ngwp/v1', '/options', array(
        'methods' => 'GET',
        'callback' => 'ngwp_get_options',
    ) );
} );