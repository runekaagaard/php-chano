<?php
// Errors on.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Exceptions.
class ReadOnlyError extends Exception {}
class NotImplementedError extends Exception {}

/**
 * An irator class that takes an array of arrays as an input and supplies
 * capabilities resembling the Django Template Language.
 *
 * @author Rune Kaagaard
 * @todo Fix everything, comment, write docs,  add (a lot more) tests and
 * cleanup.
 *
 * Implement the following filters:
 *    floatformat
 *    force_escape
 *    spaceless
 *    dictsort(reversed)
 *    escapejs
 *    force_escape
 *    get_digit
 *    iriencode
 *    join
 *    last
 *    linebreaks
 *    linebreaksbr
 *    linenumbers
 *    ljust
 *    lower
 *    pluralize
 *    random
 *    rjust
 *    safe
 *    safeseq?
 *    slice
 *    slugify
 *    stringformat
 *    time
 *    timesince
 *    timeuntil
 *    title
 *    truncatewords
 *    truncatewords_html
 *    unordered_list
 *    upper
 *    urlencode
 *    urlize
 *    urlizetrunc
 *    wordcount
 *    wordwrap
 *    yesno
 */
class DtlIter implements Iterator, ArrayAccess {
    const INITIAL = -9892895829385;

    public $count = 0;
    public $i = 0;
    public $items;
    public $current = self::INITIAL;
    public $v = self::INITIAL;
    
    public $lookup_path;
    public $lookups = array();
    public $previous_lookups = array();

    public $autoescape = True;
    
    function  __construct($items) {
        $this->items = $items;
        $this->count = count($items) - 1;
    }

    function  __toString() {
        $this->lookup_path_reset();
        $s = $this->out();
        $this->v = self::INITIAL;
        return (string)$s;
    }

    function out($escape = null) {
        if ($escape === null) $escape = $this->autoescape;
        
        $s = $this->autoescape 
            ? htmlspecialchars((string)$this->v, ENT_NOQUOTES, 'utf-8')
            : (string)$this->v;
        return (string)$s;
    }
    
    // Iterator.
    function rewind() { $this->i = 0; }
    function current() {
        $this->current = current($this->items);
        return $this;
    }
    function key() { return $this->i; }
    function next() {
        $this->lookup_next();
        $this->current = next($this->items);
        ++$this->i;
    }
    function valid() {
        return isset($this->items[$this->i]);
    }

    // Lookups
    function lookup_add($o) {
        $this->lookup_path .= $o;
        $this->lookups[$this->lookup_path] = $this->v;
    }

    function lookup_path_reset() {
        $path = $this->lookup_path;
        $this->lookup_path = '';
        return $path;
    }
    
    function lookup_next() {
        $this->previous_lookups = $this->lookups;
        $this->lookups = array();
        $this->lookup_path_reset();
    }

    // Array Access
    function offsetGet($o) {
        if ($o == '_') return $this->__toString();
        if ($this->v == self::INITIAL) $this->v = $this->current[$o];
        else $this->v = $this->v[$o];
        $this->lookup_add($o);
        return $this;
    }
    function offsetExists($offset) { throw new NotImplementedError; }
    function offsetSet($offset, $value) { throw new ReadOnlyError; }
    function offsetUnset($offset) { throw new ReadOnlyError; }

    // Object Access
    function  __get($name) { return $this->offsetGet($name); }

    // Filters.

    function filter_reset() {
        $value = $this->v;
        $this->v = self::INITIAL;
        $path = $this->lookup_path_reset();
        return $value;
    }

