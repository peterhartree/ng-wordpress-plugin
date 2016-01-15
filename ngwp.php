<?php
/*
Plugin Name: Angular WordPress
Plugin URI: https://github.com/peterhartree/ng-wordpress-plugin
Description: A companion plugin for ng-wordpress skeleton project. Adds API fields and custom routes.
Version: 0.1
Author: Peter Hartree
Author URI: https://web.peterhartree.co.uk
Text Domain: ngwp
*/

require_once('custom-fields.php');
require_once('custom-routes/archive.php');
require_once('custom-routes/options.php');
//require_once('custom-post-types/example.php');
require_once('wp-super-cache-patch.php');

add_filter( 'excerpt_more', 'modify_read_more_link' );
function modify_read_more_link() {
return '';
}

function child_theme_setup() {
  // override parent theme's 'more' text for excerpts
  remove_filter( 'excerpt_more', 'twentyfifteen_auto_excerpt_more' );
  remove_filter( 'get_the_excerpt', 'twentyfifteen_custom_excerpt_more' );
}
add_action( 'after_setup_theme', 'child_theme_setup' );