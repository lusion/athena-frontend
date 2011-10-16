<?php

abstract class DataRow {

  /**
   * @var array $row Field name => value
   */
  protected $row = array();

  /**
   * Database table 
   */
	private $table;

  /***
   * Construct a data row
   * @param class_name Class name for the data row
   * @param data Data associative array
   **/
  function __construct($table, $data) {
    $this->table = $table;
    $this->row = $data;
  }

  public function __get($var) {
    if (array_key_exists($var, $this->row)) {
      return $this->row[$var];
    }

    throw new UndefinedPropertyException($this, $var);
  }


  public function __isset($var) {
    if (array_key_exists($var, $this->row)) {
      return True;
    }
    return False;
  }

  /***
   * Commit changed fields to memory row
   */
  public function update($changes) {
		foreach ($changes as $k=>$v) {
			$this->row[$k] = $v;
		}
  }

  /***
   * Transformation functions
   **/
  static function transformValues($class, $data) {
		foreach ($data as $k=>&$v) {
      $type = isset($class::$COLUMNS[$k]['type']) ? $class::$COLUMNS[$k]['type'] : NULL;

      if ($v === NULL) continue;

      switch ($type) {
      case 'date':
        $v = Date\Immutable::load($v);
      }
    }
    return $data;
  }
}
//-----------------------------------------------------------------------------



