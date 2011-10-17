<?php

class Site_SSH_Key extends Object {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'site_id' => array('default' => NULL, 'type' => 'id'),
    'title' => array('default' => NULL),
    'type' => array('default' => NULL, 'options' => array('ssh-dss', 'ssh-dsa', 'ssh-rsa')),
    'public_key' => array('default' => NULL),
    'fingerprint' => array('default' => NULL),
    'updated' => array('default' => 'CURRENT_TIMESTAMP', 'type' => 'date'),
    'created' => array('default' => '0000-00-00 00:00:00', 'type' => 'date')
  );

  static function buildSearch($search=array()) {
    $search = new Search('site_ssh_key', $search);

    if ($v = $search->param('site')) $search->id('site_id', $v, 'site');

    return $search;
  }
}
