<?php
chdir(__DIR__);
require 'free/common.php';
require 'components/config/config.php';
require 'config.php';

Autoload::addPath('components');
Autoload::addPath('layout');
Autoload::addPath('lib');

Autoload::preload(array('debug', 'http', 'backtrace', 'view', 'mixpanel'));

ExceptionHandler::setup(function($exception) {
  try {
    Mixpanel::track('exception', array(
      'class' => get_class($exception),
      'code' => $exception->getCode()
    ));
  } catch (Exception $trackException) {}

  Debug::logException($exception);

  View::render('error');

  Layout::footer();
});

// Set up correct encoding
header('Content-Type: text/html; charset=UTF-8');

// Add the MySQL connection
Connection::add('sql', function() {
  return new Db\Connection(Config::get('mysql'));
});

