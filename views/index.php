<?php
if ($site = Site::searchSingle(Site::sessionSearchOptions())) {
  $site->makeActive();

  Layout::header();
  $block = new Block('Page not found');
  $block->header();
  print 'Sorry, we could not find the page you were looking for.';
  $block->footer();
}else{
  Session::clear();
  redirect('/login');
}
