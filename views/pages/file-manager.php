<?php

Layout::header();
$site = Site::current();

print '<div id="json-wrap">';

Section::render(array(
  'title' => 'File Manager',
  'content' => new HTML(
    '<iframe src="'.$site->getFileManagerURL().'" width="960" height="600"></iframe>'
  )
));

print '</div>';
