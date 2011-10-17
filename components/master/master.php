<?php
class Master {

  static function post($uri, $data) {
    $result = HTTP::post('http://master.'.Config::get('domain').':5307'.$uri, $data);
    if ($result->is200() || $result->is400()) {
      return json_decode($result->content, True);
    }else{
      throw new ContextException('HTTP '.$result->code.' connecting to master', array('body'=>$result->content));
    }
  }


}
