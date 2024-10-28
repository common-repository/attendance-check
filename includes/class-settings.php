<?php
class PAC_Settings
{
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  private $option_key = 'pac_setting';
  private $page_slug = 'pac-setting-admin';
  private $section_id = 'pac_setting_section_id';
  private $setting_group = 'pac_setting_group';
  private $form_name = 'pac_setting';

  /**
   * Start up
   */
  public function __construct()
  {
    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'page_init' ) );
  }

  /**
   * Add options page
   */
  public function add_plugin_page()
  {
    // This page will be under "Settings"
    add_options_page(
        'Settings Admin', // page title
        __('Attendance Check Settings', 'presscat-attendance-check'), // menu title
        'manage_options', // capability
        $this->page_slug, // menu slug
        array( $this, 'output_admin_page' ) // cb function to be called to output the content for this page
    );
  }

  /**
   * Options page callback
   */
  public function output_admin_page()
  {
    // Set class property
    $this->options = get_option( $this->option_key ); ?>
    <div class="wrap">
      <h1><?php _e('Attendance Check Settings', 'presscat-attendance-check') ?></h1>
      <form method="post" action="options.php"> <?php
        // This prints out all hidden setting fields
        settings_fields( $this->setting_group );
        do_settings_sections( $this->page_slug );
        submit_button(); ?>
      </form>
    </div> <?php
  }

  /**
   * Register and add settings
   */
  public function page_init()
  {        
    register_setting(
      $this->setting_group, // settings group name.
      $this->form_name, // option name to save
      array( $this, 'sanitize' ) // Sanitize
    );

    add_settings_section(
      $this->section_id, // ID
      __('Attendance Settings', 'presscat-attendance-check'), // Title
      array( $this, 'print_section_info' ), // Callback
      $this->page_slug // Page
    );  

    add_settings_field(
      'reset', // ID
      __('Reset', 'presscat-attendance-check'), // Title 
      array( $this, 'reset_callback' ), // Callback
      $this->page_slug, // Page
      $this->section_id // Section           
    );          
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sanitize( $input )
  {
    if ( isset($input['reset']) && $input['reset'] == 'on' ) {
      $this->delete_all_attendance_check_history();
      unset($input['reset']);
    }
    return $input;
  }

  /** 
   * Print the Section text
   */
  public function print_section_info()
  {
    // print 'Enter your settings below:';
  }

  /** 
   * Get the settings option array and print one of its values
   */
  public function reset_callback()
  {
    printf(
      '<input type="checkbox" id="reset" name="%s" />',
      "{$this->form_name}[reset]"
    );
  }
  public function delete_all_attendance_check_history() {
    $all_users = get_users();
    foreach( $all_users as $user ) {
      $user_id = $user->get('ID');
      delete_user_meta( $user_id, 'attendance_history' );
    }
    
  }
}

if( is_admin() )
  $pac_settings_page = new PAC_Settings();