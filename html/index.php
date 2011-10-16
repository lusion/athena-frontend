<?php 
require __DIR__.'/../init.php';

// Add some definitions for the process
$host = explode('.', $_SERVER['HTTP_HOST']);
define('account_username', $host[0]);

// Check the current session
Session::start();

// Load the view
View::find(Uri::interpretRequest())->render();

// Include the footer if we had a header
Layout::footer();
