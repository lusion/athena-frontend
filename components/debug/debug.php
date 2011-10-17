<?php

class Debug {

  static function log($message) {
  }

  static function logException($e) {

    print $e->getMessage();
    if ($e instanceof ContextException) {
      print '<pre>';
      var_dump($e->getContext());
      print '</pre>';
    }

    $backtrace = new Backtrace($e->getTrace());
    $backtrace->render();

  }

  static function logExternal($type, $properties) {
  }
}
