<?php

class OpenSSL {

  static function sign($message) {
		$config = Config::get('openssl');

    if (!isset($config['private-key'])) {
      throw new \Exception("Could not find private key to sign: $message");
    }

    $key = openssl_pkey_get_private($config['private-key']);

    if (!openssl_sign($message, $signature, $key)) {
      throw new \Exception("Unable to sign message: $message");
    }

    return $signature;
  }

}

