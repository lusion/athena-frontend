<?php

class Hash {
  /***
   * Beginning of "ideal" salt: bcrypt with given work factor
   **/
  static function saltStart($strength) {
    return '$2a$'.sprintf('%02d', $strength).'$';
  }

  /***
   * Generate a salt according to the current mode
   * -- bcrypt salt: length=22 alphabet=./0-9A-Za-z
   **/
  static function generateSalt($strength=10) {
    return self::saltStart($strength) . random_string(22, './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
  }

  /***
   * Hash a password with a given salt
   **/
  static function create($password, $strength=10) {
    return crypt($password, self::generateSalt($strength));
  }

  /***
   * Checks a password against a given salted hash
   **/
  static function check($password, $hash) {
    if (starts_with($hash, '$2a$')) {
      $salt = substr($hash, 0, 19);
    }elseif (starts_with($hash, '$6$')) {
      $salt = substr($hash, 0, 19);
    }else{
      throw new Exception('Unknown hash type: '.$hash);
    }
    return crypt($password, $salt) === $hash;
  }

  /***
   * Checks if a password is secure according to current standards
   **/
  static function isSecure($password, $salt, $strength=10) {
    return $salt && starts_with($salt, self::saltStart($strength));
  }
}

