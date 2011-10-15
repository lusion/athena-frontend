<?php
/**
 * Object instantiation and caching
 */
class ObjectFactory {
  /**
   * @var array $objects Cache of all objects of all classes.
   * Subkey is object table name.
   */
	private static $objects = array();

  /**
   * Remove objects from cache which are no longer referenced
   * Uses custom PHP module for reference counts
   */
	public static function garbage_collect() {
    // Log the fact that a garbage collection occurred
    Debug::log('ObjectFactory::garbage_collect()');

		gc_enable();
    $count = 0;
    // iterate through all cached objects
		$classes = array_keys(static::$objects);
    foreach ($classes as $class) {
      $keys = array_keys(static::$objects[$class]);
      foreach ($keys as $key) {
        // equivalent to xdebug_debug_xval() technique 
        if (snapbill_refcount(static::$objects[$class][$key]) == 2) {
          unset(static::$objects[$class][$key]);
          $count++;
        }
      }
    }
		gc_collect_cycles();
	}

  /**
   * Cache an object in memory, so that load() will check memory
   * first before loading from the database
   */
  public static function cache_object($object) {
    $table = $object->table;
    $primary = $object->primaryKey();

    if (!$primary) {
      throw new Exception("Cannot cache $table object with no primary");
    }

		if (isset(static::$objects[$table][$primary]) && static::$objects[$table][$primary] !== $object) {
      throw new FatalException("Attempting to cache different $table object with same primary ($primary)");
    }
		static::$objects[$table][$primary] = $object;
	}

  /**
   * Release the entire cache of objects
   * (does not remove objects referenced elsewhere)
   **/
  public static function purgeCache() {
    static::$objects = array();
  }

  /***
   * Fetch object from the cache
   **/
  private static function fetch_cached_object($table, $primary) {
    // Codes are always lower-case
    $primary = strtolower($primary);

		if (isset(static::$objects[$table][$primary])) {
      return static::$objects[$table][$primary];
    }else return NULL;
  }

  /**
   * Print reference count for each Object in cache.
   */
	public static function print_refcounts() {
    $classes = array_keys(static::$objects);
    foreach ($classes as $class) {
      $keys = array_keys(static::$objects[$class]);
      foreach ($keys as $key) {
        print "$class: $key: ".snapbill_refcount(static::$objects[$class][$key])."\n";
      }
    }
  }

  /***
   * Prints count of each type of object stored in cache
   **/
	static function print_usage() {
    foreach (self::$objects as $class=>$objects) {
      print "$class=".count($objects)."\n";
    }
	}
  
  /**
   * Spawn an object and load it into cache. If the object is already cached,
   * the cached object will be returned, with its properties updated to
   * reflect $data
   * @return Object
   * @throw CouldNotLoadException
   */
  public static function get($class, $primary, $data=null) {
    $class = strtolower($class); 

    if (!$object = static::fetch_cached_object($class, $primary)) {

      if ($data instanceof DataRow) {
        $row = $data;
      }else{
        $row = new DataRow_DB($class, $primary);
      }

      $object = new $class($row);
      static::cache_object($object);
		}

		return $object;
  }

}

