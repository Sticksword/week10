<?php
  function record_failed_login($username) {
    // The failure technically already happened, so
    // get the time ASAP.
    $sql_date = date("Y-m-d H:i:s");

    $fl_result = find_failed_login($username);
    $failed_login = db_fetch_assoc($fl_result);

    if(!$failed_login) {
      $failed_login = [
        'username' => $username,
        'count' => 1,
        'last_attempt' => $sql_date
      ];
      insert_failed_login($failed_login);
    } else {
      $failed_login['count'] = $failed_login['count'] + 1;
      $failed_login['last_attempt'] = $sql_date;
      update_failed_login($failed_login);
      if ($failed_login['count'] == 5) {
        echo 'Too many failed logins!';
      } elseif ($failed_login['count'] >= 10) {
        echo throttle_time($username);
      }
    }
    return true;
  }

  function reset_failed_login($username) {
    $sql_date = date("Y-m-d H:i:s");

    $fl_result = find_failed_login($username);
    $failed_login = db_fetch_assoc($fl_result);

    $failed_login['count'] = 0;
    $failed_login['last_attempt'] = $sql_date;
    update_failed_login($failed_login);

    return true;
  }

  function throttle_time($username) {
    $threshold = 10;
    $lockout = 60 * 10; // in seconds
    $fl_result = find_failed_login($username);
    $failed_login = db_fetch_assoc($fl_result);
    if(!isset($failed_login)) { return 0; }
    if($failed_login['count'] < $threshold) { return 0; }
    $last_attempt = strtotime($failed_login['last_attempt']);
    $since_last_attempt = time() - $last_attempt;
    $remaining_lockout = $lockout - $since_last_attempt;
    if($remaining_lockout < 0) {
      reset_failed_login($username);
      return 0;
    } else {
      return $remaining_lockout;
    }
  }
?>
