<?php

class SQLCondition {
  private $conditions = array();
  private $join;
  private $negative;


  function __construct($options=array()) {
    $this->join = ARR($options, 'join', 'AND');
    $this->negative = ARR($options, 'negative', False);
  }

  function push($sql) {
    $this->conditions[] = $sql;
  }

  function buildSQL() {
    $sql = '';
    if (!$this->conditions) {
      // Automatically evaluate to true
      $sql = '1';
    }else{
      foreach ($this->conditions as $item) {
        // Reduce "AND 0" => 0, "OR 1" => 1, etc
        if ($item === '0') {
          if ($this->join == 'AND') {
            $sql = '0'; break;
          }elseif ($this->join == 'OR') {
            continue;
          }
        }elseif ($item === '1') {
          if ($this->join == 'OR') {
            $sql = '1'; break;
          }elseif ($this->join == 'AND') {
            continue;
          }
        }

        $sql .= ($sql ? ' '.$this->join.' ' : '') . 
                ($item instanceof SQL_Condition ? $item->buildSQL() : $item);
      }
    }

    if ($this->negative) return "NOT ($sql)";
    else return "($sql)";
  }
}


