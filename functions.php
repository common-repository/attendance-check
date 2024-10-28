<?php
function pac_get_user_attendance_history( $user_id, $json = false ) {
  $history = get_user_meta( $user_id, 'attendance_history', true );
  if ( $json ) {
    return $history;
  }
  return json_decode($history, true);
}

function pac_update_user_attendance_history( $user_id, $history ) {
  $history_json = json_encode( $history );
  update_user_meta( $user_id, 'attendance_history', $history_json );
}

function pac_attendance_check($user_id) {
  $result = array(
    'code' => 200,
    'message' => __('Successfully checked attendance', 'presscat-attendance-check')
  );
  $history = pac_get_user_attendance_history($user_id);
  $current_date = pac_get_simple_current_date();
  $year = $current_date['year'];
  $month = $current_date['month'];
  $day = $current_date['day'];
  if ( $history ) {
    if ( isset( $history[$year] ) ) {
      if ( isset( $history[$year][$month] ) ) {
        if ( ! isset( $history[$year][$month][$day] ) ) {
          $history[$year][$month][$day] = true;
        }
        // 이러면 안되는데 ? 누군가가 POST메세지를 보낸겨
        else {
          $result['code'] = 401;
          $result['message'] = __('Sorry, already checked attendance', 'presscat-attendance-check');
        }
      } else {
        $history[$year][$month] = array( $day => true );
      }
    } else {
      $history[$year] = array($month => array( $day => true ));
    }
  } else {
    $history = array(
      $year => array(
        $month => array(
          $day => true
        )
      )
    );
  }
  pac_update_user_attendance_history( $user_id, $history );

  return $result;
}

function pac_get_options() {}

function pac_mycred_attendance_hook_points() {
  $points = array(
    'default' => 50,
    'ten' => 100,
    'month' => 500
  );
  $hooks = mycred_get_option( 'mycred_pref_hooks', false );
  if ( isset( $hooks['active']['attendance_check'] ) ) {
    if ( isset( $hooks['hook_prefs']['attendance_check'] ) ) {
      $default_point = $hooks['hook_prefs']['attendance_check']['creds'];
    }
    $attendance_check_default_point = $hooks['hook_prefs']['creds'];
  }
}

function pac_get_event_type( $user_id ) {
  $event_type = 'one';
  $history = pac_get_user_attendance_history( $user_id ); // update하고 나서니까, 오늘꺼는 업데이트 되어있겠지
  $current_date = pac_get_simple_current_date();
  $year = $current_date['year'];
  $month = $current_date['month'];
  $day = $current_date['day'];
  $last_day = date("t", mktime(0, 0, 0, $month, 1, $year));
  $count = 0;
  $is_consecutive = false;
  $checked_history_count = count( $history[$year][$month] ); // 이번달만!
  
  // 이미 체크된 것들의 개수와 마지막 날이 같다면?
  if ( $checked_history_count == $last_day ) {
    $event_type = 'month';
  }
  // 최소한 오늘 날짜는 10일 이상이어야 하고, 체크한 개수도 10개는 넘어야지!
  else if ( ($day >= 10) && ($checked_history_count >= 10) ) {
    for( $i = 1; $i <= $day; $i++ ) {
      if ( isset($history[$year][$month][$i]) ) {
        $count += 1;
      } else {
        // 이미,,, 앞에서 count가 안끊어지고 10번 됬다면 stop!
        if ( $count == 10 ) {
          return $event_type; // 'one';
        }
        $count = 0; // 연속적이지 않으면, 리셋시켜버린다
      }
    }
    if ( $count == 10 ) { // 11개, 12개도 안되!
      $event_type = 'ten';
    }
  }

  return $event_type;
}

function pac_get_simple_current_date() {
  //date_default_timezone_set('Asia/Seoul');
  $year = date('Y');
  $month = date('n');
  $day = date('j');
  if ( PAC_TEST_MODE ) {
    $year -= PAC_YEAR_MINUS;
    $month -= PAC_MONTH_MINUS;
    $day -= PAC_DAY_MINUS;
  }
  return array( 'year' => $year, 'month' => $month, 'day' => $day );
}
?>