    function filter_apply($function) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $function($v);
        return $this;
    }

    // Filter flags.
    function autoescape_on() { $this->autoescape = true; }
    function autoescape_off() { $this->autoescape = false; }

    // Filter commands. Non-chainable.
    function emptyor($default) {
        $value = $this->filter_reset();
        return empty($value) ? $default : $value;
    }
    function isfirst() { return $this->i === 0; }
    function islast() { return $this->i === $this->count; }
    function haschanged() {
        $this->v = self::INITIAL;
        $path = $this->lookup_path_reset();
        return isset($this->previous_lookups[$path]) &&
            $this->previous_lookups[$path] != $this->lookups[$path];
    }
    function same() {
        $this->v = self::INITIAL;
        $path = $this->lookup_path_reset();
        return isset($this->previous_lookups[$path]) &&
            $this->previous_lookups[$path] == $this->lookups[$path];
    }
    function cycle() {
        static $cycles = array();
        $args = func_get_args();
        $key = implode('', $args);
        if (empty($cycles[$key])) {
            $cycles[$key] = array($args, 0, count($args)-1);
            return $cycles[$key][0][0];
        } else {
            $cycles[$key][1]++;
            if ($cycles[$key][1] > $cycles[$key][2]) $cycles[$key][1] = 0;
            return $cycles[$key][0][$cycles[$key][1]];
        }
    }

    function firstof() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (!empty($arg)) return $arg;
        }
        return '';
    }
    
    function safe() { return $this->out(false); }
    function divisibleby($divisor) {
        return ($this->filter_reset() % $divisor) === 0;
    }
    function escape() { return htmlentities($this->filter_reset()); }

    // Filter modifiers. Chainable, but does not care for the value. Works on
    // the base object too.
    function counter() {
        $this->v = $this->i + 1;
        return $this;
    }
    function counter0() {
        $this->v = $this->i;
        return $this;
    }
    function revcounter() {
        $this->v = $this->count - $this->i + 1;
        return $this;
    }
    function revcounter0() {
        $this->v = $this->count - $this->i;
        return $this;
    }
    
    // Filter modifiers. Chainable.
    function length() {
        return $this->filter_apply(function($v) {
            return strlen($v);
        });
    }
    function striptags() {
        return $this->filter_apply(function($v) {
            return strip_tags($v);
        });
    }
    
    function vd() { var_dump($this->v); return $this; }
    function now($format) {
        return $this->filter_apply(function($v) use ($format) {
            return date($format);
        });
    }
    function widthratio($range_in, $range_out) {
        return $this->filter_apply(function($v) use ($range_in, $range_out) {
            return round($v / $range_in * $range_out);
        });
    }
    function add($amount) { 
        return $this->filter_apply(function($v) use ($amount) {
            return $v + $amount;
        });
    }
    function addslashes() {
        return $this->filter_apply(function($v) {
            return addslashes($v);
        });
    }
    function capfirst() {
        return $this->filter_apply(function($v) {
            return ucfirst($v);
        });
    }
    function upper() {
        return $this->filter_apply(function($v) {
            return strtoupper($v);
        });
    }
    function center($width) {
        return $this->filter_apply(function($v) use($width) {
            return str_pad($v, $width, " ", STR_PAD_BOTH);
        });
    }
    function cut($str) {
        return $this->filter_apply(function($v) use ($str) {
            return str_replace($str, '', $v);
        });
    }
    function date($format) {
        return $this->filter_apply(function($v) use ($format) {
            return date($format, $v);
        });
    }
    function filesizeformat() {
        return $this->filter_apply(function($size) {
            if (empty($size) || !is_numeric($size)) return '0 bytes';
            $prefixes = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
            for ($i=0; round($size, 1) >= 1024 && $i<5; $size /= 1024, ++$i);
            if ($i==0) return "$size bytes";
            return sprintf('%01.1f %s', $size, $prefixes[$i]);
        });
    }
    function yesno($yes=null, $no=null, $maybe=null) {
        $choices = array(
            true => $yes ? $yes : 'yes',
            false => $no ? $no : 'no',
            null => $maybe ? $maybe : 'maybe',
        );
        return $this->filter_apply(function($v) use ($choices, $no, $maybe) {
            if ($v === null && $no && !$maybe) $v = False;
            if ($v !== null) $v = (bool)$v;
            return $choices[$v];
        });
    }
    function wordwrap($width) {
        return $this->filter_apply(function($v) use ($width) {
            return wordwrap($v, $width);
        });
    }
    function wordcount() {
        return $this->filter_apply(function($v) {
            return str_word_count($v, 0, '0123456789');
        });
    }
    function len() {
        return $this->filter_apply(function($v) {
            return strlen($v);
        });
    }
    function stringformat($format) {
        return $this->filter_apply(function($v) use($format) {
            return sprintf("%$format", $v);
        });
    }
    function escapejs() {
        $from = array("\\"   , "'"      , "\""     , ">"      , "<"      , "&"      , "="      , "-"      , ";"      , "\u2028" , "\u2029" , "\u0000" , "\u0001" , "\u0002" , "\u0003" , "\u0004" , "\u0005" , "\u0006" , "\u0007" , "\b"     , "\t"     , "\n"     , "\v"     , "\f"     , "\r", "\u000e", "\u000f", "\u0010", "\u0011", "\u0012", "\u0013", "\u0014", "\u0015", "\u0016", "\u0017", "\u0018", "\u0019", "\u001a", "\u001b", "\u001c", "\u001d", "\u001e", "\u001f");
        $to = array('\\u005C', '\\u0027', '\\u0022', '\\u003E', '\\u003C', '\\u0026', '\\u003D', '\\u002D', '\\u003B', '\\u2028', '\\u2029', '\\u0000', '\\u0001', '\\u0002', '\\u0003', '\\u0004', '\\u0005', '\\u0006', '\\u0007', '\\u0008', '\\u0009', '\\u000A', '\\u000B', '\\u000C', '\\u000D', '\\u000E', '\\u000F', '\\u0010', '\\u0011', '\\u0012', '\\u0013', '\\u0014', '\\u0015', '\\u0016', '\\u0017', '\\u0018', '\\u0019', '\\u001A', '\\u001B', '\\u001C', '\\u001D', '\\u001E', '\\u001F');
        return $this->filter_apply(function($v) use($from, $to) {
            return str_replace($from, $to, $v);
        });
    }
}