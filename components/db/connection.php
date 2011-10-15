<?php

namespace DB;

class Connection {

  //  Connection
  private $mysql = NULL;

  // Database connection settings
  private $host = NULL;
  private $username = NULL;
  private $password = NULL;
  private $database = NULL;

	// Are we currently in a transaction
	private $dirtry = False;
  private $transactionQueries = array();

  // Prepend SQL_NO_CACHE to select queries
  private $disableCache = False;

  // Callback to log all queries run
  private $cbLogQuery = array();
	
  /***
   * Constructor and destructor
   *
   * Destructor throws exception if transaction was uncommitted
   **/
	function __construct($options=array())
	{
		$this->host = ARR($options, 'host');
		$this->username = ARR($options, 'username');
		$this->password = ARR($options, 'password');
		$this->database = ARR($options, 'database');
		$this->connect();
	}
	function __destruct() {
    if ($this->dirty) {
      throw new DroppedTransactionException('Dirty connection destruct without a commit', array(
        'queries' => $this->transactionQueries
      ));
    }
  }

  /***
   * Connect and disconnect
   **/
	function connect() {
    $this->mysql = mysql_connect($this->host,$this->username,$this->password);

    if (!$this->mysql) {
      throw new ConnectException('Could not connect to the  database on '.$this->host.' with username '.$this->username);
    }

    if ($this->database) {
      if (!mysql_select_db($this->database, $this->mysql)) {
        $this->mysql = NULL;
        throw new ConnectException('Could not select '.$this->database.' on '.$this->host.' as '.$this->username);
      }
    }

    if (!mysql_set_charset('utf8', $this->mysql)) {
      throw new ConnectException('Could not set the charset correctly');
    }
		$this->query('SET AUTOCOMMIT=0;', False);
		$this->dirty = False;
	}
	function disconnect()
	{
    if ($this->dirty) {
      throw new DroppedTransactionException('Dirty connection disconnect without a commit', array(
        'queries' => $this->transactionQueries
      ));
    }
    $this->terminate();
	}

  function terminate() {
		mysql_close($this->mysql);
    $this->mysql = NULL;
    $this->dirty = False;
    $this->transactionQueries = array();
  }

  function close() { $this->disconnect(); }

  /***
   * SQL Control Functions
   **/
  function useDatabase($database) {
    $this->database = $database;
    $this->query('USE `'.$database.'`', False);
  }
  function setIsolation($isolation) {
    $this->query('SET tx_isolation='.SQL($isolation), False);
  }
  function isDirty() {
    return $this->dirty;
  }

  /***
   * Transaction commands
   **/
	function commit() {
		$this->query('COMMIT');
    $this->dirty = False;
    $this->transactionQueries = array();
	}
	function rollback() {
		$this->query('ROLLBACK');
    $this->dirty = False;
    $this->transactionQueries = array();
	}

  /***
   * Add a bunch of callbacks
   **/
  function addQueryLogger($callback) {
    $this->cbLogQuery[] = $callback;
  }

  /***
   * Handy mysql commands
   **/
  function ping() {
    $result = mysql_ping($this->mysql);

    // Throw out an exception if connection dissapeared while in transaction
    if (!$result && $this->dirty) {
      throw new DroppedTransactionException();
    }

    return $result;
  }

	function lastInsertedID()
	{
		return mysql_insert_id($this->mysql);
	}

	function affectedRows() {        
		return mysql_affected_rows();
	}

  /***
   * Database query
   **/
	function query($query, $isDirty=True)
	{
    // Begin timing
		$startTime = microtime_float();

    // Deal with dirty queries
		if ($isDirty) {
			$this->dirty = True;
      $this->transactionQueries[] = $query;
		}

    // Run query and deal with errors
		$res = mysql_query($query, $this->mysql);
		if ($error = mysql_error($this->mysql)) {
      $errno = mysql_errno($this->mysql);

      if ($errno == 1062) {
        throw new DuplicateException($error, array('query'=>$query), $errno);
      }elseif ($errno == 1205) {
        throw new LockTimeoutException($error, array('query'=>$query), $errno);
      }elseif ($errno == 1213) {
        throw new DeadlockException($error, array('query'=>$query), $errno);
      }else{
        throw new Exception($error, array('query'=>$query), $errno);
      }
    }else if (!$res) {
      throw new Exception('mysql_query returned false value, without an error message.');
    }

    // We're done timing
    $timeTaken = microtime_float()-$startTime;

    // Send out useful log information
    foreach ($this->cbLogQuery as $cb) {
      $cb(array(
        'query' => $query,
        'time' => $timeTaken
      ));
    }

    // We're done
		return $res;
	}


	// Check if an entry exists in a table
	function exists($table,$where='') {
    return $this->getSingle('EXISTS (SELECT * FROM '.$table.\SQL::where($where).')') ? True : False;
	}

  /***
   * Take full queries without helpers for select/insert/update
   **/
	function querySelect($query)
	{
		$res = $this->query($query, False);
		if (!$res) return NULL;
		else return new Results($res);
	}

  function queryInsert($query) {
    if (!$this->query($query)) {
      return NULL;
    }

    $id = mysql_insert_id();
    if ($id === 0) return True;
    else if ($id) return $id;
    else return NULL;
  }

  function queryUpdate($query) {
    if (!$this->query($query)) {
      return NULL;
    }

		return mysql_affected_rows();
  }

  function queryDelete($query) {
    if (!$this->query($query)) {
      return NULL;
    }

		return mysql_affected_rows();
  }

