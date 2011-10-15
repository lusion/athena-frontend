<?php

class Backtrace {

  private $backtrace = NULL;

  function __construct($backtrace) {
    $this->backtrace = $backtrace;

    // Shift off the call to fatal/error/assert_true
    while (count($this->backtrace) > 1 && ($this->backtrace[1]['function'] == 'fatal' || $this->backtrace[1]['function'] == 'error' || $this->backtrace[1]['function'] == 'assert_true')) {
      array_shift($this->backtrace);
    }
  }

  /***
   * Display the current backtrace (debug helper)
   **/
  static function display() {
    $backtrace = new Backtrace(debug_backtrace());
    $backtrace->render();
  }

  /***
   * Returns an array of lines from a file around a given backtrace entry
   **/
  function get_line_context($entry) {
    if (!isset($entry['file'])) return array();
    if (file_exists($entry['file'])) {
      $file = explode("\n",file_get_contents($entry['file']));
    }else{
      $file = array();
    }
    $context = array();
    $start = max(1, $entry['line']-2);

    // Correct the keys of the file
    array_unshift($file, '');

    // Return an array of the lines, with keys preserved
    return array_slice($file, $start, 5, True);
  }

  /***
   * Renders a given backtrace entry in HTML
   **/
  function build_entry_html($entry) {
    $html = '<div class="backtrace">';
    $html .= '<div class="file">'.HTML(ARR($entry,'file', 'none')).'</div>';

    $first = True;
    foreach ($this->get_line_context($entry) as $N => $line) {
      if ($first) {
        $html .= '<ol class="code" start="'.$N.'">'."\n";
        $first = False;
      }
      $html .= ' <li class="context-'.abs($N-$entry['line']).'">'.HTML($line).'</li>'."\n";
    }	
    if (!$first) print '</ol>'."\n";
    $html .= '</div>';

    return $html;
  }

  /***
   * Renders the backtrace as html
   **/
  function build_html() {
    $html = '';
    foreach ($this->backtrace as $entry) {
      $html .= $this->build_entry_html($entry);
    }
    return $html;
  }

  /***
   * Renders the backtrace according to the current layout style
   **/
  function render() {
    print $this->build_html();
  }

}

