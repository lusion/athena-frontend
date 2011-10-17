<?php

class Site_Backup extends Object {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'site_id' => array('default' => NULL, 'type' => 'id'),
    'state' => array('default' => 'pending', 'options' => array('pending', 'complete')),
    'mysql_size' => array('default' => '0'),
    'updated' => array('default' => 'CURRENT_TIMESTAMP', 'type' => 'date'),
    'created' => array('default' => '0000-00-00 00:00:00', 'type' => 'date')
  );

  static function buildSearch($search=array()) {
    $search = new Search('site_backup', $search);

    if ($v = $search->param('site')) $search->id('site_id', $v, 'site');

    return $search;
  }
}
