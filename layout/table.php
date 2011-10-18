<?php

class Table {
	var $checkbox = 0;
	var $functions = array();
	var $headers = array();
	var $rownum = 0;
	var $options = array();
	var $rows = array();

	function __construct($headers, $options=array()) {
    $this->addHeaders($headers);
    $this->setOptions($options);
	}

  function setOptions($options) {
    $this->options = array_merge($this->options, $options);
  }

  function addHeaders($headers) {
		foreach ($headers as $name => $data) {
			$header = array();
			if (is_array($data)) {
				foreach ($data as $key=>$value) {
					if (is_numeric($key)) {
						$header['caption'] = $value;
					}else{
						$header[$key] = $value;
					}
				}
			}else{
				$header['caption'] = $data;
			}
			$this->headers[$name] = $header;
		}
  }

	function header() {
    $classes = array('data');
    if ($class = ARR($this->options,'class')) {
      $classes[] = $class;
    }
    if ($this->sortable = ARR($this->options,'sortable')) {
      $classes[] = 'sortable';
    }
    if ($this->checkable = ARR($this->options,'checkable')) {
      $classes[] = 'checkable';
    }

    print '<table'.html_attributes(array(
      'id' => ARR($this->options,'id'),
      'class' => $this->checkable ? 'checkable' : NULL
    )).'><thead><tr>';

    if ($this->checkable) {
      print '<th class="checkbox"><input class="select-all" type="checkbox" value="all" /></th>';
    }

    $column = 0;
    foreach ($this->headers as $wiki => $opt) {
      print '<th'.html_attributes(array(
        'class' => $column==0 ? 'first' : NULL
      )).'>'.HTML(ARR($opt, 'caption')).'</th>';
      $column++;
    }
    print '</tr></thead><tbody>';
  }

  function row($data, $rowopt=array()) {
    print '<tr'.html_attributes(array(
      'class' => array(ARR($rowopt, 'class'), $this->rownum & 1 ? 'alt' : NULL)
    )).' >';

    if ($this->checkable) {
			print '<td class="checkbox"><input type="checkbox" name="id[]" value="'.HTML(ARR($rowopt, 'id')).'" /></td>';
    }

    $column = 0;
    foreach ($this->headers as $wiki => $opt) {
      print '<td'.html_attributes(array(
        'class' => $column==0 ? 'first' : NULL
      )).'>'.Wiki::html($wiki, $data).'</td>';
      $column++;
    }
    print '</tr>';

    $this->rownum++;
  }

  function footer() {
    print '</tbody></table>';
  }
}
