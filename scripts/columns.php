<?php
require '../init.php';

function usage() { die("{$argv[0]} [--write|--test] [filename]\n"); }

if (count($argv) < 2) usage();

if (ARR($argv,1) == '--write') {
  define('columns_mode', 'write');
}elseif (ARR($argv,1) == '--test') {
  define('columns_mode', 'test');
}else usage();


function inline_array($arr) {
  $code = 'array(';
  $fp = True;
  $indexed = 0;
  foreach ($arr as $k => $v) {
    if ($fp) $fp = False;
    else $code .= ', ';
    if ($k === $indexed++) {
      /* indexed, no need for a key */
    }else{
      $code .= "'$k' => ";
    }
    if (is_array($v)) {
      $code .= inline_array($v);
    }else{
      $code .= var_export($v, True);
    }
  }
  $code .= ")";
  return $code;
}

if (columns_mode == 'test') {
  print 'Checking columns: ';
}

$errors = array();
$SQL = Connection::open('sql');
foreach ($SQL->querySelect('SHOW TABLES') as $table) {
  if (!class_exists($table)) continue;

  if (!is_subclass_of($table, 'Object')) continue;

  if (columns_mode == 'write') {
    print '== '.$table." ==\n";
  }else print '.';

  if (!isset($table::$COLUMNS)) {
    if (columns_mode == 'write') {
      print " # No column variable was found\n";
    }else{
      $errors[] = "No column variable found for `$table`";
    }
    continue;
  }

  $columns = $table::$COLUMNS;
  $order = array();
  $found = array();

  foreach ($SQL->querySelect('SHOW COLUMNS FROM `'.$table.'`') as $row) {
    $k = $row['Field'];
    $found[$k] = True;
    if (!isset($columns[$k])) {
      $columns[$k] = array('default' => $row['Default']);
    }
    if ($k == 'id' || substr($k,-3) == '_id') {
      $columns[$k]['type'] = 'id';
    }
    if (starts_with($row['Type'], 'enum')) {
      if (!preg_match_all('/\'([^\']*)\'/', $row['Type'], $m)) {
        throw new Exception('Could not find options for enum');
      }
      $columns[$k]['options'] = array_values($m[1]);
    }
    $order[] = $k;
  }

  $columns = array_filter_key($columns, function($k) use ($found) {
    return isset($found[$k]);
  });

  // Sort the columns in the same order as the database
  uksort($columns, function($a, $b) use ($order) {
    return array_search($a, $order) - array_search($b, $order);
  });

  $code = "  static \$COLUMNS = array(\n";
  $f = True;
  foreach ($columns as $k => $p) {
    if ($f) $f = False;
    else $code .= ",\n";
    $code .= "    '$k' => ";
    
    $code .= inline_array($p);
  }
  $code .= "\n  );\n";
  
  if (!$path = Autoload::findPath($table)) {
    print " * Could not find file\n";
    continue;
  }

  $contents = file_get_contents($path);
  $replaced = preg_replace('/'."\n".'\s*static\s*\$COLUMNS[^;]*;'.".*\n".'/', "\n$code", $contents);

  if ($replaced != $contents) {
    if (columns_mode == 'write') {
      print " * Changes made\n";
      file_put_contents($path, $replaced);
    }elseif (columns_mode == 'test') {
      $errors[] = "`$table` does not match";
    }
  }
}

if ($errors) {
  print "\n".' * '.implode("\n * ", $errors)."\n";
  exit(1); 
}else{
  if (columns_mode == 'test') {
    print "\n";
  }
}

