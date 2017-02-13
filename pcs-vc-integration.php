<?php
// based on https://github.com/easydigitaldownloads/EDD-Extension-Boilerplate
/**
 * Plugin Name: Post Content Shortcodes Visual Composer Integration
 * Plugin URI:  https://github.com/nwoetzel/pcs-vc-integration
 * Description: This plugin maps post-content-shortcodes shortcodes to WPBakery Visual Composer elements.
 * Version:     1.0.1
 * Author:      Nils Woetzel
 * Author URI:  https://github.com/nwoetzel
 * Text Domain: pcs-vc-integration
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'PCS_VC_Integration' ) ) {

/**
 * Main PCS_VC_Integration class
 *
 * @since 1.0.0
 */
class PCS_VC_Integration {

    /**
     * @var PCS_VC_Integration $instance The one true PCS_VC_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      object self::$instance The one true PCS_VC_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new PCS_VC_Integration();
            self::$instance->setup_constants();
//            self::$instance->load_textdomain();
            self::$instance->hooks();
        }
        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function setup_constants() {
        // Plugin version
        define( 'PCS_VC_INTERGATION_VER', '1.0.1' );
        // Plugin path
        define( 'PCS_VC_INTERGATION_DIR', plugin_dir_path( __FILE__ ) );
        // Plugin URL
        define( 'PCS_VC_INTERGATION_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Run action and filter hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {
        // map shortcodes
        if( function_exists( 'vc_map' ) ) { 
            add_action( 'vc_before_init', array( $this, 'vcMap' ) );
        }
    }

    /**
     * map post-content-shortcodes shortcodes to visual composer elements
     * https://wordpress.org/plugins/post-content-shortcodes/
     *
     * @access      public since it is registered as an action
     * @since       1.0.0
     * @return      void
     */
    public function vcMap() {
        // post-content
        vc_map( array(
            'name' => 'Post Content',
            'base' => 'post-content',
            'category' => 'Content',
            'description' => 'Display the content of one post within another post.',
            'params' => array(
                self::idParam(),
                self::showTitleParam(),
                self::showExcerptParam(),
                self::excerptLengthParam(),
                self::showImageParam(),
                self::imageWidthParam(),
                self::imageHeightParam(),
            ),
        ) );

        // post-list
        vc_map( array(
            'name' => 'Post List',
            'base' => 'post-list',
            'category' => 'Content',
            'description' => 'Display the content of one post within another post.',
            'params' => array(
                self::showExcerptParam(),
                self::excerptLengthParam(),
                self::showImageParam(),
                self::imageWidthParam(),
                self::imageHeightParam(),
            ),
        ) );
    }

    /**
     * This is a shortcode parameter to select a post by its id.
     *
     * @access       protected
     * @since        1.0.0
     * @return       array describing a shortcode parameter
     */
    protected static function idParam() {
        return array(
            'param_name' => 'id',
            'heading' => 'Post',
            'description' => 'Select a post. They are grouped by post type',
            'type' => 'autocomplete',
            'settings' => array(
                'sortable' => true,
                'min_length' => 1,
                'display_inline' => true,
                'values' => self::posts(),
            ),
            'save_always' => true,
            'admin_label' => true,
            'group' => 'Data',
        );
    }

    /**
     * This is a shortcode parameter to enable showing the post title.
     *
     * @access       protected
     * @since        1.0.0
     * @return       array describing a shortcode parameter
     */
    protected static function showTitleParam() {
        return array(
            'param_name' => 'show_title',
            'heading' => 'Display the post title?',
            'description' => 'Whether or not to show the post title at the top of the content. By default, the title is wrapped in h2 tags.',
            'value' => array( __( 'Yes', 'js_composer' ) => true ),
            'type' => 'checkbox',
            'admin_label' => true,
            'group' => 'Layout',
        );
    }

