<?php
$site = Site::searchSingle(array('domain'=>array_shift($extra)));
$site->makeActive();

View::render($extra, 'views/pages');
