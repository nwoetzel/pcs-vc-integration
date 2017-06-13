<?php
// based on https://github.com/easydigitaldownloads/EDD-Extension-Boilerplate
/**
 * Plugin Name: Post Content Shortcodes Visual Composer Integration
 * Plugin URI:  https://github.com/nwoetzel/pcs-vc-integration
 * Description: This plugin maps post-content-shortcodes shortcodes to WPBakery Visual Composer elements.
 * Version:     1.3.0
 * Author:      Nils Woetzel
 * Author URI:  https://github.com/nwoetzel
 * Text Domain: pcs-vc-integration
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
        require __DIR__ . '/vendor/autoload.php';
}

if( !class_exists( 'PCS_VC_Integration' ) ) {

/**
 * Main PCS_VC_Integration class
 *
 * @since 1.0.0
 */
class PCS_VC_Integration {

    /**
     * @since 1.1.0
     * @var   string Text domain used for translations
     */
    CONST TEXT_DOMAIN = 'pcs-vc-integration';

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
            self::$instance->load_textdomain();
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
        define( 'PCS_VC_INTERGATION_VER', '1.3.0' );
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
     * Internationalization
     *
     * @access      public
     * @since       1.1.0
     * @return      void
     */
    public function load_textdomain() {
        // Set filter for language directory
        $lang_dir = PCS_VC_INTEGRATION_DIR . '/languages/';
        $lang_dir = apply_filters( 'pcs_vc_integration_languages_directory', $lang_dir );
        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), self::TEXT_DOMAIN );
        $mofile = sprintf( '%1$s-%2$s.mo', self::TEXT_DOMAIN, $locale );
        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/' . self::TEXT_DOMAIN . '/' . $mofile;
        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/pcs-vc-integration/ folder
            load_textdomain( self::TEXT_DOMAIN, $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/pcs-vc-integration/languages/ folder
            load_textdomain( self::TEXT_DOMAIN, $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( self::TEXT_DOMAIN, false, 'pcs-vc-integration/languages' );
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
            'name' => __('Post Content', self::TEXT_DOMAIN),
            'base' => 'post-content',
            'category' => __('Content', self::TEXT_DOMAIN),
            'description' => __('Display the content of one post within another post.', self::TEXT_DOMAIN),
            'params' => array(
                self::idParam(),
                self::showTitleParam(),
                self::showExcerptParam(),
                self::excerptLengthParam(),
                self::shortcodesParam(),
                self::showImageParam(),
                self::imageWidthParam(),
                self::imageHeightParam(),
                self::linkImageParam(),
            ),
        ) );

        // post-list
        vc_map( array(
            'name' => __( 'Post List', self::TEXT_DOMAIN),
            'base' => 'post-list',
            'category' => __( 'Content', self::TEXT_DOMAIN),
            'description' => __( 'Display the content of a list of posts within another post.', self::TEXT_DOMAIN),
            'params' => array(
                self::showExcerptParam(),
                self::excerptLengthParam(),
                self::shortcodesParam(),
                self::showImageParam(),
                self::imageWidthParam(),
                self::imageHeightParam(),
                self::linkImageParam(),
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
            'heading' => __( 'Post', self::TEXT_DOMAIN),
            'description' => __( 'Select a post. They are grouped by post type', self::TEXT_DOMAIN),
            'type' => 'autocomplete',
            'settings' => array(
                'sortable' => true,
                'min_length' => 1,
                'display_inline' => true,
                'values' => self::posts(),
            ),
            'save_always' => true,
            'admin_label' => true,
            'group' => __( 'Data', self::TEXT_DOMAIN),
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
            'heading' => __( 'Display the post title?', self::TEXT_DOMAIN),
            'description' => __( 'Whether or not to show the post title at the top of the content. By default, the title is wrapped in h2 tags.', self::TEXT_DOMAIN),
            'value' => array( __( 'Yes', 'js_composer' ) => true ),
            'type' => 'checkbox',
            'admin_label' => true,
            'group' => __( 'Layout', self::TEXT_DOMAIN),
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
            'heading' => __( 'Display an excerpt of the post content?', self::TEXT_DOMAIN),
            'description' => __( 'Determines whether to default to showing the post excerpt instead of the post content (still falls back to post content if the excerpt is not set).', self::TEXT_DOMAIN),
            'value' => array( __( 'Yes', 'js_composer' ) => "true" ),
            'type' => 'checkbox',
            'admin_label' => true,
            'group' => __( 'Layout', self::TEXT_DOMAIN),
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
            'heading' => __( 'Excerpt length [words]', self::TEXT_DOMAIN),
            'description' => __( 'Limit the length of the content/excerpt shown. Specify the maximum number of words (a read more link will automatically be appended to any entries that exceed that limit).', self::TEXT_DOMAIN),
            'type' => 'textfield',
            'dependency' => array( 'element' => 'show_excerpt', 'not_empty' => true),
            'admin_label' => true,
            'group' => __( 'Layout', self::TEXT_DOMAIN),
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
            'heading' => __( 'Display the featured image with the post?', self::TEXT_DOMAIN),
            'description' => __( 'Determines whether or not to display the featured image (if so, this appears before the content).', self::TEXT_DOMAIN),
            'value' => array( __( 'Yes', 'js_composer' ) => true ),
            'type' => 'checkbox',
            'admin_label' => true,
            'group' => __( 'Featured Image', self::TEXT_DOMAIN),
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
            'heading' => __( 'Image width', self::TEXT_DOMAIN),
            'description' => __( 'The width, in pixels, to which the featured image should be sized.', self::TEXT_DOMAIN),
            'type' => 'textfield',
            'dependency' => array( 'element' => 'show_image',' value' => array( "1" ), 'not_empty' => true),
            'admin_label' => true,
            'group' => __( 'Featured Image', self::TEXT_DOMAIN),
        );
    }

    /**
     * This is a shortcode parameter to enable the link for the image.
     *
     * @access       protected
     * @since        1.3.0
     * @return       array describing a shortcode parameter
     */
    protected static function linkImageParam() {
        return array(
            'param_name' => 'link_image',
            'heading' => __( 'Link image', self::TEXT_DOMAIN),
            'description' => __( 'Set this to true/1 if you would like to wrap the featured image in a link to the post.', self::TEXT_DOMAIN),
            'value' => array( __( 'Yes', 'js_composer' ) => true ),
            'type' => 'checkbox',
            'dependency' => array( 'element' => 'show_image',' value' => array( "1" ), 'not_empty' => true),
            'admin_label' => true,
            'group' => __( 'Featured Image', self::TEXT_DOMAIN),
        );
    }

    /**
     * This is a shortcode parameter to enable parsing of included shortcodes in post or excerpt.
     *
     * @access       protected
     * @since        1.3.0
     * @return       array describing a shortcode parameter
     */
    protected static function shortcodesParam() {
        return array(
            'param_name' => 'shortcodes',
            'heading' => __( 'Shortcodes', self::TEXT_DOMAIN),
            'description' => __( 'By default, this plugin strips shortcodes out of the post content/excerpt. If you would like to attempt to process/render shortcodes in the content, set this flag to true.', self::TEXT_DOMAIN),
            'value' => array( __( 'Yes', 'js_composer' ) => true ),
            'type' => 'checkbox',
            'admin_label' => true,
            'group' => __( 'Layout', self::TEXT_DOMAIN),
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
            'heading' => __( 'Image height', self::TEXT_DOMAIN),
            'description' => __( 'The height, in pixels, to which the featured image should be sized.', self::TEXT_DOMAIN),
            'type' => 'textfield',
            'dependency' => array( 'element' => 'show_image',' value' => array( "1" ), 'not_empty' => true),
            'admin_label' => true,
            'group' => __( 'Featured Image', self::TEXT_DOMAIN),
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
function pcs_vc_integration_load() {
    return PCS_VC_Integration::instance();
}
add_action( 'plugins_loaded', 'pcs_vc_integration_load' );

/**
 * A nice function name to retrieve the instance that's created on plugins loaded
 *
 * @since 1.0.0
 * @return object PCS_VC_Integration
 */
function pcs_vc_integration() {
	return pcs_vc_integration_load();
}
