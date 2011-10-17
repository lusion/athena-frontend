<?php

class Site_Application extends Object {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'site_id' => array('default' => NULL, 'type' => 'id'),
    'application' => array('default' => NULL),
    'state' => array('default' => 'pending', 'options' => array('pending', 'active')),
    'path' => array('default' => NULL),
    'database' => array('default' => NULL),
    'updated' => array('default' => 'CURRENT_TIMESTAMP', 'type' => 'date'),
    'created' => array('default' => '0000-00-00 00:00:00', 'type' => 'date')
  );

  static function buildSearch($search=array()) {
    $search = new Search('site_application', $search);

    if ($v = $search->param('site')) $search->id('site_id', $v, 'site');

    return $search;
  }

  function __get($var) {
    switch ($var) {
    case 'url':
      return 'http://www.'.substr($this->path, strlen('/sites/'));
    }
    return parent::__get($var);
  }

  function __isset($var) {
    switch ($var) {
    case 'url':
      return True;
    }
    return parent::__isset($var);
  }

}

