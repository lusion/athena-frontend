<?php

class Owner extends Object {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'client_id' => array('default' => NULL, 'type' => 'id'),
    'reseller_id' => array('default' => NULL, 'type' => 'id'),
    'updated' => array('default' => 'CURRENT_TIMESTAMP', 'type' => 'date'),
    'created' => array('default' => '0000-00-00 00:00:00', 'type' => 'date')
  );

  static function current() {
    return self::$current;
  }
}

