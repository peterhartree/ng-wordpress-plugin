<?php
/**
 * Tell WP Super Cache to cache API endpoints
 *
 * Props http://wordpress.stackexchange.com/a/213331
 *
 * Hopefully WP Super Cache will support API requests by default soon:
 * https://github.com/Automattic/wp-super-cache/pull/22
 *
 * @param string $eof_pattern
 *
 * @return string
 */
function wcorg_json_cache_requests( $eof_pattern ) {
    global $wp_super_cache_comments;

    global $known_headers;
    $known_headers[] = "Access-Control-Allow-Headers";
    $known_headers[] = "Access-Control-Allow-Origin";
    $known_headers[] = "Access-Control-Allow-Methods";


    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {

        // Accept a JSON-formatted string as an end-of-file marker, so that the page will be cached
        $json_object_pattern     = '^[{].*[}]$';
        $json_collection_pattern = '^[\[].*[\]]$';

        $eof_pattern = str_replace(
            '<\?xml',
            sprintf( '<\?xml|%s|%s', $json_object_pattern, $json_collection_pattern ),
            $eof_pattern
        );

        // Don't append HTML comments to the JSON output, because that would invalidate it
        $wp_super_cache_comments = false;
    }

    return $eof_pattern;
}
add_filter( 'wp_cache_eof_tags', 'wcorg_json_cache_requests' );