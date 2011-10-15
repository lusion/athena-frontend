<?php

namespace DB;

require_once dirname(__FILE__).'/results.php';
require_once dirname(__FILE__).'/connection.php';

class Exception extends \ContextException {}

/***
 * Simple names for database exceptions
 **/
class DuplicateException extends Exception {}
class LockTimeoutException extends Exception {}
class DeadlockException extends Exception {}

/***
 * Non-database exceptiotns
 **/
class DroppedTransactionException extends Exception {}
class UnrecognisedValueException extends Exception {}
class ConnectException extends Exception {}
class FormatException extends Exception {}

