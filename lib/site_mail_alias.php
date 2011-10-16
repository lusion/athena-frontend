<?php

class Site_Mail_Alias extends Object {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'site_id' => array('default' => NULL, 'type' => 'id'),
    'username' => array('default' => NULL),
    'destination' => array('default' => NULL),
    'updated' => array('default' => 'CURRENT_TIMESTAMP', 'type' => 'date'),
    'created' => array('default' => '0000-00-00 00:00:00', 'type' => 'date')
  );

  static function buildSearch($search=array()) {
    $search = new Search('site_mail_alias', $search);

    if ($v = $search->param('site')) $search->id('site_id', $v, 'site');

    return $search;
  }
}
