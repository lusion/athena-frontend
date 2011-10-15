<?php

class DataRow_DB extends DataRow {
  /**
   * @var array Grouped updates
   */
	private $stored_updates = array();
  /**
   * @var int Group updates together
   */
	private $stored_delay = 0;
  /**
   * @var bool Is this row currently locked
   */
  private $locked = False;

  /**
   * Instantiate a datarow for the given table
   */
  public function __construct($class, $primary) {
    $data = Connection::open('sql')->selectSingle('* FROM '.SQL::table($class).' WHERE id='.SQL($primary));
    parent::__construct($class, $data);
  }

  /**
   * Prevent updates to rows
   **/
  public function update($changes) {
    throw new Exception('Updates not available (read-only)');
  }
}

