<?php

class Database {

  function __construct($properties) {
    foreach ($properties as $k=>$v) {
      $this->$k = $v;
    }
  }

  static function search($search=array()) {
    $search = new Search('database', $search);

    $databases = array();

    if ($v = $search->param('site')) {
    }

    $search->checkParams();

    return $databases;
  }
}