  /**
   * Construct a  SELECT query on SnapBill database
   * @param string $fields Fields to SELECT
   * @param string $from Tables to select FROM
   * @param string $where WHERE clause
   * @param string $orderby column name and ASC or DESC
   * @param int $limit LIMIT number
   * @return result 
   * @example
   * $resultset = $mysql->select("id, date", "invoice", "id=client->id", "date ASC");
   */
	function select($fields, $from=NULL, $where=NULL, $orderby=NULL, $limit=NULL) {
    $query  = 'SELECT '.($this->disableCache?'SQL_NO_CACHE ':'');
		$query .= $fields.\SQL::from($from).\SQL::where($where);
    $query .= ($orderby ? ' ORDER BY '.$orderby : '');
    $query .= ($limit   ? ' LIMIT '.$limit : '');
		return $this->querySelect($query);
	}

  /***
   * Execute a  SELECT query with support for paging
   * @param string $query The actual query to run
   * @param array &$paging Reference to array created by build_paging
   * @param bool $optimise Use an SQL_CALC_FOUND_ROWS optimisation rather than two queries (see http://bugs.mysql.com/bug.php?id=18454)
   **/
	function selectPaged($query, &$paging, $optimise=True) {
		if ($paging === False) {
			return $this->select($query);
		}
		$page = max(1, ARR($paging,'page'));
		$perpage = ARR($paging,'perpage',25);

    $lock = \SQL::removeLock($query);

    if ($optimise) {
      $res = $this->select('SQL_CALC_FOUND_ROWS '.$query.' LIMIT '.(($page-1)*$perpage).','.$perpage.$lock);
      $paging['total'] = $this->getSingle('FOUND_ROWS()');
    }else{
      // Re-write the query stripping out original fields
      $from_position = stripos($query, ' from ');
      $paging['total'] = $this->getSingle('count(*)'.substr($query, $from_position).$lock);
      // Run actual query after we check for sane page number
      $res = NULL;
    }

		$paging['numpages'] = ceil($paging['total'] / $perpage);
		if ($paging['total'] && $page > $paging['numpages']) {
			// Chosen page too high, force re-run of query
			$page = $paging['numpages'];
      $res = NULL;
    }
    // If we need to actually query the data still
    if (!$res) {
			$res = $this->select($query.' LIMIT '.(($page-1)*$perpage).','.$perpage.$lock);
		}

		$paging['page'] = $page;
		return $res;
	}

  /***
   * Insert / update / delete shorthands
   **/
	function insert($table, $data)
	{
		$fields = '';
		$values = '';
		foreach ($data as $key => $value)
		{
			if ($fields != '') $fields .= ',';
			$fields .= '`'.$key.'`';

			if ($values != '') $values .= ',';
			$values .= SQL($value);
		}
    return $this->queryInsert('INSERT INTO `'.$table.'` ('.$fields.') VALUES ('.$values.')');
	}

	function update($table, $actions, $where=False) {
		if (!$where) throw new Exception('For database update; $where must be set');

		$action_sql = '';
		foreach ($actions as $key => $value) {
      $action_sql .= ($action_sql ? ', ' : '') . "`$key`=".SQL($value);
		}
		
		return $this->queryUpdate('UPDATE `'.$table.'` SET '.$action_sql.\SQL::where($where));
	}

	function delete($table, $where=False) {
    return $this->queryDelete('DELETE '.\SQL::from($table).\SQL::where($where));
	}
	
	function insertUpdate($table,$data,$keyData) {
		$keyfields = '';
		$keyvalues = '';
		$fields = '';
		$values = '';
		$actions = '';
		foreach ($keyData as $key => $value) {
      if ($keyfields != '') $keyfields .= ',';
      $keyfields .= '`'.$key.'`';
      if ($keyvalues != '') $keyvalues .= ',';
      $keyvalues .= SQL($value);
    }
		foreach ($data as $key => $value) {
      if ($fields != '') $fields .= ',';
      $fields .= '`'.$key.'`';
      if ($values != '') $values .= ',';
      $values .= SQL($value);

      if ($actions != '') $actions .= ',';
      $actions .= '`'.$key.'` = '.SQL($value);
		}
		$this->query('INSERT INTO '.$table.' ('.$keyfields.','.$fields.') VALUES('.$keyvalues.','.$values.') ON DUPLICATE KEY UPDATE '.$actions);

		return mysql_affected_rows();
	}

  /***
   * Some helper methods
   **/
  function selectAll($query, $from=NULL, $where=NULL, $orderby=NULL, $limit=NULL) {
    return $this->select($query, $from, $where, $orderby, $limit)->asArray();
  }
  function selectSingle($query, $from=NULL, $where=NULL, $orderby=NULL, $limit=NULL) {
    return $this->select($query, $from, $where, $orderby, $limit)->fetch();
  }
  function getSingle($query, $from=NULL, $where=NULL, $orderby=NULL, $limit=NULL) {
    return $this->select($query, $from, $where, $orderby, $limit)->get();
  }
  function fetchSingle($query, $from=NULL, $where=NULL, $orderby=NULL, $limit=NULL) {
    return $this->select($query, $from, $where, $orderby, $limit)->fetch();
  }
  function queryGetSingle($query) {
    return $this->querySelect($query)->get();
  }
  function queryFetchSingle($query) {
    return $this->querySelect($query)->fetch();
  }

	function count($table, $where=NULL) {
		return $this->getSingle('count(*)', $table, $where);
	}
}
