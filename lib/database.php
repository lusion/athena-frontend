<?php

class Database {

  function __construct($properties) {
    foreach ($properties as $k=>$v) {
      $this->$k = $v;
    }

    $this->id = $this->name;
    $this->oldest = Date\Immutable::load($this->oldest);
    $this->update = Date\Immutable::load($this->update);
  }

  static function search($search=array()) {
    $search = new Search('database', $search);

    $databases = array();

    $site = Site::load($search->param('site'));
    $result = Master::post('/mysql/databases/list', array('site-id'=>$site->id));
    foreach ($result['databases'] as $db) {
      $databases[] = new Database($db);
    }

    $search->checkParams();

    return $databases;
  }
}
