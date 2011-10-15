<?php

class Session {

  /**
   * Retrieves the current sesion object
   **/
  public static function load() {
    $infinite = False;

    if ($infinite) $lifetime = time() + Config::get('session-infinite-timeout');
    else $lifetime = 0;

    session_set_cookie_params($lifetime, '/', $_SERVER['HTTP_HOST'], Request::isSecure(), True);
    session_start();
  }

  /**
   * Retrieves a variable from the session
   **/
  public static function get($var) {
    return ARR($_SESSION, $var);
  }

  /**
   * Updates the session variable
   */
  public static function update($changes) {
    foreach ($changes as $k => $v) {
      $_SESSION[$k] = $v;
    }
  }
}

