<?php

/***
 * Class to prevent cross site requests. 
 *
 * Does basic authentication on site_id / domain and simple post filtering
 **/
class CSRF {

  static function generate($uri, $data, $post) {
    $token = time()."\0".$uri."\0".serialize($data)."\0".implode("\0", $post);
    $token .= "\0".md5($token);
    return base64_encode($token);
  }

  static function render($uri, $data, $post) {
    print '<input type="hidden" name="csrf" value="'.HTML(self::generate($uri, $data, $post)).'" />';
  }

  static function data() {
    if (!Request::isPost()) return False;
    if (!$csrf = POST('csrf')) return False;

    $parts = explode("\0", base64_decode($csrf));

    // Check the checksum
    $checksum = array_pop($parts);
    if ($checksum != md5(implode("\0", $parts))) {
      return False;
    }

    // Check time and uri
    if (abs(array_shift($parts)-time()) > 900) return False;
    if (array_shift($parts) != $_SERVER['REQUEST_URI']) return False;

    // Pull out the hard-coded data
    $data = unserialize(array_shift($parts));

    // Extract the relevant parts from the post array
    return array_merge(POST($parts), $data);
  }
}
