<?php

class Session {
  static $COLUMNS = array(
    'id' => array('default' => NULL, 'type' => 'id'),
    'state' => array('default' => 'active', 'options' => array('active', 'expired', 'closed')),
    'session_hash' => array('default' => NULL),
    'created' => array('type' => 'date'),
    'username' => array('default' => ''),
    'user_id' => array('default' => NULL, 'type' => 'id'),
    'subdomain' => array('default' => NULL),
    'infinite' => array('default' => 0),
    'last_ping' => array('default' => NULL),
    'variables' => array('default' => array(), 'type' => 'serialized-array')
  );

  private static $current = NULL;
  private static $lastError = NULL;

  /**
   * Retrieves the current sesion object
   **/
  public static function current() {
    return self::$current;
  }

  /**
   * Retrieves last error from login with visitor sessions
   **/
  public static function getLastError() {
    return self::$lastError;
  }

  /**
   * Pings the session (extend the timeout)
   */
  public function ping() {
    // Update the last ping time and commit if its a new transaction
    $SQL = Connection::open('sql');
    $commit = !$SQL->isDirty();
    $this->update(array('last_ping' => Date::now()));
    if ($commit) $SQL->commit();
  }

  /**
   * Sends the session cookie to the browser
   */
  private function emitCookie() {
    // Work out cookie lifetime, one month for infinite cookies
    if ($this->infinite) $lifetime = time() + DEF('session_infinite_timeout',3600*24*30);
    else $lifetime = 0;

    $shardId = Shard::toId(Connection::getShard());
    $hash = $shardId.'/'.$this->session_hash;

    // Set cookie 's' on secure (http-only), 'b' on insecure
		if (ssl || https) {
      setcookie('s', $hash, $lifetime, '/', $_SERVER['HTTP_HOST'], True, True);
    }else{
      setcookie('b', $hash, $lifetime, '/', $_SERVER['HTTP_HOST'], False, False);
    }
  }

  /**
   * Emits a blank cookie to remove old one
   */
  private static function deleteCookie() {
		if (ssl || https) {
      setcookie('s', '', 1, '/', $_SERVER['HTTP_HOST'], True, True);
    }else{
      setcookie('b', '', 1, '/', $_SERVER['HTTP_HOST'], False, False);
    }
  }

  /**
   * Reads the current session cookie from the browser
   * @return string Session hash or NULL if not set
   */
  public static function getCookie() {
    // Allow override of session_hash when not in production
    $hash = GET('session_hash', NULL);
    if (!$hash) {
      $hash = ARR($_COOKIE, (ssl || https) ? 's' : 'b');
    }
    if ($hash && 40 != strlen($hash)) {
      $shardHash = ARR($_COOKIE, (ssl || https) ? 's' : 'b');
      $pieces = explode('/', $shardHash);
      if (count($pieces) == 2) {
        list($shard, $hash) = explode('/', $shardHash);
        Connection::setShard(Shard::toName($shard));
      }else{
        $hash = array_shift($pieces);
      }
    }
    return $hash;
  }

  /**
   * Generates a new random session hash
   * @return string Session hash
   */
  public static function generateHash() {
    return sha1(mt_rand());
  }

