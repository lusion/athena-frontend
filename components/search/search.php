<?php

class UncheckedField extends ContextException {
  function __construct($field, $extra=array()) {
    parent::__construct('Field "'.$field.'" was not checked', $extra);
  }
}

class SearchResultIterator implements Iterator, Countable {
	function __construct($dbtable, $results, $preload) {
		$this->dbtable = $dbtable;
		$this->results = $results;
		$this->preload = $preload;

		$this->current = $this->results->getRow();
    $this->position = 0;
	}

  public function count() {
    return count($this->results);
  }

  public function load(&$data, $table) {
    if (count($data) < count($table::$COLUMNS)) {
      throw new Exception('Not enough columns to pull data from');
    }
    $slice = array_splice($data, 0, count($table::$COLUMNS));
    // Load the table if there is a single non-NULL value
    foreach ($slice as $v) {
      if ($v !== NULL) {
        return $table::load(array_combine(array_keys($table::$COLUMNS), $slice));
      }
    }
    // All the values where null
    return NULL;
  }
  public function rewind() { $this->results->rewind(); $this->next(); }
  public function current() {
    // Garbage collect after each 100
    if (($this->position+1) % 100 == 0) {
      if (Toggle::get('garbage_collect')) {
        ObjectFactory::garbage_collect();
      }
    }
    

    $data = $this->current;

    $object = $this->load($data, $this->dbtable);

    // Preload each object as needed
    foreach ($this->preload as $preload) {
      $this->load($data, $preload);
    }

    if ($data) {
      throw new Exception('Leftover data remaining after searchresult loads');
    }
    return $object;
  }

  public function key() { return $this->position; }
  public function next() { $this->position++; $this->current = $this->results->getRow(); }
  public function valid() { return $this->current ? True : False; }
}

class Search {
	private $tokens = array();
	private $params = array();
	private $checked = array();
	private $preload = array();
  private $condition;
  private $condition_stack = array();
	private $join_id = 1;
  private $primary;

  private static $emulated = array();

  /***
   * Emulate a given search
   **/
  static function emulate($dbtable, $query, $results) {
    self::$emulated[$dbtable][] = array($query, $results);
  }

  /***
   * Find if a search needs to be emulated
   **/
  static function fetchEmulated($dbtable, $query) {
    if (isset(self::$emulated[$dbtable])) {
      foreach (self::$emulated[$dbtable] as &$emulatedQuery) {
        if ($emulatedQuery[0] == $query) return $emulatedQuery[1];
        var_dump($emulatedQuery[0], $query);
      }
    }
    return NULL;
  }


	// Constructor
	function __construct($dbtable, $search, $primary='id') {
		$SQL = Connection::open('sql');

    if (!is_array($search)) {
      $search = array($search);
    }

    $this->primary = $primary;
		$this->dbtable = $dbtable;
		$this->sql_from = '`'.$dbtable.'`'; 
    $this->condition = new SQLCondition();
    $this->condition_stack = array($this->condition);

		foreach ($search as $k=>$v) {
      if (is_int($k)) {
        if (is_object($v)) $k = strtolower(get_class($v));
        elseif (is_string($v) && empty($this->params['query'])) $k = 'query';
        elseif ($v === False) {
          $this->and_0();
        }
      }
      $this->params[$k] = $v;
		}

    // Load in the preload settings
    $this->preload = $this->param('preload');
    if (!$this->preload) $this->preload = array();
    elseif (!is_array($this->preload)) $this->preload = array($this->preload);

		// Split search query up into tokens
		if ($query = $this->param('query')) {
			$query = str_replace(array('“','”'), '"', $query);


			// This code is a bit slow, but search queries are short
			$tok = ''; $len = strlen($query); $quoted = False;
			for ($k=0; $k<$len; $k++) {
				switch ($query[$k]) {
				case '\\': $tok .= $query[$k++]; break;
				case  ' ':
					if ($quoted) { $tok .= ' '; }
					elseif ($tok) { $this->tokens[] = $tok; $tok = ''; }
					break;
				case '"':
					$quoted = !$quoted;
					break;

				default: $tok .=  $query[$k];
				}
			}
			if ($tok) $this->tokens[] = $tok;
		}
	}

	function set_param($k, $v = NULL) {
    if (is_array($k) && $v === NULL) {
      foreach ($k as $a => $b) $this->set_param($a, $b);
    }else{
      if ($k == 'reseller_id') $k = 'reseller'; // override reselle rather, so default doesn't cause problems
      $this->params[$k] = $v;
    }
	}

  function preload($table) {
    return in_array($table, $this->preload, True);
  }

