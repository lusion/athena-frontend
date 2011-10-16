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

    $checkable = ARR($this->options, 'checkable');

    print '<table'.html_attributes(array(
      'id' => ARR($this->options,'id')
    )).'><thead><tr>';

    if ($checkable) {
      print '<th class="checkbox"><input class="select-all" type="checkbox" value="all" /></th>';
    }

    foreach ($this->headers as $wiki => $opt) {
      print '<th>'.HTML(ARR($opt, 'caption')).'</th>';
    }
    print '</tr></thead><tbody>';
  }

  function row($data, $rowopt=array()) {
    print '<tr'.html_attributes(array(
      'class' => array(ARR($rowopt, 'class'), $this->rownum & 1 ? 'alt' : NULL)
    )).' >';

    if ($name = ARR($this->options, 'checkable')) {
			print '<td class="checkbox"><input type="checkbox" name="'.HTML($name).'[]" value="'.HTML(ARR($rowopt, 'id')).'" /></td>';
    }

    foreach ($this->headers as $wiki => $opt) {
      print '<td>'.Wiki::html($wiki, $data).'</td>';
    }
    print '</tr>';

    $this->rownum++;
  }

  function footer() {
    print '</tbody></table>';
  }
}
