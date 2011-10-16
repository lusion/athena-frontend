<?php

class Session {

  /**
   * Retrieves the current sesion object
   **/
  public static function start() {
    $infinite = False;

    if ($infinite) $lifetime = time() + Config::get('session-infinite-timeout');
    else $lifetime = 0;

    session_set_cookie_params($lifetime, '/', $_SERVER['HTTP_HOST'], Request::isSecure(), True);
    session_start();
  }

  /**
   * Opens a new session with given variables
   **/
  public static function open($changes) {
    session_regenerate_id(True);
    self::update($changes);
  }

  /**
   * Clear out all session variables
   **/
  public static function clear() {
    foreach (array_keys($_SESSION) as $key) {
      unset($_SESSION[$key]);
    }
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

