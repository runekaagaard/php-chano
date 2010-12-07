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
    public $value = self::INITIAL;
    
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
        $this->value = self::INITIAL;
        return (string)$s;
    }

    function out($escape = null) {
        if ($escape === null) $escape = $this->autoescape;
        $s = $this->autoescape ? htmlentities($this->value) : $this->value;
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
        $this->lookups[$this->lookup_path] = $this->value;
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
        if ($this->value == self::INITIAL) $this->value = $this->current[$o];
        else $this->value = $this->value[$o];
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
        $value = $this->value;
        $this->value = self::INITIAL;
        $path = $this->lookup_path_reset();
        return $value;
    }

    function filter_apply($function) {
        $this->value = $function($this->value);
        return $this;
    }

    // Filter flags.
    function autoescape_on() { $this->autoescape = true; }
    function autoescape_off() { $this->autoescape = false; }

    // Filter commands. Non-chainable.
    function emptyor($default) {
        $value = $this->filter_reset();
        $return = empty($value) ? $default : $value;
    }
    function isfirst() { return $this->i === 0; }
    function islast() { return $this->i === $this->count; }
    function haschanged() {
        $this->value = self::INITIAL;
        $path = $this->lookup_path_reset();
        return isset($this->previous_lookups[$path]) &&
            $this->previous_lookups[$path] != $this->lookups[$path];
    }
    function same() {
        $this->value = self::INITIAL;
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
    
    function vd() { var_dump($this->value); return $this; }
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
    ;}
    function counter() {
        $this->value = $this->i + 1;
        return $this;
    }
    function counter0() {
        $this->value = $this->i;
        return $this;
    }
    function revcounter() {
        $this->value = $this->count - $this->i + 1;
        return $this;
    }
    function revcounter0() {
        $this->value = $this->count - $this->i;
        return $this;
    }
    /**
     * Return human readable sizes
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.3.0
     * @link        http://aidanlister.com/2004/04/human-readable-file-sizes/
     * @param       int     $size        size in bytes
     * @param       string  $max         maximum unit
     * @param       string  $system      'si' for SI, 'bi' for binary prefixes
     * @param       string  $retstring   return string format
     */
    static function filesize_readable($size, $max = null, $system = 'bi',
    $retstring = '%01.1f %s') {
        // Pick units
        $systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
        $systems['si']['size']   = 1000;
        $systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
        $systems['bi']['size']   = 1024;
        $sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];
        // Max unit to display
        $depth = count($sys['prefix']) - 1;
        if ($max && false !== $d = array_search($max, $sys['prefix'])) {
            $depth = $d;
        }
        // Loop
        $i = 0;
        while ($size >= $sys['size'] && $i < $depth) {
            $size /= $sys['size'];
            $i++;
        }
        return sprintf($retstring, $size, $sys['prefix'][$i]);
    }
    function filesizeformat() {
        return $this->filter_apply(function($v) {
            return DtlIter::filesize_readable($v);
        });
    }
}