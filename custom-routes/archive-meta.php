<?php
/**
 * Get WordPress archive
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 *   year => months => post names
 */
function ngwp_get_archive_meta( $data ) {

    $archive_meta = array();
    $args = array();

    if(isset($data['post_type'])):
        $args['post_type'] = $data['post_type'];
    else:
        $args['post_type'] = 'post';
    endif;

    $count_posts = wp_count_posts($args['post_type']);
    $published_posts = $count_posts->publish;

    $archive_meta['published_posts'] = intval($published_posts);

    return $archive_meta;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'ngwp/v1', '/archive-meta', array(
        'methods' => 'GET',
        'callback' => 'ngwp_get_archive_meta',
    ) );
} );
