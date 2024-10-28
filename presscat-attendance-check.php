<?php
/**
 * Attendance check plugin made by Presscat
 *
 * Simple attendance check plugin
 *
 * @author    Presscat <info@presscat.co.kr>
 * @copyright 2018 Presscat
 * @license   GPLv2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://presscat.co.kr
 * @package   Attendance Check
 *
 * @wordpress-plugin
 * Plugin Name:       Attendance Check
 * Description:       Simple attendance check plugin
 * Version:           1.0.0
 * Author:            Presscat <info@presscat.co.kr>
 * Author URI:        https://presscat.co.kr
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       presscat-attendance-check
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Attendance_Check {

  private $ver = '1.0.0';
  static private $instance = null;
  static public $test_mode = false;
  static public $year_minus = 0;
  static public $month_minus = -2;
  static public $day_minus = 0;

  function __construct() {
    // check myCRED activation first
    if ( ! is_admin() ) {
      include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    // check whether mycred plugin is active on initiate this class
    $is_mycred_active = is_plugin_active('mycred/mycred.php');
    if ( $is_mycred_active ) {
      $this->setup_constants();
      $this->load_textdomain();
      $this->includes();
      $this->setup_DB();
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
      add_shortcode( 'attendance_check', array($this, 'shortcode') );
    } else {
      deactivate_plugins( 'attendance-check/presscat-attendance-check.php' );
      wp_die( "<h3>". __( 'myCRED plugin is not activated. Please install and activate it first to use this plugin.', 'presscat-attendance-check' ) ."</h3><a href='plugins.php'>Return to Wordpress plugins page</a>" );
    }
  }
  public static function instance() {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self();
    }

    return self::$instance;
  }
  private function setup_constants() {
    if ( ! defined( 'PAC_PLUGIN_URL' ) ) { // PAC means presscat attendance check
      define( 'PAC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }
    if ( ! defined( 'PAC_PLUGIN_DIR' ) ) { // PAC means presscat attendance check
      $plugin_dir = wp_normalize_path( plugin_dir_path( __FILE__ ) );
      define( 'PAC_PLUGIN_DIR', $plugin_dir );
    }
    define( 'PAC_TEST_MODE', self::$test_mode );
    define( 'PAC_YEAR_MINUS', self::$year_minus );
    define( 'PAC_MONTH_MINUS', self::$month_minus );
    define( 'PAC_DAY_MINUS', self::$day_minus );
  }
  private function setup_DB() {
  }
  function includes() {
    include_once PAC_PLUGIN_DIR . 'functions.php';
    include_once PAC_PLUGIN_DIR . 'includes/class-mycred-hook.php';
    include_once PAC_PLUGIN_DIR . 'includes/hooks.php';
    include_once PAC_PLUGIN_DIR . 'includes/class-settings.php';
  }
  function enqueue_scripts() {
    // $dynamic_ver = rand(1, 100); // for cache problem
    $dynamic_ver = '1.0.0';
    
    wp_register_script( 'moment.js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.js', array('jquery'), false, false );
    wp_register_style( 'attendance-check-font-awesome', 'https://use.fontawesome.com/releases/v5.0.13/css/all.css', array(), false, 'all' );
    wp_enqueue_style( 'attendance-check', PAC_PLUGIN_URL . 'style.css', array('attendance-check-font-awesome'), $dynamic_ver, 'all' );
    wp_enqueue_script( 'attendance-check', PAC_PLUGIN_URL . 'main-min.js', array('jquery', 'moment.js'), $dynamic_ver, true );

    // register ajax url
    wp_localize_script( 'attendance-check', 'pac_ajax_obj', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'seal_src' => PAC_PLUGIN_URL . 'assets/images/seal.png',
        'no_seal_src' => PAC_PLUGIN_URL . 'assets/images/no_seal.png',
        'test_mode' => PAC_TEST_MODE,
        'year_minus' => PAC_YEAR_MINUS,
        'month_minus' => PAC_MONTH_MINUS,
        'day_minus' => PAC_DAY_MINUS
      ) 
    );
  }
  function load_textdomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), 'presscat-attendance-check' );
    $loaded = load_textdomain( 'presscat-attendance-check', trailingslashit( WP_LANG_DIR ) . "presscat-attendance-check/presscat-attendance-check-{$locale}.mo" );

    if ( $loaded ) {
      return $loaded;
    } else {
      load_plugin_textdomain( 'presscat-attendance-check', false, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
  }
  function shortcode( $atts ) {
    if ( is_user_logged_in() ) {
      $user_id = get_current_user_id();
      $history = pac_get_user_attendance_history( $user_id, true );
      if ( $history ) {
        $atts = array(
          'history' => $history
        );
      }
    }

    include_once PAC_PLUGIN_DIR . 'template.php';
  }
}
add_action( 'plugins_loaded', ['Attendance_Check', 'instance'], 1000 );

/**
 * Check whether mycred plugin is active on active this plugin
 *
 * @return void
 */
function pac_activate() {
  $is_mycred_active = is_plugin_active('mycred/mycred.php');
  if ( ! $is_mycred_active ) {
    // __( 'myCRED Plugin is not activated. Please activate it first to use this plugin.',  );
    deactivate_plugins( 'attendance-check/presscat-attendance-check.php' );
    wp_die( "<h3>". __( 'myCRED plugin is not activated. Please install and activate it first to use this plugin.', 'presscat-attendance-check' ) ."</h3><a href='plugins.php'>Return to Wordpress plugins page</a>" );
  }
}
register_activation_hook( __FILE__, 'pac_activate' );
