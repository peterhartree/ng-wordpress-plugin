<?php
/**
* Add REST API support to an already registered taxonomy.
*/
add_action( 'init', 'my_custom_taxonomy_rest_support', 25 );
function my_custom_taxonomy_rest_support() {
  global $wp_taxonomies;

  //be sure to set this to the name of your taxonomy!
  $taxonomy_name = 'planet_class';

  if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
      $wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
      $wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
      $wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
  }
}
