<?php
ini_set('display_errors', True); error_reporting(E_ALL);

Config::write(array(

  'mysql' => array(
    'username' => 'root',
    'password' => 'qS8crpsJ',
    'host' => 'localhost',
    'database' => 'athena'
  ),

  'domain' => 'hostdep',
  'snapbill-domain' => 'snap',

  'mixpanel-token' => 'ce2c444bc6930bff604de7a6b7c76aa8'
));

