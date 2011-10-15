<?php

class Object {
  private $table = null;
  private $datarow = null;

  static function load($data) {

    if (is_array($data)) {
      $primary = static::dataPrimaryKey($data);
    }else{
      $primary = $data;
      $data = null;
    }

    return ObjectFactory::get(get_called_class(), $primary, $data);
  }

  function __construct($datarow) {
    $this->table = strtolower(get_called_class());
    $this->datarow = $datarow;
  }

  function __get($var) {
    if ($var == 'table') return $this->table;
    else{
      return $this->datarow->$var;
    }
  }

  /***
   * Code to calculate primary keys
   **/
  public static function dataPrimaryKey($data) {
    return ARR($data, 'id', null) ?: ARR($data, 'code', null);
  }
  function primaryKey() {
    if (isset(static::$COLUMNS['code'])) {
      return strtolower($this->code);
    }else{
      return $this->id;
    }
  }

  /***
   * Handy shortcuts for searches
   **/
	static function search($search = array()) {
		$search = static::buildSearch($search);
		return $search->execute();
	}
	static function searchPaged($search=array(), &$paging=False) {
		$search = static::buildSearch($search);
		return $search->execute($paging);
	}
	static function searchSingle($search=array()) {
    if (!is_array($search)) $search = array($search);
		$results = static::search(array_merge($search, array('limit'=>'1')));
		if ($results) return array_first($results);
		else return NULL;
	}
	static function searchIterator($search=array(), &$paging=False) {
		$search = static::buildSearch($search);
    return $search->executeIterator($paging);
	}
	static function count($search=array()) {
		$search = static::buildSearch($search);
		return $search->count();
	}
	static function exists($search=array()) {
		$search = static::buildSearch($search);
		return $search->exists();
	}
}