    /**
     * This is a shortcode parameter to select showing the excerpt (instead of the full post content).
     *
     * @access       protected
     * @since        1.0.0
     * @return       array describing a shortcode parameter
     */
    protected static function showExcerptParam() {
        return array(
            'param_name' => 'show_excerpt',
            'heading' => 'Display an excerpt of the post content?',
            'description' => 'Determines whether to default to showing the post excerpt instead of the post content (still falls back to post content if the excerpt is not set).',
            'value' => array( __( 'Yes', 'js_composer' ) => "true" ),
            'type' => 'checkbox',
            'admin_label' => true,
            'group' => 'Layout',
        );
    }

    /**
     * This is a shortcode parameter to define the length of the excerpt if shown.
     * @see showExcerptParam
     *
     * @access       protected
     * @since        1.0.0
     * @return       array describing a shortcode parameter
     */
    protected static function excerptLengthParam() {
        return array(
            'param_name' => 'excerpt_length',
            'heading' => 'Excerpt length [words]',
            'description' => 'Limit the length of the content/excerpt shown. Specify the maximum number of words (a read more link will automatically be appended to any entries that exceed that limit).',
            'type' => 'textfield',
            'dependency' => array( 'element' => 'show_excerpt', 'not_empty' => true),
            'admin_label' => true,
            'group' => 'Layout',
        );
    }

    /**
     * This is a shortcode parameter to enable showing the image.
     *
     * @access       protected
     * @since        1.0.0
     * @return       array describing a shortcode parameter
     */
    protected static function showImageParam() {
        return array(
            'param_name' => 'show_image',
            'heading' => 'Display the featured image with the post?',
            'description' => 'Determines whether or not to display the featured image (if so, this appears before the content).',
            'value' => array( __( 'Yes', 'js_composer' ) => true ),
            'type' => 'checkbox',
            'admin_label' => true,
            'group' => 'Featured Image',
        );
    }

    /** 
     * This is a shortcode parameter to define the width of the image shown.
     *
     * @access       protected
     * @since        1.0.0
     * @return       array describing a shortcode parameter
     */
    protected static function imageWidthParam() {
        return array(
            'param_name' => 'image_width',
            'heading' => 'Image width',
            'description' => 'The width, in pixels, to which the featured image should be sized.',
            'type' => 'textfield',
            'dependency' => array( 'element' => 'show_image',' value' => array( "1" ), 'not_empty' => true),
            'admin_label' => true,
            'group' => 'Featured Image',
        );
    }

    /** 
     * This is a shortcode parameter to define the height of the image shown.
     *      
     * @access       protected
     * @since        1.0.0
     * @return       array describing a shortcode parameter
     */     
    protected static function imageHeightParam() {        
        return array(
            'param_name' => 'image_height',
            'heading' => 'Image height',
            'description' => 'The height, in pixels, to which the featured image should be sized.',
            'type' => 'textfield',
            'dependency' => array( 'element' => 'show_image',' value' => array( "1" ), 'not_empty' => true),
            'admin_label' => true,
            'group' => 'Featured Image',
        );
    }

    /******************
     * helper functions
     ******************/

    /**
     * This collects all posts of all post types.
     * Helper for the idParam().
     *
     * @access       protected
     * @since        1.0.0
     * @return       array posts array with label, value and group each
     */
    protected static function posts() {
        $posts_array = get_posts(array(
            'post_type' => 'any',
            'numberposts' => -1,
            'orderby' => 'post_title',
            'order' => 'ASC',
            'fields' => array('ID','post_title','post_type')
        ));

        $posts = array();
        foreach($posts_array as $post) {
            $posts[] = array( 'label' => $post->post_title, 'value' => $post->ID, 'group' => $post->post_type);
        }

        return $posts;
    }

}

} // End if class_exists check

/**
 * The main function responsible for returning the one true PCS_VC_Integration
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \PCS_VC_Integration The one true PCS_VC_Integration
 */
function PCS_VC_Integration_load() {
    return PCS_VC_Integration::instance();
}
add_action( 'plugins_loaded', 'PCS_VC_Integration_load' );

/**
 * A nice function name to retrieve the instance that's created on plugins loaded
 *
 * @since 2.2.3
 * @return object PCS_VC_Integration
 */
function pcs_vc_integration() {
	return pcs_vc_integration_load();
}
