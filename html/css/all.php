<?php
header('Content-type: text/css');
foreach (array(
  'screen.css',
  'rounded.css',
  'backtrace.css'
) as $css) {
  echo file_get_contents(__DIR__.'/'.$css);
}
