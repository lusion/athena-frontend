<?php

function dollars_match($match, &$assign, $html=False) {
	try {
    // Manual override $empty
    if ($match[2] == 'empty') $item = '';
    else $item = ARR($assign,$match[2]);

		if (!empty($match[3])) {
      if ($html) $match[3] = UNHTML($match[3]);
			foreach (explode('->', substr($match[3],2)) as $child) {
				if ($child && is_object($item) && isset($item->$child)) {
					$item = $item->$child;
				}else $item = NULL;
			}
		}
		if (!empty($match[6])) { // array lookup
			if (is_array($item)) $item = ARR($item,$match[6]);
		}
		if (!empty($match[8])) { // format
      if ($html) $match[8] = UNHTML($match[8]);
      foreach (explode('|', $match[8]) as $format) {
        if (is_object($item) && method_exists($item, 'format')) $item = $item->format($format);
        else if ($format[0] == '?' && ($p = strpos($format, ':'))) {
          $yes = substr($format, 1, $p-1);
          $no = substr($format, $p+1);
          if ($item && $yes === '') {
            // ?: operator, just keep the item
          }else{
            $line = $item ? $yes : $no;
            if ($html) $line = UNHTML($line);
            $item = Wiki::dollars($item ? $yes : $no, $assign);
            if ($html) $item = HTML::load($item);
          }
        }else if ($format[0] == '>') {
          $fn = strtolower(substr($format,1));

          if (substr($fn, 0, 4) == 'css:') {
            // Doesn't work with preg_replace
            return new HTML('<span style="'.HTML(substr($fn, 4)).'">'.HTML($item).'</span>');
          }
          switch ($fn) {
          // transform abbreviations
          case 'l': case 'u': $fn = $fn=='l'?'lower':'upper';
          case 'lower': case 'upper': $fn = "strto$fn";
            /*fall through*/
          // standard functions
          case 'u2s': case 'strtoupper': case 'strtolower':
            $item = $fn($item); break;
          // transform to lower first
          case 'ucwords': case 'ucfirst':
            $item = $fn(strtolower($item)); break;
          case 'null':
            $item = NULL; break;
          case 'nbsp':
            return new HTML(nbsp(HTML($item)));
          case 'url':
            $item = URL($item); break;
          case 'empty':
            if ($item !== NULL) $item = ''; break;
          case 'status':
            return new HTML('<a class="status '.HTML($item).'" title="'.HTML(ucwords(u2s($item))).'"></a>'); break;
          case 'icon':
            return new HTML('<img style="vertical-align:middle" src="/ico/'.HTML($item).'" />');
          }
        }elseif ($item === NULL) {
          /* do nothing with sprintf formats on null items */
        }else{
          $format = Wiki::dollars($format, $assign);
          if ($format instanceof Currency) return $format->format($item);
          else $item = sprintf($format, $item);
        }
      }
		}
	} catch (Exception $e) { return NULL; }
	return $item;
}
function strong_match($match) {
	switch (strlen($match[1])) {
	case 1: return '<strong>'.$match[2].'</strong>';
	case 2: return '<em>'.$match[2].'</em>';
	case 3: return '<em><strong>'.$match[2].'</strong></em>';
	default: return $match[0];
	}
}

function stars_match($match) {
	return '<span style="color:red;">'.$match[1].'</span>';
}

function entity_match($match) {
  switch ($match[1]) {
  case 'laquo': case 'raquo':
  case 'mdash': case 'ndash':
  case 'nbsp': return '&'.$match[1].';';
  default: return $match[0];
  }
}

function link_match($match) {
	$href = $text = $match[1];
	// Accept different link text [[http://www.google.com|google.com]]
	if (strpos($href,'|') !== False) {
		list($href, $text) = explode('|', $href, 2);
	}

	// Use mailto if needed
	if (strpos($href,'@') !== False) $href = 'mailto:'.$href;

	return '<a href="'.($href).'">'.($text).'</a>';
}

class Wiki {
  const image_regex = '/<([a-z]*) src=["\']([a-zA-Z_\\/.?=#%-]*)["\']([^>]*)>/';
	const dollars_regex = '\$(([a-zA-Z][a-zA-Z0-9_]*)((->[a-zA-Z0-9_]*)*))(\\[([^\\]]*)\\])?(\(([^)]*)\))?';
	const dollars_html_regex = '\$(([a-zA-Z][a-zA-Z0-9_]*)((-&gt;[a-zA-Z0-9_]*)*))(\\[([^\\]]*)\\])?(\(([^)]*)\))?';
  const link_regex = '/\[\[(([^\]]|\][^\]])*)\]\]/';
  const entity_regex = '/&(#[0-9]+|[A-Za-z0-9]+);/';
  const entity_html_regex = '/&amp;(#[0-9]+|[A-Za-z0-9]+);/';

	static function dollars($line, &$assign) {
    // Temporary special case for single dollar (dont string'ify)
    if (preg_match('/^'.Wiki::dollars_regex.'$/', $line, $match)) {
      return dollars_match($match, $assign);
    }
		return preg_replace_callback('/'.Wiki::dollars_regex.'/', function($match) use (&$assign) { return dollars_match($match, $assign); }, $line);
	}
	static function dollars_valid($line, &$assign) {
		if (preg_match_all('/'.Wiki::dollars_regex.'/', $line, $matches)) {
			if ($matches) foreach (array_keys($matches[0]) as $k) {
				$match = array_map(function($m) use ($k) { return $m[$k]; }, $matches);
				if (dollars_match($match, $assign) === NULL) return False;
			}
		}
		return True;
	}

	static function dollars_html($line, &$assign) {
    // Temporary special case for single dollar (dont string'ify)
    if (preg_match('/^'.Wiki::dollars_html_regex.'$/', $line, $match)) {
      $value = dollars_match($match, $assign, True);
      return $value === NULL ? NULL : HTML($value, True);
    }
		return preg_replace_callback('/'.Wiki::dollars_html_regex.'/', function($match) use (&$assign) { return HTML(dollars_match($match, $assign, True), True); }, $line);
	}



	static function text($line, &$assign) {
		// Link 'removal'
		$line = preg_replace_callback(Wiki::link_regex,function($m) {
			// If its got a caption return that
			if (($p = strpos($m[1], '|')) !== False) return substr($m[1], $p+1);
			// Otherwise return the link
			return $m[1];
		},$line);
		$line = static::dollars($line, $assign);
		return $line;
	}

	static function html($line, &$assign) {
		$line = strval(HTML($line, True));
    $line = preg_replace_callback('/\[(\*+)\]/','stars_match',$line); // any number of stars in square brackets [**]
		$line = preg_replace_callback('/(\*+)([^*<]+?)\1/','strong_match',$line); // any number of stars, then text, then same number of stars **bold**
		$line = preg_replace_callback(Wiki::link_regex,'link_match',$line); // any text inside double square brackets [[link]]
		$line = preg_replace_callback(Wiki::entity_html_regex,'entity_match',$line); // any text inside double square brackets [[link]]
		$line = static::dollars_html($line, $assign);
		return $line;
	}

  /***
   * Search for { ... }, and cut it out if any variables inside do not exist
   **/
  static function wiki_clear($wiki, &$assign) {
    return preg_replace_callback("/{(.*?)}/ms",function ($match) use (&$assign) {
      $options = explode('|or|', $match[1]);
      foreach ($options as $option) {
        // Check variables in this match, if any are empty return ''
        if (Wiki::dollars_valid($option, $assign)) return $option;
      }
      return '';
    },$wiki);
  }

}

