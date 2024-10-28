<?php
function pac_attendance_check_ajax_callback() {
  $result = array(
    'code' => 200,
    'message' => __('Successfully checked attendance', 'presscat-attendance-check')
  );

  if ( isset( $_POST['check_today'] ) ) {
    if ( is_user_logged_in() ) {
      $user_id = get_current_user_id();
      $result = pac_attendance_check( $user_id );
    } else {
      $result['code'] = 401;
      $result['message'] = __( 'Please login first', 'presscat-attendance-check' );
    }
  } else {
    $result['code'] = 500;
    $result['message'] = __( 'Unknown error occurred', 'presscat-attendance-check' );
  }

  if ( isset($user_id) && $result['code'] == 200 ) {
    $event_type = pac_get_event_type( $user_id );
    do_action( 'pac_after_attendance_check', $user_id, $event_type );
  }

  wp_send_json( $result );
}
add_action( 'wp_ajax_attendance_check', 'pac_attendance_check_ajax_callback' );
add_action( 'wp_ajax_nopriv_attendance_check', 'pac_attendance_check_ajax_callback' );


/**
 * Register custom hook in myCRED
 *
 * @param [type] $installed
 * @return void
 */
function pac_mycred_setup_hook( $installed )
{
	$installed['attendance_check'] = array(
		'title'       => __( 'Attendance Check', 'presscat-attendance-check' ),
		'description' => __( 'Set the point of attendance check please', 'presscat-attendance-check' ),
		'callback'    => array( 'PAC_myCRED_Custom_Hook' )
	);
	return $installed;
}
add_filter( 'mycred_setup_hooks', 'pac_mycred_setup_hook' );
?>