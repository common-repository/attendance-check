<?php
class PAC_myCRED_Custom_Hook extends myCRED_Hook {
  function __construct( $hook_prefs, $type ) {
    parent::__construct(array(
      'id'  =>  'attendance_check',
      'defaults' => array(
        'one'   => array( 'creds' => 50, 'log' => '%plural% for attendance check only one day' ),
        'ten'   => array( 'creds' => 100, 'log' => '%plural% for attendance check for ten day' ),
        'month' => array( 'creds' => 500, 'log' => '%plural% for attendance check for one month' )
      )
    ), $hook_prefs, $type);
  }
  
  public function run() {
    add_action( 'pac_after_attendance_check', array( $this, 'attendance_check' ), 100, 2 );
  }

  public function attendance_check( $user_id, $event_type ) {
    if ( $this->core->exclude_user( $user_id ) ) return;

    $this->core->add_creds( 'attendance_check', $user_id, $this->prefs[$event_type]['creds'], $this->prefs[$event_type]['log'], 0, '' );
  }

  public function preferences() { $prefs = $this->prefs; ?>
    <!-- ONE -->
    <ol>
      <li>
        <label for="<?php echo $this->field_id( array('one', 'creds') )?>" class="subheader"><?php echo $this->core->plural(); ?></label>
        <input type="text" 
        name="<?php echo $this->field_name( array('one', 'creds') ); ?>" 
        id="<?php echo $this->field_id( array('one', 'creds') ); ?>" 
        value="<?php echo $this->core->format_number($prefs['one']['creds']); ?>" size="8" />
      </li>
      <li>
        <label class="subheader"><?php _e( 'Log template', 'mycred' ); ?></label>
        <input type="text"
        name="<?php echo $this->field_name( array('one', 'log') ); ?>"
        id="<?php echo $this->field_id( array('one', 'log') ); ?>"
        value="<?php echo esc_attr( $prefs['one']['log'] ); ?>" class="long" />
      </li>
    </ol>

    <!-- TEN -->
    <ol>
      <li>
        <label for="<?php echo $this->field_id( array('ten', 'creds') )?>" class="subheader"><?php echo $this->core->plural(); ?></label>
        <input type="text"
        name="<?php echo $this->field_name( array('ten', 'creds') ); ?>" 
        id="<?php echo $this->field_id( array('ten', 'creds') ); ?>" 
        value="<?php echo $this->core->format_number($prefs['ten']['creds']); ?>" size="8" />
      </li>
      <li>
        <label class="subheader"><?php _e( 'Log template', 'mycred' ); ?></label>
        <input type="text"
        name="<?php echo $this->field_name( array('ten', 'log') ); ?>"
        id="<?php echo $this->field_id( array('ten', 'log') ); ?>"
        value="<?php echo esc_attr( $prefs['ten']['log'] ); ?>" class="long" />
      </li>
    </ol>

    <!-- MONTH -->
    <ol>
      <li>
        <label for="<?php echo $this->field_id( array('month', 'creds') )?>" class="subheader"><?php echo $this->core->plural(); ?></label>
        <input type="text" 
        name="<?php echo $this->field_name( array('month', 'creds') ); ?>" 
        id="<?php echo $this->field_id( array('month', 'creds') ); ?>" 
        value="<?php echo $this->core->format_number($prefs['month']['creds']); ?>" size="8" />
      </li>
      <li>
        <label class="subheader"><?php _e( 'Log template', 'mycred' ); ?></label>
        <input type="text"
        name="<?php echo $this->field_name( array('month', 'log') ); ?>"
        id="<?php echo $this->field_id( array('month', 'log') ); ?>" 
        value="<?php echo esc_attr( $prefs['month']['log'] ); ?>" class="long" />
      </li>
    </ol> <?php
  }

  public function sanitise_preferences( $data ) {  
    $new_data = $data;
    $new_data['one']['creds'] = ( !empty( $data['one']['creds'] ) ) ? $data['one']['creds'] : $this->defaults['one']['creds'];
    $new_data['one']['log'] = ( !empty($data['one']['log']) ) ? sanitize_text_field( $data['one']['log'] ) : $this->defaults['one']['log'];

    $new_data['ten']['creds'] = ( !empty( $data['ten']['creds'] ) ) ? $data['ten']['creds'] : $this->defaults['ten']['creds'];
    $new_data['ten']['log'] = ( !empty($data['ten']['log']) ) ? sanitize_text_field( $data['ten']['log'] ) : $this->defaults['ten']['log'];

    $new_data['month']['creds'] = ( !empty( $data['month']['creds'] ) ) ? $data['month']['creds'] : $this->defaults['month']['creds'];
    $new_data['month']['log'] = ( !empty($data['month']['log']) ) ? sanitize_text_field( $data['month']['log'] ) : $this->defaults['month']['log'];

    return $new_data;
  }
}