<?php

class View {

  static function load($uri, $path='views') {
    $uri = Uri::split($uri);

    $path .= '/';
    $extra = $uri;

		while ($extra) {
      if ($extra[0] == '.') break;

      if (file_exists($path . $extra[0].'.php')) {
        $filename = array_shift($extra);
        require $path . $filename .'.php';
        return True;
      }elseif (is_dir($path . $extra[0])) {
        $path .= array_shift($extra).'/';
        continue;
      }elseif (file_exists($path . 'index.php')) {
        require $path . 'index.php';
        return True;
      }else{
        break;
      }
    }

    if (file_exists($path . 'index.php')) {
      require $path . 'index.php';
      return True;
    }

		throw new Exception('Could not find view: '.implode('/', $uri).' in '.$path);
  }

  static function render($uri, $path='views') {
    self::load($uri, $path);
  }
}
