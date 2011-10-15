<?php

class Config {
  private static $db = array();

  static function write($opt) {
    foreach ($opt as $k => $v) {
      self::$db[$k] = $v;
    }
  }

  static function get($key) {
    return self::$db[$key];
  }
}

