<?php

class Debug {

  static function log($message) {
  }

  static function logException($e) {

    print $e->getMessage();
    $backtrace = new Backtrace($e->getTrace());
    $backtrace->render();

  }

  static function logExternal($type, $properties) {
  }
}
