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
 * @todo Fix everything, comment, add tests and cleanup.
 */
class TIter implements Iterator, ArrayAccess {
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
        $s = $this->autoescape ? htmlentities($this->value) : $this->value;
        $this->value = self::INITIAL;
        return (string)$s;
    }

    function to_s() { return $this->__toString(); }
    
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

    // Callable toString shortcut.
    function  __call($name, $args) {
        return $this->$name->__toString();
    }

    // Filters.

    function filter_reset() {
        $value = $this->value;
        $this->value = self::INITIAL;
        $path = $this->lookup_path_reset();
        return $value;
    }
    function emptyor($default) {
        $value = $this->filter_reset();
        $return = empty($value) ? $default : $value;
    }
    function length() {
        return strlen($this->filter_reset());
    }
    function striptags() {
        return strip_tags($this->filter_reset());
    }
    function safe() {
        return $this->filter_reset();
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
    function vd() {
        var_dump($this->value);
        return $this;
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

    function autoescape_on() { $this->autoescape = true; }
    function autoescape_off() { $this->autoescape = false; }
    function firstof() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (!empty($arg)) return $arg;
        }
        return '';
    }
    function now($format) { return date($format); }
    function widthratio($range_in, $range_out) {
        return round($this->filter_reset() / $range_in * $range_out);
    }
    function add($amount) { return $this->filter_reset() + $amount; }
    function addslashes() { return addslashes($this->filter_reset()); }
    function capfirst() { return ucfirst($this->filter_reset()); }
    function center($width) {
        return str_pad($this->filter_reset(), $width, " ", STR_PAD_BOTH);
    }
    function cut($str) { return str_replace($str, '', $this->filter_reset()); }
    function date($format) { return date($format, $this->filter_reset()) ;}
    function divisibleby($divisor) {
        return ($this->filter_reset() % $divisor) === 0;
    }
    function escape() { return htmlentities($this->filter_reset()); }
    /*function addslashes() { return $this->filter_reset(); }
    function addslashes() { return $this->filter_reset(); }
    function addslashes() { return $this->filter_reset(); }
    function addslashes() { return $this->filter_reset(); }*/
}