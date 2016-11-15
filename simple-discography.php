<?php
/*
Plugin Name: Simple Discography
Version: 1.3.1
Plugin URI: http://wordpress.org/plugins/simple-discography
Description: Simple Discography is a small plugin to very easy to use  that will allow you to manage the albums of your band.
Author: Sébastien Batteur
Author URI: http://www.batteur.be
*/

/*
Copyright (c) 2014, Marcus Sykes

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**
 * init Constant
 */
define('RELEASE', 'sidi-release');
define('CATALOG', 'sidi-catalog-number');
define('AMAZON', 'sidi-amazon');
define('COVER', 'sidi-cover');
define('DISCS', 'sidi-discs');
define('POST_TYPE', 'discography');

/**
 * General section
 */

require_once('classes/SiDi_Helper.php');

add_action( 'plugins_loaded', 'sidi_load_textdomain' );

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function sidi_load_textdomain() {
    load_plugin_textdomain( 'sidi', false, dirname( plugin_basename( __FILE__ ) ) . '/includes/langs/' );
}

$current_theme = wp_get_theme();
if($current_theme->get('name') === 'Genesis' || $current_theme->get('Template') === 'genesis'){
    define('GENESIS_ACTIVE', true);
    add_post_type_support( POST_TYPE, 'genesis-seo' );
    add_post_type_support( POST_TYPE, 'genesis-layouts');
    add_post_type_support( POST_TYPE, 'genesis-simple-sidebars');
} else{
    define('GENESIS_ACTIVE', false);
}

add_filter( 'excerpt_more', 'sidi_new_excerpt_more',100 );

function sidi_new_excerpt_more( $more ) {
    global $post;
    if($post->post_type === POST_TYPE)
        return ' <a title="'.esc_attr(__('Read More', 'sidi')).'" href="'. get_permalink( get_the_ID() ) . '">[...]</a>';
    else
        $more;
}

add_action( 'init', 'sidi_register_album_cpt' );
/**
 * Register an Album custom post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function sidi_register_album_cpt() {
    $labels = array(
        'name'                  => __( 'Albums', 'sidi' ),
        'singular_name'         => __( 'Album', 'sidi' ),
        'menu_name'             => __( 'Discography', 'sidi' ),
        'name_admin_bar'        => __( 'Album', 'sidi' ),
        //'archives'              => __( 'Album Archives', 'sidi' ),
        'all_items'             => __( 'All Albums', 'sidi' ),
        'add_new'               => __( 'Add New', 'sidi' ),
        'add_new_item'          => __( 'Add New Album', 'sidi' ),
        'new_item'              => __( 'New Album', 'sidi' ),
        'edit_item'             => __( 'Edit Album', 'sidi' ),
        'update_item'           => __( 'Update Album', 'sidi' ),
        'view_item'             => __( 'View Album', 'sidi' ),
        'search_items'          => __( 'Search Albums', 'sidi' ),
        //'parent_item_colon'     => __( 'Parent Album:', 'sidi' ),
        'not_found'             => __( 'No Albums found.', 'sidi' ),
        'not_found_in_trash'    => __( 'No Albums found in Trash.', 'sidi' ),
        //'featured_image'        => __( 'Featured Image', 'sidi' ),
        //'set_featured_image'    => __( 'Set featured image', 'sidi' ),
        //'remove_featured_image' => __( 'Remove featured image', 'sidi' ),
        //'use_featured_image'    => __( 'Use as featured image', 'sidi' ),
        //'insert_into_item'      => __( 'Insert into Album', 'sidi' ),
        //'uploaded_to_this_item' => __( 'Uploaded to this Album', 'sidi' ),
        'items_list'            => __( 'Album list', 'sidi' ),
        'items_list_navigation' => __( 'Album list navigation', 'sidi' ),
        'filter_items_list'     => __( 'Filter Album list', 'sidi' ),
    );
    $support = array( 'title', 'editor', 'author', 'thumbnail' );
    if(GENESIS_ACTIVE){
        $support[]='genesis-seo';
        $support[]='genesis-layouts';
        $support[]='genesis-simple-sidebars';
    }

    $args = array(
        'label'                 => __( 'Album', 'sidi' ),
        'description'           => __( 'Simple Discography Album', 'sidi' ),
        'labels'                => $labels,
        'public'                => true,
        'menu_icon'             => 'dashicons-playlist-audio',
        'publicly_queryable'    => true,
        'show_in_admin_bar'     => true,
        'show_ui'               => true,
        'show_in_nav_menus'     => true,
        'show_in_menu'          => true,
        'query_var'             => 'discography',
        'rewrite'               => array( 'slug' => 'discography' ),
        'capability_type'       => 'post',
        'has_archive'           => false,
        'hierarchical'          => false,
        'menu_position'         => 20,
        'supports'              => $support,
        'taxonomies'            => array( 'category', 'post_tag' ),
        'can_export'            => true,
        'exclude_from_search'   => false,
        'register_meta_box_cb'  => 'add_album_metaboxes'
    );

    register_post_type( POST_TYPE, $args );
}

require_once('SiDi_Discography_Widget.php');

require_once('SiDi_content.php');

if ( ! is_admin() ) {
    require_once('SiDi_Shortcodes.php');
} else {
    require_once('SiDi_Posts.php');
}
