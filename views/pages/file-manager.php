<?php

Layout::header();
$site = Site::current();

Section::render(array(
  'title' => 'File Manager',
  'content' => new HTML(
    '<iframe src="'.$site->getFileManagerURL().'" width="960" height="600"></iframe>'
  )
));

