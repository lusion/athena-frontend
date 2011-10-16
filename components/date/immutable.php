<?php
namespace Date;

class Immutable extends \Date {
	function __set($var, $val) { throw new FatalException('Date is immutable', array('date'=>$this)); }
}
