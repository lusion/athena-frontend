<?php

namespace HTTP;

class Response {
  function __construct($code, $content) {
    $this->code = $code;
    $this->codeGroup = intval($code / 100);
    $this->content = $content;
  }

  function is100() { return $this->codeGroup == 1; }
  function is200() { return $this->codeGroup == 2; }
  function is300() { return $this->codeGroup == 3; }
  function is400() { return $this->codeGroup == 4; }
  function is500() { return $this->codeGroup == 5; }

  /***
   * Returns the content only if successful
   **/
  function read() {
    if ($this->is200()) {
      return $this->content;
    }else{
      return '';
    }
  }
}


