<?php

class Site extends Object {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'client_id' => array('default' => NULL, 'type' => 'id'),
    'reseller_id' => array('default' => NULL, 'type' => 'id'),
    'server_id' => array('default' => NULL, 'type' => 'id'),
    'database_server_id' => array('default' => NULL, 'type' => 'id'),
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
    'updated' => array('default' => 'CURRENT_TIMESTAMP', 'type' => 'date'),
    'created' => array('default' => '0000-00-00 00:00:00', 'type' => 'date')
  );
  static $current = NULL;

  static function buildSearch($search=array()) {
    $search = new Search('site', $search);

    if ($v = $search->param('id')) { $search->eq('site.id', $v); }

    if ($v = $search->param('username')) { $search->eq('site.username', $v); }
    if ($v = $search->param('domain')) { $search->eq('site.domain', $v); }

    if ($v = $search->param('client_id')) $search->eq('site.client_id', $v);
    if ($v = $search->param('reseller_id')) $search->eq('site.reseller_id', $v);

    return $search;
  }

  static function sessionSearchOptions() {
    if ($site_id = Session::get('site_id')) {
      return array('id'=>$site_id);
    }elseif ($client_xid = Session::get('client_xid')) {
      list($reseller_id, $client_id) = Xid::decode($client_xid);
      return array('client_id'=>$client_id, 'reseller_id'=>$reseller_id);
    }elseif ($reseller_id = Session::get('reseller_id')) {
      return array('reseller_id'=>$reseller_id);
    }else{
      return False;
    }
  }

  static function current() {
    return self::$current;
  }

  function getFileManagerURL() {
		// Create a signed key
		$key = $this->username.':'.time();
		$key .= ':' . OpenSSL::sign($key);

		// Base64-URL encode the key
		$key = strtr(base64_encode($key), '+/', '-_');

		// Put it all together for the file manager url
		return (Request::isSecure() ? 'https' : 'http') . '://'.$this->username.'.'.Config::get('domain').'/ajaxplorer?hostdeploy-key='.$key.'&ignore_tests=true';
  }

  function getDomains() {
    $domains = array($this->domain);
    foreach (Site_Subdomain::searchIterator($this) as $subdomain) {
      $domains[] = $subdomain->domain;
    }
    return $domains;
  }

  function getPaths($options=array()) {
    $paths = array();

    $live = ARR($options, 'live', False);

    if (!$live) $paths[] = '/';

    foreach ($this->getDomains() as $domain) {
      $paths[] = '/sites/'.$domain;
    }

    return $paths;
  }

  function checkSessionAccess() {
    if ($site_id = Session::get('site_id')) {
      return ($this->id == $site_id);
    }elseif ($client_xid = Session::get('client_xid')) {
      list($reseller_id, $client_id) = Xid::decode($client_xid);
      return ($this->client_id == $client_id && $this->reseller_id == $reseller_id);
    }elseif ($reseller_id = Session::get('reseller_id')) {
      return ($this->reseller_id == $reseller_id);
    }else{
      return False;
    }
  }

  function makeActive() {
    self::$current = $this;
  }

  function fetchStatistics() {
    $result = Connection::open('master')->call('/stats', array('site-id'=>$this->id));
    return $result['stats'];
  }

}
