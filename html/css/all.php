<?php
header('Content-type: text/css');
foreach (array(
  'screen.css',
  'site-choice.css',
  'dialog.css',
  'section.css',
  'page.css',
  'rounded.css',
  'backtrace.css'
) as $css) {
  echo file_get_contents(__DIR__.'/'.$css);
}
