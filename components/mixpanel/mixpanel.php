<?php

class Mixpanel {
  static function track($event, $properties) {
    $properties['token'] = Config::get('mixpanel-token');

    $url = 'http://api.mixpanel.com/track/?data='.base64_encode(json_encode(array(
      'event' => $event,
      'properties' => $properties
    )));

    HTTP::get($url);
  }
}

