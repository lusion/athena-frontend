<?php 
require __DIR__.'/../init.php';

// Add some definitions for the process
$host = explode('.', $_SERVER['HTTP_HOST']);
define('account_username', $host[0]);

// Load the view
View::render(Uri::interpretRequest());
