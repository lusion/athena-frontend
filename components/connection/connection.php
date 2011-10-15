<?php

class Connection {
  private static $connections = array();
  private static $callbacks = array();

  static function add($name, $callback) {
    self::$callbacks[$name] = $callback;
  }

  static function open($name) {
    if ($connection = self::existing($name)) {
      return $connection;
    }

    if (!$callback = ARR(self::$callbacks, $name)) {
      throw new Exception('Could not find callback for connection: '.$name);
    }

    self::$connections[$name] = $callback();

    return self::$connections[$name];
  }

  static function existing($name) {
    return ARR(self::$connections, $name);
  }
}
