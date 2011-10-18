<?php

if ($data = CSRF::data()) {
  $action = $data['action'];
  unset($data['action']);

  try {
    $result = Master::post($action, $data);
  } catch (Exception $e) {
    $result = array(
      'status' => 'error',
      'error-message' => 'There was an error on our side while processing your request. Please try again later or contact the support team for assistance.'
    );
    Debug::logException($e);
  }
  Layout::result($result);
}

if ($site = Site::searchSingle(array('domain'=>array_shift($extra)))) {
  if (!$site->checkSessionAccess()) {
    $site = NULL;
  }
}

if (!$site) {
  if ($site = Site::searchSingle(Site::sessionSearchOptions())) {
    redirect('/site/'.$site->domain);
  }else{
    Session::clear();
    redirect('/login');
  }
}

$site->makeActive();

if ($extra) {
  if ($view = View::find($extra, 'views/pages')) {
    $view->render();
  }else{
    Layout::header();
    $block = new Block(new HTML('Page not found: <span class="plain">'.HTML($site->domain).'</span>'));
    $block->header();
    print 'Sorry, we could not find the page you were looking for.';
    $block->footer();
  }
}else{
  View::display('dashboard', 'views/pages');
}