	function param($k, $default=NULL) {
    $this->checked[$k] = True;
		if ($k == 'reseller' && Reseller::current()) $default = Reseller::current()->id;

    // Magic _id params
    if (!isset($this->params[$k]) && isset($this->params[$k.'_id'])) {
      return $this->param($k.'_id', $default);
    }
		return ARR($this->params,$k,$default);
	}
	function get_tokens() {
		return $this->tokens;
	}

  // Join the field_value column where needed
  function search_field_values() {
    foreach ($this->params as $k => $v) {
      if (substr($k,0,5) == 'data-') {
        $fv = 'fv'.$this->join_id++;
        $this->join('field_value as '.$fv, $fv.'.item_type='.SQL($this->dbtable).' and '.$fv.'.item_id='.$this->dbtable.'.id');
        $this->eq($fv.'.variable', substr($k, 5));
        $this->eq($fv.'.value', $this->param($k));
      }
    }
  }

	function remove_token($token) {
		foreach ($this->tokens as $k=>$tok) {
			if ($tok == $token) unset($this->tokens[$k]);
		}
	}

  function add_token($token) {
    $this->tokens[] = $token;
  }

  /***
   * Push a condition on to the stack
   **/
  function pushCondition($condition) {
    $this->condition = $this->condition_stack[] = $condition;
  }
  /***
   * Pop last condition of stack, and start writing to new last one
   **/
  function popCondition() {
    $condition = array_pop($this->condition_stack);
    $this->condition = $this->condition_stack[count($this->condition_stack)-1];
    $this->condition->push($condition);
  }
  /***
   * Build SQL "WHERE" statement from the condition
   **/
  function getSQLWhere() {
    if (count($this->condition_stack) > 1) throw Exception("Cannot build query with non-empty condition stack");
    return $this->condition->buildSQL();
  }

  /***
   * Param helpers
   **/
  function cmp_param($param, $field, $transform=NULL) {
    foreach (array(
      '>' => 'gt', '>=' => 'geq',
      '<' => 'lt', '<=' => 'leq',
      '' => 'eq') as $match => $fn)
    {
      if ($value = $this->param($param.$match)) {
        if ($transform) $value = $transform($value);
        $this->$fn($field, $value);
      }
    }
  }

  /***
   * Wrap new conditions in a NOT()
   **/
  function begin_not() {
    $this->pushCondition(new SQLCondition(array(
      'negative' => True,
    )));
  }
  /***
   * Stop wrapping new conditions in a NOT()
   **/
  function end_not() {
    $this->popCondition();
  }

	function join($table, $on) {
		$this->sql_from .= ' JOIN '.$table.' ON '.$on;
	}
	function leftjoin($table, $on) {
		$this->sql_from .= ' LEFT JOIN '.$table.' ON '.$on;
	}
	function and_sql($sql) {
		$this->condition->push($sql);
	}

	function comp($field, $value, $comp) {
		$this->and_sql(SQL::field($field).$comp.SQL($value));
	}
	function lt($field, $value) { $this->comp($field, $value, '<'); }
	function gt($field, $value) { $this->comp($field, $value, '>'); }
	function leq($field, $value) { $this->comp($field, $value, '<='); }
	function geq($field, $value) { $this->comp($field, $value, '>='); }
	function neq($field, $value) { $this->comp($field, $value, '!='); }
  function eq($field, $value) {
    if ($value === NULL) $this->comp($field, $value, ' is ');
    else $this->comp($field, $value, '=');
  }

  function eq_year_month($field, $value) {
    $value = Date::load($value);
		$this->and_sql('(YEAR('.SQL::field($field).')='.$value->year.' AND MONTH('.SQL::field($field).')='.$value->month.')');
  }

  function and_0() {
    $this->and_sql('0');
  }
	function null($field) {
		$this->and_sql(SQL::field($field).' is NULL');
	}
  function null_or_sql($field, $sql) {
    $this->and_sql('('.SQL::field($field).' is NULL OR ('.$sql.'))');
  }
	function not_null($field) {
		$this->and_sql(SQL::field($field).' is not NULL');
	}
	function eq_or_null($field, $value) {
		$this->null_or_sql($field, SQL::field($field).'='.SQL($value));
	}
	function in($field, $options) {
		if ($options) $this->and_sql(SQL::in($field, $options));
		else $this->and_sql('0');
	}
	function in_or_null($field, $options) {
		if ($options) $this->null_or_sql($field, SQL::in($field, $options));
		else $this->null($field);
	}
	function not_in($field, $options) {
		$this->and_sql('not '.SQL::in($field, $options));
	}

