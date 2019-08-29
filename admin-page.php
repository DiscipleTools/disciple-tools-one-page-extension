<?php
/**
 * Plugin Name: Disciple Tools Extension - Admin Page
 * Plugin URI: https://github.com/DiscipleTools/disciple-tools-starter-plugin
 * Description: DT Grid Install adds the full locations database.
 * of the Disciple Tools system.
 * Version:  0.1.0
 * Author URI: https://github.com/DiscipleTools
 * GitHub Plugin URI: https://github.com/DiscipleTools/disciple-tools-starter-plugin
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.3
 *
 * @package Disciple_Tools
 * @link    https://github.com/DiscipleTools
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Instructions for customizing
 * Refactor names to unique project name
 * Refactor:
 *      Admin Page
 *      Admin_Page
 *      admin_page
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! function_exists( 'admin_page' ) ) {
    function admin_page() {
        $required_dt_theme_version = '0.22.0';
        $wp_theme = wp_get_theme();
        $version = $wp_theme->version;
        /*
         * Check if the Disciple.Tools theme is loaded and is the latest required version
         */
        $is_theme_dt = strpos( $wp_theme->get_template(), "disciple-tools-theme" ) !== false || $wp_theme->name === "Disciple Tools";
        if ( !$is_theme_dt || version_compare( $version, $required_dt_theme_version, "<" ) ) {
            add_action( 'admin_notices', 'dt_starter_plugin_hook_admin_notice' );
            add_action( 'wp_ajax_dismissed_notice_handler', 'dt_hook_ajax_notice_handler' );
            return new WP_Error( 'current_theme_not_dt', 'Disciple Tools Theme not active or not latest version.' );
        }
        /**
         * Load useful function from the theme
         */
        if ( !defined( 'DT_FUNCTIONS_READY' ) ){
            require_once get_template_directory() . '/dt-core/global-functions.php';
        }
        /*
         * Don't load the plugin on every rest request. Only those with the 'sample' namespace
         */
        $is_rest = dt_is_rest();
        if ( !$is_rest || strpos( dt_get_url_path(), 'sample' ) != false ){
            return Admin_Page::instance();
        }
        return false;
    }
}
add_action( 'plugins_loaded', 'admin_page' );


/**
 * Class Admin_Page
 */
class Admin_Page {

    public $token = 'admin_page';
    public $title = 'Admin Page';
    public $permissions = 'manage_dt';

    /**  Singleton */
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {
        add_action( "admin_menu", array( $this, "register_menu" ) );
    } // End __construct()


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_menu_page( 'Extensions (DT)', 'Extensions (DT)', $this->permissions, 'dt_extensions', [ $this, 'extensions_menu' ], 'dashicons-admin-generic', 59 );
        add_submenu_page( 'dt_extensions', $this->title, $this->title, $this->permissions, $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu() {}

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content() {

        if ( !current_user_can( $this->permissions ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        ?>
        <div class="wrap">
            <h2><?php echo esc_html( $this->title ) ?></h2>
            <div class="wrap">
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <!-- Main Column -->

                            <?php $this->main_column(); ?>

                            <!-- End Main Column -->
                        </div><!-- end post-body-content -->
                        <div id="postbox-container-1" class="postbox-container">
                            <!-- Right Column -->

                            <?php $this->right_column(); ?>

                            <!-- End Right Column -->
                        </div><!-- postbox-container 1 -->
                        <div id="postbox-container-2" class="postbox-container">
                        </div><!-- postbox-container 2 -->
                    </div><!-- post-body meta box container -->
                </div><!--poststuff end -->
            </div><!-- wrap end -->
        </div><!-- End wrap -->

        <?php
    }

    public function main_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <th>Header</th>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }

    public function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <th>Information</th>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }


}