<?php

Layout::header();
$site = Site::current();

Section::render(array(
  'title' => 'File Manager',
  'class'=>'file-manager',
  'content' => new HTML(
    '<iframe src="'.$site->getFileManagerURL().'" width="960" height="600"></iframe>'
  )
));