	function identifier($field, $options, $_null=False) {
		if (!is_array($options)) $options = $options ? explode(',', $options) : array();

    // Allow any state if options=*
    if (count($options) == 1 && array_first($options) == '*') return;

		$ok = $nok = array();

		foreach ($options as $o) {
			if ($o[0] == '!') {
				$nok[] = substr($o,1);
			}else{
				$ok[] = $o;
			}
		}
    if ($ok) {
      if ($_null) $this->in_or_null($field, $ok);
      else $this->in($field, $ok);
    }
		if ($nok) $this->not_in($field, $nok);
  }

	function state($field, $options) {
    $this->identifier($field, $options);
	}

  function code($field, $codes, $class=NULL) {
    if (is_string($codes)) $codes = explode(',', $codes);
    else if (!is_array($codes)) $codes = array($codes);

    // Convert to actual code's
    $codes = array_map(function($code) use ($class) {
      // String codes can be prefixed by !
      return is_string($code) ? $code : $class::load($code)->code;
    }, $codes);

    // Use identifier function for codes
    $this->identifier($field, $codes);
  }

  function id($field, $ids, $class=NULL, $_null=False) {
    if (is_string($ids)) $ids = explode(',', $ids);
    else if (!is_array($ids)) $ids = array($ids);

    // Convert to actual id's
    $ids = array_map(function($id) use ($class) {
      // Numeric or !(numeric)
      if (is_numeric($id) || (is_string($id) && $id[0] == '!' && is_numeric(substr($id, 1)))) {
        return $id;
      }else{
        return $class::load($id)->id;
      }
    }, $ids);

    // Use identifier function for id's
    $this->identifier($field, $ids, $_null);
  }

  function id_or_null($field, $ids, $class=NULL) {
    return $this->id($field, $ids, $class, True);
  }

  function check_id($field, $table) {
    if (($s = $this->param('reseller')) || ($s = $this->param('reseller_id'))) {
      if (is_string($s)) $s = explode(',', $s);
      else if (!is_array($s)) $s = array($s);

      $this->in($field,array_map(function($s) { return Reseller::load($s)->id; }, $s));
    }
  }
  function checkParams() {
    foreach ($this->params as $k=>$v) {
      if (empty($this->checked[$k]) && $k != 'orderby' && $k != 'limit' && $k != 'lock') {
        throw new UncheckedField($k, array('dbtable'=>$this->dbtable));
      }
    }
    return True;
  }
  /**
   * Add a LIKE clause
   */
  public function like($field, $search) {
    $this->and_sql(SQL::field($field).' LIKE '.SQL($search));
  }


  function executeIterator(&$paging = False) {
		$SQL = Connection::open('sql');
    if (!$this->checkParams()) throw new FatalException();
		$orderby = $this->param('orderby');

    $query = '`'.$this->dbtable.'`.*';
    foreach ($this->preload as $table) {
      $query .= ', `'.$table.'`.*';
    }
		$query .= ' FROM '.$this->sql_from.' WHERE '.$this->getSQLWhere().($orderby?' ORDER BY '.$orderby:'');

    // Limit if its needed
    if ($limit = $this->param('limit')) $query .= ' LIMIT '.$limit;

    // Add appropriate lock
    $query .= SQL::lock($this->param('lock', NULL));

		if ($paging) {

			$paging['perpage'] = max(1,INT(ARR($paging,'perpage',25)));
			$paging['page'] = max(1, INT(ARR($paging,'page',1)));
      // Disable SQL_CALC_FOUND_ROWS with order by. See http://bugs.mysql.com/bug.php?id=18454
      $optimise = ($orderby ? False : True);
      // Run the paged query
			$res = $SQL->selectPaged($query, $paging, $optimise);
		}else{
			$res = $SQL->select($query);
		}

    return new SearchResultIterator($this->dbtable, $res, $this->preload);
	}

	function exists() {
		$SQL = Connection::open('sql');
    if (!$this->checkParams()) throw new FatalException();
		return !!($SQL->exists($this->sql_from.' WHERE '.$this->getSQLWhere()));
	}
	function count() {
		$SQL = Connection::open('sql');
    if (!$this->checkParams()) throw new FatalException();
		return intval($SQL->getSingle('count(*) FROM '.$this->sql_from.' WHERE '.$this->getSQLWhere()));
	}
	function execute(&$paging = False) {
		$SQL = Connection::open('sql');
		
    if (!$this->checkParams()) throw new FatalException();
		$objects = array();
		$dbtable = $this->dbtable;

    $primary = $this->primary;
    foreach ($this->executeIterator($paging) as $object) {
      if ($primary) {
        $objects[$object->$primary] = $object;
      }else{
        $objects[] = $object;
      }
		}
		return $objects;
  }
}


