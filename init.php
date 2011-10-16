<?php
chdir(__DIR__);
require 'free/common.php';
require 'components/config/config.php';
require 'config.php';

Autoload::addPath('components');
Autoload::addPath('layout');
Autoload::addPath('lib', array('\\'));

Autoload::preload(array('debug', 'http', 'backtrace', 'view', 'mixpanel'));

ExceptionHandler::setup(function($exception) {
  try {
    Mixpanel::track('exception', array(
      'class' => get_class($exception),
      'code' => $exception->getCode()
    ));
  } catch (Exception $trackException) {}

  Debug::logException($exception);

  View::display('error');

  Layout::footer();
});

// Set up correct encoding
header('Content-Type: text/html; charset=UTF-8');

// Add the MySQL connection
Connection::add('sql', function() {
  return new Db\Connection(Config::get('mysql'));
});

// @todo This should probably be moved
function redirect($url, $done=True) {
  if (headers_sent()) {
    if (DEF('developer')) {
      print 'Redirect to <a href="'.HTML($url).'">'.HTML($url).'</a>';
    }else{
  ?><script type="text/javascript">window.location.href="<?php echo ($url); ?>";</script>
    <noscript><meta http-equiv="refresh" content="0;url=<?php echo ($url); ?>" /></noscript><?php
    }
  } else {
          header('Location: '.($url));
  }

  print '<a href="'.($url).'">Redirecting...</a>';

  if ($done) {
    if (function_exists('done')) {
      done();
    }else{
      Connection::commit();
      exit(0);
    }
  }
}

