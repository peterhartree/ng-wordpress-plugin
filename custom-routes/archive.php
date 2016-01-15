<?php
/**
 * Get WordPress archive
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest,  * or null if none.
 *   year => months => post names
 */
function ngwp_get_archive( $data ) {
    global $wpdb;
    $archive = array();
    $args = array();
    $args['numberposts'] = -1;

    if(isset($data['post_type'])):
        $args['post_type'] = $data['post_type'];
    else:
        $args['post_type'] = 'post';
    endif;

    // Get all published posts (for use later)
    $posts = get_posts( $args );

    // Get years that have posts
    $years_query = "SELECT YEAR(post_date) AS year FROM ". $wpdb->prefix ."posts WHERE post_type = '" . $args['post_type'] . "' AND post_status = 'publish' GROUP BY year DESC";

    $years = $wpdb->get_results( $years_query );

    // For each year, get months with posts
    foreach ( $years as $year ):
        $months_query = "SELECT MONTH(post_date) AS month FROM ". $wpdb->prefix ."posts WHERE post_type = '" . $args['post_type'] . "' AND post_status = 'publish' GROUP BY month DESC";

        $months = $wpdb->get_results( $months_query );

        // For each month with posts, get post titles and permalink
        foreach ( $months as $key => $month ):
            $month->posts = array();

            $dateObj   = DateTime::createFromFormat('!m', $month->month);
            $month->name = $dateObj->format('F');

            foreach ( $posts as $post ):

                $post_timestamp = strtotime($post->post_date);
                $post_month = date( 'n' , $post_timestamp);
                $post_year = date( 'Y' , $post_timestamp);

                if($post_month === $month->month && $post_year == $year->year):
                    $post_data = array();
                    $post_data['post_title'] = $post->post_title;

                    $post_args = array();
                    $post_args['id'] = $post->ID;
                    $post_args['type'] = $post->post_type;
                    $post_args['slug'] = $post->post_name;
                    $post_data['link'] = ngwp_get_url($post_args);

                    $post_data['date_published'] = ngwp_get_published_date($post_args);

                    $month->posts[] = $post_data;
                endif;
            endforeach;

            if(count($month->posts) === 0):
                unset($months[$key]);
            endif;

        endforeach;

        $year->months = $months;
    endforeach;

    $archive = $years;
    return $archive;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'ngwp/v1', '/archive', array(
        'methods' => 'GET',
        'callback' => 'ngwp_get_archive',
    ) );
} );
