<?php
/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
add_action( 'rest_api_init', 'ngwp_register_custom_fields' );

function ngwp_register_custom_fields() {
    register_rest_field( array('post', 'page'),
        'ngwp',
        array(
            'get_callback'    => 'ngwp_get_custom_fields',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_custom_fields( $object, $field_name, $request ) {
    $custom_fields = array();
    $custom_fields['url'] = ngwp_get_url( $object );
    $custom_fields['excerpt'] = ngwp_get_excerpt( $object, $field_name, $request );
    $custom_fields['meta_title'] = ngwp_get_meta_title( $object, $field_name, $request );
    $custom_fields['meta_desc'] = ngwp_get_meta_desc( $object, $field_name, $request );
    $custom_fields['cannonical_path'] = ngwp_get_cannonical_path( $object, $field_name, $request );
    $custom_fields['robots'] = ngwp_get_robots( $object, $field_name, $request );
    $custom_fields['social_image'] = ngwp_get_social_image( $object, $field_name, $request );
    $custom_fields['author'] = ngwp_get_author( $object, $field_name, $request );
    $custom_fields['date_published'] = ngwp_get_published_date( $object );
    $custom_fields['date_modified'] = ngwp_get_modified_date( $object );
    $custom_fields['featured_image'] = ngwp_get_featured_image( $object );

    $previous = false;
    $custom_fields['next_post'] = ngwp_get_adjacent_post( $object['id'], $previous );

    $previous = true;
    $custom_fields['previous_post'] = ngwp_get_adjacent_post( $object['id'], $previous );


    return $custom_fields;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_url( $object ) {
    $type = $object[ 'type' ];
    $slug = $object[ 'slug' ];
    $ng_press_url = '/' . $type . '/' . $slug . '/';
    return $ng_press_url;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_excerpt( $object, $field_name, $request ) {
    $excerpt = $object[ 'excerpt' ][ 'rendered' ];
    $elipses_pos = strpos($excerpt, '&hellip;');
    $ng_press_excerpt = substr($excerpt, 0, $elipses_pos) . '&hellip;';
    return $ng_press_excerpt;
}


/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_meta_title( $object, $field_name, $request ) {
    $meta_title = get_post_meta($object[ 'id' ], '_yoast_wpseo_title', true);

    if(empty($meta_title)):
        $meta_title = $object['title']['rendered'];
    endif;

    return $meta_title;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_meta_desc( $object, $field_name, $request ) {
    $meta_desc = get_post_meta($object[ 'id' ], '_yoast_wpseo_metadesc', true);
    return $meta_desc;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_cannonical_path( $object, $field_name, $request ) {
    return ngwp_get_url( $object );
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_robots( $object, $field_name, $request ) {
    // @TODO
    $robots = 'index,follow';
    return $robots;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_social_image( $object, $field_name, $request ) {
    $thumb_id = get_post_thumbnail_id();
    $thumb_url_array = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);
    $thumb_url = $thumb_url_array[0];
    return $thumb_url;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_author( $object, $field_name, $request ) {
    $author = array();
    $author_id = $object['author'];
    $author['ID'] = $author_id;
    $author['display_name'] = get_the_author_meta('display_name', $author_id);
    return $author;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_adjacent_post( $post_id, $previous ) {
    // Get a global post reference since get_adjacent_post() references it
    global $post;

    // Store the existing post object for later so we don't lose it
    $oldGlobal = $post;

    // Get the post object for the specified post and place it in the global variable
    $post = get_post( $post_id );

    // Get the post object for the previous post
    if($previous) {
        $adjacent_post = get_previous_post();
    }
    else {
        $adjacent_post = get_next_post();
    }

    // Reset our global object
    $post = $oldGlobal;

    if ( '' == $adjacent_post )
        return 0;

    $adjacent_post_data = array();
    $adjacent_post_data['ID'] = $adjacent_post->ID;
    $adjacent_post_data['post_title'] = $adjacent_post->post_title;
    $adjacent_post_data['post_name'] = $adjacent_post->post_name;
    $adjacent_post_data['post_type'] = $adjacent_post->post_type;

    $object = array();
    $object['type'] = $adjacent_post_data['post_type'];
    $object['slug'] = $adjacent_post_data['post_name'];
    $adjacent_post_data['url'] = ngwp_get_url($object);

    return $adjacent_post_data;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_published_date( $object ) {
    $date = get_the_date(null, $object['id']);
    return $date;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_modified_date( $object ) {
    $date = get_the_modified_date(null, $object['id']);
    return $date;
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function ngwp_get_featured_image( $object ) {
    $featured_image = array();
    $featured_image_id = get_post_thumbnail_id( $object['id'] );
    $featured_image_full_size_array = wp_get_attachment_image_src($featured_image_id, 'full', true);
    $featured_image_large_size_array = wp_get_attachment_image_src($featured_image_id, 'large', true);
    $featured_image_medium_size_array = wp_get_attachment_image_src($featured_image_id, 'medium', true);
    $featured_image_thumbnail_size_array = wp_get_attachment_image_src($featured_image_id, 'thumbnail', true);
    $featured_image['srcs'] = array();
    $featured_image['srcs']['full'] = $featured_image_full_size_array[0];
    $featured_image['srcs']['large'] = $featured_image_large_size_array[0];
    $featured_image['srcs']['medium'] = $featured_image_medium_size_array[0];
    $featured_image['srcs']['thumbnail'] = $featured_image_thumbnail_size_array[0];

    $featured_image['is_default'] = false;

    if(!has_post_thumbnail($object['id'])):
        // This was the default featured image
        $featured_image['is_default'] = true;
    endif;

    return $featured_image;
}