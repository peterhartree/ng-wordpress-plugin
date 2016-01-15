<?php
/**
 * Get WordPress archive
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 */
function ngwp_get_archive( $data ) {

    $args = array();
    $args['echo'] = 0;

    if(isset($data['type'])):
        $args['type'] = $data['type'];
    endif;

    if(isset($data['post_type'])):
        $args['post_type'] = $data['post_type'];
    endif;

    $url = get_bloginfo('url');

    $archives = wp_get_archives( $args );

    $archive = str_replace($url, '', $archives);

    return $archives;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'ngwp/v1', '/archive', array(
        'methods' => 'GET',
        'callback' => 'ngwp_get_archive',
    ) );
} );
