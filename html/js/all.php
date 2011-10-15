<?php
header('Content-type: text/javascript');
foreach (array(
  'dialog.js',
  'interface.js',
) as $js) {
  echo file_get_contents(__DIR__.'/'.$js);
}
