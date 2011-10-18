<?php

class HTTP {
  static $curlOptions = array(
    CURLOPT_CONNECTTIMEOUT => 1,
    CURLOPT_TIMEOUT => 2,
    CURLOPT_FRESH_CONNECT => 1,
    CURLOPT_FORBID_REUSE => 1,
  );

  static function request($url, $options) {
    // @note Can't use array merge because of integer keys
    $curlOptions = self::$curlOptions;
    $curlOptions[CURLOPT_URL] = $url;
    $curlOptions[CURLOPT_HEADER] = 0;
    $curlOptions[CURLOPT_RETURNTRANSFER] = 1;

    if (isset($options['username']) && isset($options['password'])) {
      $curlOptions[CURLOPT_USERPWD] = "{$options['username']}:{$options['password']}";
    }

    if (isset($options['post'])) {
      if (is_array($options['post'])) {
        $post = http_build_query($options['post']);
      }else $post = $options['post'];

      $curlOptions[CURLOPT_POST] = 1;
      $curlOptions[CURLOPT_POSTFIELDS] = $post;
    }

    $startTime = microtime_float();

    $curl = curl_init();
    curl_setopt_array($curl, $curlOptions);
    $content = curl_exec($curl);
    $error = curl_error($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($error) {
      throw new Exception('Curl error on '.$url.': '.$error);
    }

    Debug::logExternal('curl', array(
      'time' => microtime_float() - $startTime
    ));

    return new HTTP\Response(
      $httpCode,
      $content
    );
  }


  static function get($url, $options=array()) {
    return self::request($url, $options);
  }

  static function post($url, $data, $options=array()) {
    $options['post'] = $data;
    return self::request($url, $options);
  }
}

