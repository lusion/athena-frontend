<?php 
require __DIR__.'/../init.php';

// Add some definitions for the process
$host = explode('.', $_SERVER['HTTP_HOST']);
define('account_username', $host[0]);

// Show the plain index page if this is not a reseller login page
$SQL = Connection::open('sql');
if (!$SQL->exists('reseller WHERE username='.SQL(account_username))) {
  $response = HTTP::get('https://username.snapbill.com/get.php?service=snapbill&username='.URL(account_username));
  if ($response) {
    $response = json_decode($response, True);
    if ($response['state'] == 'active') {
      $SQL->insert('reseller', array('username'=>account_username));
    }else $response = NULL;
  }

  if (!$response) {
    file_get_contents(__DIR__.'/plain/index.htm');
    exit(0);
  }
}

// Check the current session
Session::start();

// Load the view
View::find(Uri::interpretRequest())->render();

// Include the footer if we had a header
Layout::footer();