  /**
   * Called at beginning of most pages
   * Create session for user if necessary and get credentials
   * @param function Function taking username/password which returns object or raises LoginException
   * @param boolean $visitor_sessions Whether guests should have sessions 
  * @return mixed 'expired', 'logout' or login() result 
   * @see login()
   */
  public static function start($login_function, $visitor_sessions = True) {
    $session = NULL;
    $expired = False;
    $emit = False;
    $ping = False;
    $redirect = NULL;
    self::$lastError = NULL;

    // Try load in the current session from cookie
    if ($session_hash = self::getCookie()) {
      self::$current = Session::searchSingle(array('subdomain'=>subdomain, 'session_hash'=>$session_hash, 'not_timed_out'=>True));
      if (!self::$current) {
        // Save expired as a possible return value
        self::$lastError = LoginException::EXPIRED;
        // Emit either a new session, or delete cookie
        $emit = True;
      } else {
        // Did we just request a logout?
        if (isset($_REQUEST['logout'])) {
          // Close the old session
          self::$current->update(array('state'=>'closed', 'variables' => array()));
          self::$current = NULL;
          // Save logout as a possible return value
          self::$lastError = LoginException::LOGOUT;
          // Will run a deleteSession if current is still null
          $emit = True;
        }else{
          $ping = True;
        }
      }
    }

    // Just start a new session if we need one, don't emit yet as we might change hash
    if ($visitor_sessions && !self::$current) {
      self::$current = self::add(array()); 
      $emit = True;
    }

    try {
      if ($username = POST('login_username')) {
        $password = POST('login_password','');

        // Ensure password is a string; $password=False overrides password check
        $password = strval($password);

        // Try login with the given username and password
        $result = $login_function($username, $password);

        // Successful login: change the hash (to prevent fixation)
        $session_parameters = array(
          'username'=>$username,
          'user_id'=>$result instanceof User ? $result->id : NULL,
          'infinite'=>POST('login_infinite')?1:0
        );
        if ($visitor_sessions) {
          self::$current->update($session_parameters);
        } else {
          self::$current = self::add($session_parameters);
        }
        self::$current->emitCookie();

        if ($return = REQ('return')) {
          // remove 'logout' token from url parameters
          redirect(preg_replace('/[?&]logout[^&]*/','',$return));
        }else{
          return $result;
        }
      }elseif (self::$current && self::$current->username) {

        // Just continue session, returned logged in user
        $result = $login_function(self::$current->username, False);
        self::$current->ping();
        return $result;
      }
    } catch (LoginException $e) {
      // Emit cookie before leaving if needed
      if ($emit) self::$current->emitCookie();
      if ($visitor_sessions) {
        self::$lastError = $e->getCode();
      }else{
        throw $e;
      }
    }

    if ($ping) {
      self::$current->ping();
    }
    if ($emit) {
      if (self::$current) {
        self::$current->emitCookie();
      }else{
        self::deleteCookie();
      }
    }

    if ($visitor_sessions) return NULL;
    elseif (self::$lastError) throw new LoginException(self::$lastError);
    else return NULL;
  }

  /***
   * Creates a new session as the given reseller, and emits a cookie
   **/
  static function switchTo($object) {
    if ($object instanceof User) {
      // Create the session
      self::$current = self::add(array(
        'username' => $object->username,
        'user_id'  => $object->id,
        'infinite' => self::$current ? self::$current->infinite : 0
      ));
    }elseif ($object instanceof Reseller) {
      // Check we are on the correct shard
      if ($object->shard != Connection::getShard()) {
        Connection::setShard($object->shard);
      }

      // Create the session
      self::$current = self::add(array(
        'username' => $object->username,
        'user_id'  => DEF('user_id'),
        'infinite' => self::$current ? self::$current->infinite : 0,
      ));
    }else{
      throw new FatalException('Can only switch session to user/reseller');
    }

    // Emit the cookie
    self::$current->emitCookie();
  }

	function update($changes, $record = True, $force = False) { return parent::update($changes, False, $force); }

  /**
   * 
   */
  public static function buildSearch($search) {
    // basic search object
    $search = new Search('session', $search);
    // search expects 'reseller' parameter to be used
    $search->param('reseller');

    if ($v = $search->param('session_hash')) $search->eq('session_hash', $v);
    if ($v = $search->param('subdomain')) $search->eq('subdomain', $v);

    if ($state = $search->param('state', 'active')) {
      $search->state('state', $state);
    }
    if ($search->param('not_timed_out')) {
      $search->and_sql('(infinite=1 OR last_ping >='.Date::now()->mysql.' - INTERVAL 15 MINUTE)');
    }
    return $search;
  }
  /**
   *
   * Record forced off 
   */ 
  public static function add($data=NULL, $reseller=NULL, $record=False) {
    // Generate a hash for this session
    $data['session_hash'] = static::generateHash();
    $data['last_ping'] = Date::now();
    $data['subdomain'] = subdomain;

    $session = parent::add($data, $reseller, False);
    return $session;
  }

  /**
   * Add a key/value pair to the TEXT PHP serialised field
   */
  public static function append($key, $value) {
    $session = static::$current;
    if (!$session) {
      $session = self::startVisitor();
    }
    $session->update(array("variables"=>array_merge(
      $session->variables,
      array($key=>$value)
    )));
  }

  /**
   * @param mixed $key
   * @return mixed Value corresponding to key, stored in TEXT column
   */
  public static function get($key) {
    $session = static::$current;
    if (!$session) return NULL;
    return $session->variables[$key];
  }
  /**
   * Remove key=>value pair from TEXT column for session
   * @param mixed $key
   */
  public static function remove($key) {
    $session = static::$current;
    if ($session) {
      $variables = $session->variables;
      unset($variables[$key]);
      $session->update(array('variables' => $variables));
    }
  }

  public static function has($key) {
    $session = static::$current;
    if (!$session) return False;
    return isset($session->variables[$key]);
  }
}

