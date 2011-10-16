<?php 
require __DIR__.'/../init.php';

// Add some definitions for the process
$host = explode('.', $_SERVER['HTTP_HOST']);
define('account_username', $host[0]);

// Check the current session
Session::load();

if ($site = Site::load(Session::get('site-id'))) {
  $site->makeActive();
}

// Load the view
View::render(Uri::interpretRequest());

// Include the footer if we had a header
Layout::footer();
