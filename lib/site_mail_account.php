<?php

class Site_Mail_Account extends Object {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'site_id' => array('default' => NULL, 'type' => 'id'),
    'firstname' => array('default' => NULL),
    'surname' => array('default' => NULL),
    'username' => array('default' => NULL),
    'password_crypt' => array('default' => NULL),
    'fname' => array('default' => ''),
    'lname' => array('default' => ''),
    'updated' => array('default' => 'CURRENT_TIMESTAMP', 'type' => 'date'),
    'created' => array('default' => '0000-00-00 00:00:00', 'type' => 'date')
  );

  static function buildSearch($search=array()) {
    $search = new Search('site_mail_account', $search);

    if ($v = $search->param('site')) $search->id('site_id', $v, 'site');

    return $search;
  }
}
