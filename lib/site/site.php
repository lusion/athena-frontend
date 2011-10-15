<?php

class Site extends Object {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'owner_id' => array('default' => NULL, 'type' => 'id'),
    'server_id' => array('default' => NULL, 'type' => 'id'),
    'state' => array('default' => 'active', 'options' => array('pending', 'upgrade', 'active')),
    'server_uid' => array('default' => '0'),
    'ip_address_id' => array('default' => NULL, 'type' => 'id'),
    'username' => array('default' => NULL),
    'domain' => array('default' => NULL),
    'password_crypt' => array('default' => NULL),
    'password_mysql' => array('default' => NULL),
    'parked' => array('default' => '0'),
    'suspended' => array('default' => '0'),
    'use_ssh' => array('default' => '0'),
    'use_ssl' => array('default' => '0'),
    'updated' => array('default' => 'CURRENT_TIMESTAMP'),
    'created' => array('default' => '0000-00-00 00:00:00')
  );
  static $current = NULL;

  static function buildSearch($search=array()) {
    $search = new Search('site', $search);

    if ($v = $search->param('username')) { $search->eq('site.username', $v); }
    if ($v = $search->param('domain')) { $search->eq('site.domain', $v); }

    if ($v = $search->param('owner')) {
      $search->id('site.owner_id', $v, 'owner');
    }

    return $search;
  }

  static function current() {
    return self::$current;
  }

  function makeActive() {
    self::$current = $this;
  }

  function fetchStatistics() {
    $result = Connection::open('master')->call('/stats', array('site-id'=>$this->id));
    return $result['stats'];
  }

}
