<?php
Layout::header();
$site = Site::current();

Section::render(array(
  'title' => 'Backups',
  'quota-limit' => NULL, 'quota-total' => NULL,
  'items' => Site_Backup::search($site),
  'headers' => array(
    '$item->created(F jS Y \\a\\t g:ia)' => 'Date',
    '$item->mysql_size(>bytesize)' => 'MySQL Storage',
    '$item->state' => 'State',
  ),
  'empty-message' => 'There are currently no backups for this site.',
  'dialogs' => array('add-database' => 'Backup Now'),
));

