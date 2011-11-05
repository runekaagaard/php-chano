<?php

// Includes.
require realpath(dirname(__FILE__) . '/lib/text.php');

// Exceptions.
class Chano_ReadOnlyError extends Exception {}
class Chano_NotImplementedError extends Exception {}
class Chano_TypeNotTraversableError extends Exception {}
class Chano_TypeNotComplexError extends Exception {}
class Chano_ValueIsEmptyError extends Exception {}
class Chano_NoMatchingIteratorFoundError extends Exception {}

/**
 * An iterator that supplies capabilities matching the Django Template Language. 
 * Implements the full featureset (almost) found here:
 *   - http://docs.djangoproject.com/en/dev/ref/templates/builtins/.
 *
 * Is has github and readthedocs pages at:
 *   - https://github.com/runekaagaard/php-chano and
 *   - http://chano.readthedocs.org/
 * 
 * @author Rune Kaagaard <rumi.kg@gmail.com>
 * @todo
 *   - Create more iterators, i.e. for mysql and mysqli ressources.
 *
 * @codestyle
 *   In this project i've been experimenting with a non-pear code style. Some of
 *   those experiments entails:
 *     - One line functions.
 *     - Skipping brackets.
 *     - Having both an if/foreach and a statement on a single line.
 *     - Skipping default "public" keywords.
 *     - Using only "public" and "private", nothing inbetween.
 *     - Not documenting some helper and callback functions.
 */
class Chano implements Iterator, ArrayAccess {
    /**
     * The encoding used in charset sensitive filters.
     * 
     * @var string
     */
    static $encoding = 'utf-8';
    
    /**
     * An array of iterators for supported datatypes. See the 
     * register_iterator() method.
     * 
     * @var type array
     */
    static $iterators;

    /**
     * The value of the current item being operated on. When a lookup is
     * performed or a function is applied on the looked up item, this value
     * changes accordingly.
     *
     * @var mixed
     */
    private $_v = self::INITIAL;

    /**
     * A value used because PHP properties defaults to null, that makes it
     * possible to separate between the two.
     *
     * @var string
     */
    const INITIAL = '__CHANO_INITIAL__';

    /**
     * The matched iterator for the items given to the constructor.
     * 
     * @var <type>
     */
    private $_iterator;

    /**
     * The current 0-indexed count.
     *
     * @var int
     */
    private $_i = 0;

    /**
     * The current item in the iteration.
     *
     * @var mixed
     */
    private $_current = self::INITIAL;

    /**
     * A clone of the current item, if the deepcopy() functions has been called.
     * 
     * @var mixed
     */
    private $_current_clone = self::INITIAL;

    /**
     * A string representation of the various lookups the user performs on the
     * current item before hitting the __toString() method.
     * 
     * @var string
     */
    private $_lookup_path;

    /**
     * The actual values for the different lookup paths. Used to to check if the
     * value is changed() or the same() as the previous iteration.
     * 
     * @var array
     */
    private $_lookups = array();

    /**
     * The lookups for the previous iteration.
     * 
     * @var array
     */
    private $_previous_lookups = array();

    /**
     * If everytime the __toString() method is being called, its output should
     * be HTML escaped or not.
     * 
     * @var bool
     */
    private $_autoescape = true;

    /**
     * If the next time only the __toString() method, the global autoescape
     * value should be overridden, and with a value of true or false.
     * 
     * @var bool/null
     */
    private $_autoescape_single = null;

    /**
     * Sets the iterator for the items being iterated over.
     * 
     * @param mixed $items
     *   Accepts an array, object, iterator, etc. of arrays or objects. The
     *   given value is responsible for being countable if any of the filters
     *   using that feature are to be used.
     *
     *   If the value null is given then no iterator is set, and the user is
     *   responsible for doing so.
     */
    function __construct($items) {
        if ($items !== null)
            $this->_set_matching_iterator($items);
    }

    /**
     * Returns the current value cast to a string, possibly HTML escaping it
     * first.
     *
     * @return string
     */
    function __toString() {
        $s = (string)$this->_reset_v();
        if ($this->_autoescape_single !== null)
            $do_escape = $this->_autoescape_single;
        else
            $do_escape = $this->_autoescape;
        return $do_escape ? $this->_escape($s) : $s;
    }

    /**
     * HTML escapes given string, using the set encoding.
     * 
     * @param string $s
     * @return string
     */
    private function _escape($s) {
        return htmlspecialchars((string)$s, ENT_NOQUOTES, self::$encoding);
    }

    /**
     * Yes, this actually seems to be the fastest way to make a deep copy of an
     * object or an array in PHP. Scary! Used by the deepcopy() function.
     * 
     * @param array/object $var
     * @return array/object
     */
    private function _clone($var) {
        return unserialize(serialize($var));
    }
    
    /**
     * Resets and returns current value. Resets lookups.
     *
     * @return mixed
     */
    private function _reset_v($deepcopy=false) {
        $value = $this->_v;
        unset($this->_v);
        $this->_v = self::INITIAL;
        $this->_lookup_path_reset();
        $this->_reset_current_from_clone();
        return $value;
    }
    
    /**
     * Some functions, like length() and pluralize() should work directly on the
     * base instance too, i.e. outside of a foreach loop. Returns the main
     * iterator if the v property has not been set, else the v property.
     * 
     * @return mixed
     */
    private function _get_v_or_iterator() {
        if ($this->_v === self::INITIAL) return $this->_iterator;
        else return $this->_v;
    }

    /**
     * Resets escape settings and current value. Used by functions that returns
     * an actual value, and not the Chano instance.
     *
     * @return mixed
     */
    private function _reset_filter() {
        $this->_autoescape_single = null;
        return $this->_reset_v();
    }

    /**
     * Register an iterator for a new data type that Chano should be able to
     * handle.
     *
     * @param string $class
     *   The name of the iterator class.
     */
    static function register_iterator($class) {
        self::$iterators[] = $class;
    }

    /**
     * Tries the registered iterators by turn and stores the first matching one.
     * If no match is found it throws a Chano_NoMatchingIteratorFoundError.
     *
     * @param mixed $items
     * @return bool
     */
    private function _set_matching_iterator($items) {
        foreach (self::$iterators as $iterator) {
            if (call_user_func(array($iterator, 'is_match'), $items)) {
                $this->_iterator = call_user_func(
                    array($iterator, 'get_instance'), $items);
                return true;
            }
        }
        throw new Chano_NoMatchingIteratorFoundError;
    }

    /**
     * Implementation of Iterator interface.
     */
    function rewind() { $this->_iterator->rewind(); }
    function current() {
        $this->_set_current($this->_iterator->current());
        return $this;
    }
    function key() { return $this->_iterator->key(); }
    function next() {
        $this->_lookup_next();
        $this->_set_current($this->_iterator->next());
        ++$this->_i;
    }
    function valid() {
        return $this->_iterator->valid();
    }

    /*
     * Handles lookups ($i->key1->key2->etc->_) so it can be compared to lookups
     * for previous item.
     */

    /**
     * Adds a lookup.
     *
     * @param string $o
     */
    private function _lookup_add($o) {
        $this->_lookup_path .= $o;
        $this->_lookups[$this->_lookup_path] = $this->_v;
    }

    /**
     * Resets and returns the current lookup path.
     *
     * @return string
     */
    private function _lookup_path_reset() {
        $path = $this->_lookup_path;
        $this->_lookup_path = '';
        return $path;
    }

    /**
     * Stores current lookups as previous and resets lookups.
     */
    private function _lookup_next() {
        $this->_previous_lookups = $this->_lookups;
        $this->_lookups = array();
        $this->_lookup_path_reset();
    }

    /*
     * Implementation of ArrayAcces interface.
     */

    /**
     * Handles a lookup on the current item.
     *
     * @param string $o
     * @return Chano instance
     */
    function offsetGet($o) {
        if ($o == '_') return $this->__toString();
        if ($o == 'v') return $this->_reset_v();

        if ($this->_v === self::INITIAL) $v = $this->_current;
        else $v = &$this->_v;
        
        if (is_object($v)) $this->_v = &$v->$o;
        elseif (is_array($v)) $this->_v = &$v[$o];
        elseif (is_scalar($v)) $this->_v = &$v;

        $this->_lookup_add($o);
        return $this;
    }
    function offsetExists($offset) { throw new Chano_NotImplementedError; }
    function offsetSet($offset, $value) { throw new Chano_ReadOnlyError; }
    function offsetUnset($offset) { throw new Chano_ReadOnlyError; }

    /**
     * Implementation of magic methods.
     */

    /**
     * Handles a lookup on the current item.
     *
     * @param string $o
     * @return Chano instance
     */
    function  __get($name) { return $this->offsetGet($name); }

    /**
     * Functions not existing on Chano is passed on to the current item and
     * used to modify the current value.
     * 
     * @param string $name
     * @param array $args
     * @return mixed
     */
    function __call($name, $args) {
        $this->_v = call_user_func_array(array($this->_current, $name), $args);
        return $this;
    }
    
    /**
     * Sets the current item and resets the clone. Not part of the public API,
     * even though it is public.
     * 
     * @param mixed $current 
     */
    function _set_current($current) {
        $this->_current = &$current;
        unset($this->_current_clone);
        $this->_current_clone = self::INITIAL;
    }

    /**
     * Resets the current item from the clone if it exists.
     */
    private function _reset_current_from_clone() {
        if ($this->_current_clone !== self::INITIAL) {
            unset($this->_current);
            if (is_object($this->_current_clone))
                $this->_current = clone $this->_current_clone;
            else
                $this->_current = $this->_current_clone;
        }
    }
    
    /**
     * Makes Chano work with single values too, not just i.e. an array of
     * arrays.
     * 
     * @staticvar boolean $chano
     * @param mixed $value
     * @return Chano 
     */
    static function with($value=null) {
        static $chano = false;
        if (!$chano) $chano = new Chano(null);
        $chano->_set_current(array('value' => $value));
        $chano->__get('value');
        return $chano;
    }
    
    /*
     * Template inheritance.
     */

    /**
     * The name of the active block.
     * @var string
     */
    private static $active_block = null;
    
    /**
     * The content of active blocks with Chano::$active_block as keys.
     * @var array
     */
    private static $blocks = array();
    
    /**
     * A flag that tracks if we are inside and <?Chano::extend()?>
     * <?Chano::endextend()?> section or not.
     * @var bool
     */
    private static $inside_extend = false;
    
    /**
     * Will be replaced with the content of the parent block. 
     * @const string
     */
    const super = '__CHANO_SUPER__';
    
    /**
     * Begins an extend.
     */
    static function extend() {
        self::$inside_extend = true;
        ob_start();
    }

    /**
     * Ends an extend.
     */
    static function endextend() {
        self::$inside_extend = false;
        ob_end_clean();
    }
    
    /**
     * Begins a named block.
     * 
     * @param string $name 
     *   The name of the block.
     */
    static function block($name) {
        ob_start();
        self::$active_block = $name;
    }
    
    /**
     * Ends a block. If inside an <?Chano::extend()?><?Chano::endextend()?> 
     * section and the block has content, store that content for later use. If
     * not, check if the current block is extended, if it is output the extended
     * content, else output the content of the current block.
     */
    static function endblock() {
        $content = ob_get_clean();
        $has_content = trim($content) !== '';
        $is_extended = isset(self::$blocks[self::$active_block]);
        
        if (self::$inside_extend) {
            if (!$is_extended && $has_content)
                self::$blocks[self::$active_block] = $content;
        } else {
            if ($is_extended) {
                if ($has_content)
                    echo str_replace(self::super, $content, 
                                     self::$blocks[self::$active_block]);
                else
                    echo self::$blocks[self::$active_block];
                unset(self::$blocks[self::$active_block]);
            } else {
                if ($has_content) echo $content;
            }
        }
        self::$active_block = null;
    }
    
    /**
     * Wether the current block already has been extended. Can be used to avoid
     * running slow running code, if its content will not be used anyway.
     * 
     * @return bool
     */
    static function blockempty() {
        return !isset(self::$blocks[self::$active_block]);
    }

    ////////////////////////////////////////////////////////////////////////////
    // Below this line are the methods that are part of the template api.     //
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @section filter
     *   Filters
     *
     *   Modifies the value of the current item. All filters are chainable.
     */

    private function _clean_list($list) {
        $new_list = array();
        foreach ($list as $key => $item) {
            if (is_scalar($item)) $new_list[$key] = $item;
            elseif (!empty($item) && is_array($item))
                $new_list[$key] = $this->_clean_list($item);
        }
        return $new_list;
    }

    private function _unorderedlist($list=null, $indent=1) {
        $html = '';
        $ws = str_repeat("\t", $indent);
        $vs = array_values($list);
        $count = count($vs);
        for ($i=0; $i<$count; ++$i) {
            $item = $vs[$i];
            $next_item = isset($vs[$i+1]) ? $vs[$i+1] : false;
            if (is_scalar($item)) $html .= "$ws<li>$item";
            if (is_array($item)) $html .= $this->_unorderedlist($item, $indent);
            if (is_array($next_item) && !is_array($item)) {
                $html .=
                    "\n$ws<ul>\n"
                    . $this->_unorderedlist($next_item, $indent+1)
                    . "$ws</ul>\n$ws";
                ++$i;
            }
            if (is_scalar($item)) $html .= "</li>\n";
        }
        return $html;
    }

    /**
     * Recursively takes an array and returns an HTML unordered list - without
     * opening and closing <ul> tags.
     *
     * The array is assumed to be in the proper format. For example, if ``var``
     * contains::
     *
     *     array(
     *         'States', array(
     *             'Kansas', array(
     *                   'Lawrence', 'Topeka'
     *             ), 'Illinois'
     *         )
     *     )
     *
     * then ``<?=$item->var->unordered_list()?>`` will render::
     *
     *     <li>States
     *     <ul>
     *             <li>Kansas
     *             <ul>
     *                     <li>Lawrence</li>
     *                     <li>Topeka</li>
     *             </ul>
     *             </li>
     *             <li>Illinois</li>
     *     </ul>
     *     </li>
     *
     * @chanotype filter
     * @return Chano instance
     */
    function unorderedlist() {
        $this->_autoescape_single = false;
        $this->_v = $this->_unorderedlist($this->_clean_list($this->_v));
        return $this;
    }

    /**
     * Strips all [X]HTML tags.
     *
     * For example::
     *
     *     <?=$item->value->striptags()?>
     *
     * If ``$value`` is
     * ``"<b>Joel</b> <button>is</button> a <span>slug</span>"``, the output
     * will be ``"Joel is a slug"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function striptags() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = strip_tags($v);
        return $this;
    }

    /**
     * Displays the current date and/or time, using a format according to the
     * given string which can contain format specifiers characters as described
     * in the :ref:`date` filter section.
     *
     * Example::
     *
     *     Current time is: <?=$item->now("%B %e, %Y, %R %P")?>
     *
     * This will display as ``"Current time is: March 10, 2001, 5:16 pm"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function now($format) {
        $now = defined('CHANO_TESTS_NOWTIME') ? CHANO_TESTS_NOWTIME : time();
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = strftime($format, $now);
        return $this;
    }
    /**
     * For creating bar charts and such, this tag calculates the ratio of a
     * given value to a maximum value, and then applies that ratio to a
     * constant.
     *
     * For example::
     *
     *     <img src="bar.gif" height="10" width="<?=$item->value->widthratio(175, 100)?>" />
     *
     * Above, if ``value`` is 175 and, the image in the above example will be
     * 88 pixels wide
     * (because 175/200 = .875; .875 * 100 = 87.5 which is rounded up to 88).
     *
     * @chanotype filter
     * @param numeric $max_in
     *   The maximum before value.
     * @param numeric $max_out
     *   The maximum after value.
     * @return Chano instance
     */
    function widthratio($max_in, $max_out) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = round($v / $max_in * $max_out);
        return $this;
    }

    /**
     * Adds the given amount to the current value.
     *
     * If ``value`` is 2, then ``<?=$item->value->add(2)?>`` will render 4.
     *
     * @chanotype filter
     * @param numeric $amount
     * @return Chano instance
     */
    function add($amount) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v += $amount;
        return $this;
    }

    /**
     * Adds slashes before quotes. Useful for escaping strings in CSV, for
     * example.
     *
     * For example::
     *
     *     <?=$item->value->addslashes()?>
     *
     * If ``value`` is ``"I'm using Chano"``, the output will be
     * ``"I\'m using Chano"``
     * .
     * @chanotype filter
     * @return Chano instance
     */
    function addslashes() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = addslashes($v);
        return $this;
    }

    /**
     * Capitalizes the first character of the value.
     *
     * For example::
     *
     *     <?=$item->value->capfirst()?>
     *
     * If ``value`` is ``"chano"``, the output will be ``"Chano"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function capfirst() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        $e = self::$encoding;
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = mb_strtoupper(mb_substr($v, 0, 1, $e), $e)
                     . mb_substr($v, 1, mb_strlen($v, $e), $e);
        return $this;
    }

    /**
     * Converts a string into all uppercase.
     *
     * For example::
     *
     *     <?=$item->value->upper()?>
     *
     * If ``value`` is ``"Joel is a slug"``, the output will be
     * ``"JOEL IS A SLUG"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function upper() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = mb_strtoupper($v, self::$encoding);
        return $this;
    }

    /**
     * Version of $this->_mb_str_pad that works with all charsets.
     *
     * @param string $input
     * @param int $pad_length
     * @param string $pad_string
     * @param int $pad_type
     * @todo This workaround could have flaws. Make a better one.
     * @return string
     */
    function _mb_str_pad($input, $pad_length, $pad_string=' ',
    $pad_type=STR_PAD_RIGHT) {
        // Thanks to Kari "Haprog" Sderholm!
        // http://www.php.net/manual/en/function.str-pad.php#89754.
        $diff = strlen($input) - mb_strlen($input, self::$encoding);
        return str_pad($input, $pad_length+$diff, $pad_string, $pad_type);
    }
    /**
     * Centers the value in a field of a given width.
     *
     * For example::
     *
     *     <?=$item->value->center(15)?>
     *
     * If ``value`` is ``"Chano!"``, the output will be ``"     Chano!    "``.
     *
     * @param int $width
     * @chanotype filter
     * @return Chano instance
     */
    function center($width) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v) {
            if (!is_array($v) && !($v instanceof stdClass)) {
                $v = $this->_mb_str_pad($v, $width, " ", STR_PAD_BOTH);
            }
        }
        return $this;
    }

    /**
     * Left-aligns the value in a field of a given width.
     *
     * For example::
     *
     *     "<?=$item->value->ljust(10)?>"
     *
     * If value is ``Chano!``, the output will be ``"Chano!    "``.
     *
     * @param int $width
     * @chanotype filter
     * @return Chano instance
     */
    function ljust($width) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_mb_str_pad($v, $width, " ", STR_PAD_RIGHT);
        return $this;
    }

    /**
     * Right-aligns the value in a field of a given width.
     *
     * For example::
     *
     *     "<?=$item->value->rjust(10)?>"
     *
     * If value is ``Chano!``, the output will be ``"    Chano!"``.
     *
     * @param int $width
     * @chanotype filter
     * @return Chano instance
     */
    function rjust($width) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_mb_str_pad($v, $width, " ", STR_PAD_LEFT);
        return $this;
    }

    /**
     * Removes all values of passed argument from the current value.
     *
     * For example::
     *
     *     <?=$item->value->cut(" ")?>
     *
     * If ``value`` is ``"String with spaces"``, the output will be
     * ``"Stringwithspaces"``.
     *
     * @param string $string
     *   The string to remove.
     * @chanotype filter
     * @return Chano instance
     */
    function cut($string) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = str_replace($string, '', $v);
        return $this;
    }

    /**
     * Formats a date according to the given format.
     *
     * The format must be in a syntax supported by the
     * `strftime() <http://php.net/manual/en/function.strftime.php>`_ function.
     *
     * The used timezone is the one found by the
     * `date_default_timezone_get() <http://www.php.net/manual/en/function.date-default-timezone-get.php>`_
     * function.
     *
     * Uses the current locale as set by the `setlocale() <http://php.net/manual/en/function.setlocale.php>`_
     * function.
     *
     * The input value can be a digit, which will be interpreted as a linux
     * timestamp, a ``DateTime()`` class or a string
     * `recognized by <http://www.php.net/manual/en/datetime.formats.php>`_ the
     * `strtotime() <http://php.net/manual/en/function.strtotime.php>`_
     * class.
     *
     * For example::
     *
     *     <?=$item->value->date("%d %B %Y")?>
     *
     * If ``value`` is the string "2000-01-01", a DateTime object like
     * ``new DateTime("2000-01-01")`` or the linux timestamp integer 946684800,
     * the output will be the string ``'01 January 2000'``.
     *
     * @param string $format
     * @chanotype filter
     * @return Chano instance
     */
    function date($format) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                if ($v instanceof DateTime)
                    $v = strftime($format, $v->format('U'));
                elseif (ctype_digit((string)$v))
                    $v = strftime($format, (int)$v);
                else
                    $v = strftime($format, strtotime($v));
        return $this;
    }

    private function _filesizeformat($size) {
        static $prefixes = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
        if (empty($size) || !is_numeric($size)) return "0 $prefixes[0]";
        for ($i=0; round($size, 1) >= 1024 && $i<5; $size /= 1024, ++$i);
        if ($i==0) return "$size $prefixes[0]";
        return sprintf('%01.1f %s', $size, $prefixes[$i]);
    }

    /**
     * Format the value like a 'human-readable' file size (i.e. ``'13 KB'``,
     * ``'4.1 MB'``, ``'102 bytes'``, etc).
     *
     * For example::
     *
     *     <?=$item->value->filesizeformat()?>
     *
     * If ``value`` is 123456789, the output will be ``117.7 MB``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function filesizeformat() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_filesizeformat($v);
        return $this;
    }

    /**
     * Given a string mapping values for true, false and (optionally) null,
     * returns one of those strings according to the value:
     *
     * For example::
     *
     *     <?=$item->value->yesno("yeah", "no", "maybe")?>
     *
     * ==========  ===========================  ==================================
     * Value       Arguments                    Outputs
     * ==========  ===========================  ==================================
     * ``true``    ``("yeah", "no", "maybe")``  ``yeah``
     * ``false``   ``("yeah", "no", "maybe")``  ``no``
     * ``null``    ``("yeah", "no", "maybe")``  ``maybe``
     * ``null``    ``("yeah", "no")``           ``"no"`` (converts null to false
     *                                          if no mapping for null is given)
     * ==========  ===========================  ==================================
     *
     * @param string $yes
     * @param string $no
     * @param string $maybe
     * @chanotype filter
     * @return Chano instance
     */
    function yesno($yes='', $no='', $maybe='') {
        $choices = array(
            true => $yes ? $yes : 'yes',
            false => $no ? $no : 'no',
            null => $maybe ? $maybe : 'maybe',
        );
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v) {
            if (!is_array($v) && !($v instanceof stdClass))  {
                if ($v === null && $no && !$maybe) $v = False;
                if ($v !== null) $v = (bool)$v;
                $v = $choices[$v];
            }
        }
        return $this;
    }

    /**
     * Wraps words at specified line length.
     *
     * For example::
     *
     *     <?=$item->value->wordwrap(5)?>
     *
     * If ``value`` is ``Joel is a slug``, the output will be::
     *
     *     Joel
     *     is a
     *     slug
     *
     * @param int $width
     *   Number of characters at which to wrap the text.
     * @chanotype filter
     * @return Chano instance
     */
    function wordwrap($width) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = wordwrap($v, $width, "\n", true);
        return $this;
    }

    /**
     * Returns the number of words.
     *
     * For example::
     *
     *     <?=$item->value->wordcount()?>
     *
     * If ``value`` is ``"Joel is a slug"``, the output will be ``4``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function wordcount() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = str_word_count($v, 0, '0123456789');
        return $this;
    }

    /**
     * Formats the variable according to the argument, a string formatting
     * specifier. This specifier uses the syntax of the
     * `sprintf <http://php.net/manual/en/function.sprintf.php>`_ function.
     *
     * For example::
     *
     *     <?=$item->value->stringformat:("%03d")?>
     *
     * If ``value`` is ``1``, the output will be ``"001"``.
     *
     * @param string $format
     * @chanotype filter
     * @return Chano instance
     */
    function stringformat($format) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = sprintf("$format", $v);
        return $this;
    }

    /**
     * Escapes characters for use in JavaScript strings. This does *not* make
     * the string safe for use in HTML, but does protect you from syntax errors
     * when using templates to generate JavaScript/JSON.
     *
     * For example::
     *
     *     <?=$item->value->escapejs()?>
     *
     * If ``value`` is ``"testing\r\njavascript \'string" <b>escaping</b>"``,
     * the output will be
     * ``"testing\\u000D\\u000Ajavascript \\u0027string\\u0022 \\u003Cb\\u003Eescaping\\u003C/b\\u003E"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function escapejs() {
        // Thanks Heine!: http://drupal.org/node/479368#pift-results-479368-3198
        // 886-3198886.
        static $replace_pairs = array('\\' => '\u005C', '"' => '\u0022',
            "\x00" => '\u0000', "\x01" => '\u0001', "\x02" => '\u0002',
            "\x03" => '\u0003', "\x04" => '\u0004', "\x05" => '\u0005',
            "\x06" => '\u0006', "\x07" => '\u0007', "\x08" => '\u0008',
            "\x09" => '\u0009', "\x0a" => '\u000A', "\x0b" => '\u000B',
            "\x0c" => '\u000C', "\x0d" => '\u000D', "\x0e" => '\u000E',
            "\x0f" => '\u000F', "\x10" => '\u0010', "\x11" => '\u0011',
            "\x12" => '\u0012', "\x13" => '\u0013', "\x14" => '\u0014',
            "\x15" => '\u0015', "\x16" => '\u0016', "\x17" => '\u0017',
            "\x18" => '\u0018', "\x19" => '\u0019', "\x1a" => '\u001A',
            "\x1b" => '\u001B', "\x1c" => '\u001C', "\x1d" => '\u001D',
            "\x1e" => '\u001E', "\x1f" => '\u001F', "'" => '\u0027',
            '<' => '\u003C', '>' => '\u003E', '&' => '\u0026',
            '/' => '\u002F', "\xe2\x80\xa8" => '\u2028',
            "\xe2\x80\xa9" => '\u2029',);
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = strtr($v, $replace_pairs);
        return $this;
    }

    /**
     * Outputs the first item in an array, stdClass or Traversable.
     *
     * For example::
     *
     *     <?=$item->value->first()?>
     *
     * If ``value`` is the array ``array('a', 'b', 'c')``, the output will be
     * ``'a'``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function first() {
        if (is_array($this->_v)) {
            if (empty($this->_v)) throw new Chano_ValueIsEmptyError;
            reset($this->_v);
            $this->_v = current($this->_v);
        } elseif ($this->_v instanceof stdClass
        || $this->_v instanceof Traversable) {
            $has_value = false;
            foreach ($this->_v as $v) {
                $has_value = true;
                $this->_v = $v;
                break;
            }
            if (!$has_value) throw new Chano_ValueIsEmptyError;
        } else {
            throw new Chano_TypeNotTraversableError;
        }
        return $this;
    }

    /**
     * Replaces ampersands with ``&amp;`` entities.
     * 
     * This is rarely useful as ampersands are automatically escaped. See 
     * :ref:`escape` for more information.
     *
     * For example::
     *
     *     <?=$item->value->fixampersands()?>
     *
     * If ``value`` is ``Tom & Jerry``, the output will be ``Tom &amp; Jerry``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function fixampersands() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = strtr($v, '&', '&amp;');
        return $this;
    }

    function _floatformat($v, $ds) {
        if (!is_numeric($v)) return '';
        if (!is_numeric($ds)) $ds = '-1';
        $ds = (string)$ds;
        $hide_zeros = true;
        if ($ds) {
            if ($ds[0] == '-') $ds = ltrim($ds, '-');
            else $hide_zeros = False;
        }
        if ($hide_zeros && (int)$v == $v) return $v;
        $ds = $ds ? $ds : 1;
        return sprintf("%.{$ds}f", round($v, $ds));
    }

    /**
     * When used without an argument, rounds a floating-point number to one
     * decimal place -- but only if there's a decimal part to be displayed.
     * For example:
     *
     * ============  ====================================  ========
     * ``value``     Template                              Output
     * ============  ====================================  ========
     * ``34.23234``  ``<?=$item->value->floatformat()?>``  ``34.2``
     * ``34.00000``  ``<?=$item->value->floatformat()?>``  ``34``
     * ``34.26000``  ``<?=$item->value->floatformat()?>``  ``34.3``
     * ============  ====================================  ========
     *
     * If used with a numeric integer argument, ``floatformat`` rounds a number
     * to that many decimal places. For example:
     *
     * ============  =====================================  ==========
     * ``value``     Template                               Output
     * ============  =====================================  ==========
     * ``34.23234``  ``<?=$item->value->floatformat(3)?>``  ``34.232``
     * ``34.00000``  ``<?=$item->value->floatformat(3)?>``  ``34.000``
     * ``34.26000``  ``<?=$item->value->floatformat(3)?>``  ``34.260``
     * ============  =====================================  ==========
     *
     * If the argument passed to ``floatformat`` is negative, it will round a
     * number to that many decimal places -- but only if there's a decimal part
     * to be displayed. For example:
     *
     * ============  ======================================  ==========
     * ``value``     Template                                Output
     * ============  ======================================  ==========
     * ``34.23234``  ``<?=$item->value->floatformat(-3)?>``  ``34.232``
     * ``34.00000``  ``<?=$item->value->floatformat(-3)?>``  ``34``
     * ``34.26000``  ``<?=$item->value->floatformat(-3)?>``  ``34.260``
     * ============  ======================================  ==========
     *
     * Using ``floatformat`` with no argument is equivalent to using
     * ``floatformat`` with an argument of ``-1``.
     *
     * @param string $format
     * @chanotype filter
     * @return Chano instance
     */
    function floatformat($decimal_places=null) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_floatformat($v, $decimal_places);
        return $this;
    }

    function _getdigit($v, $n) {
        if (!intval($v) || !intval($n)) return $v;
        $v_s = (string)$v;
        if (!isset($v_s[$n-1])) return $v;
        else return $v_s[$n-1];
    }

    /**
     * Given a whole number, returns the requested digit, where 1 is the
     * right-most digit, 2 is the second-right-most digit, etc. Returns the
     * original value for invalid input (if input or argument is not an integer,
     * or if argument is less than 1). Otherwise, output is always an integer.
     *
     * For example::
     *
     *     <?=$item->value->get_digit(2)?>
     *
     * If ``value`` is ``123456789``, the output will be ``8``.
     *
     * @param int $number
     * @chanotype filter
     * @return Chano instance
     */
    function getdigit($number) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_getdigit($v, $number);
        return $this;
    }

    /**
     * Converts a string into all lowercase.
     *
     * For example::
     *
     *     <?=$item->value->lower()?>
     *
     * If ``value`` is ``Still MAD At Yoko``, the output will be
     * ``still mad at yoko``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function lower() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = mb_strtolower($v, self::$encoding);
        return $this;
    }

    /**
     * Converts a string into titlecase.
     *
     * For example::
     *
     *     <?=$item->value->title()?>
     *
     * If ``value`` is ``"my first post"``, the output will be
     * ``"My First Post"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function title() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v) {
            if (!is_array($v) && !($v instanceof stdClass))  {
                // Some PHP 5.2.x versions have problems with single quotes,
                // interpreting them as spaces. Fix.
                $v = str_replace("'", '__SINGLEQUOTE', $v);
                $v = mb_convert_case($v, MB_CASE_TITLE, self::$encoding);
                $v = str_ireplace('__SINGLEQUOTE', "'", $v);
            }
        }
        return $this;
    }

    private function _urlize_cb1($ms) {
        return empty($ms[2]) ? $ms[0]
               : "$ms[1]<a href=\"$ms[2]\" rel=\"nofollow\">$ms[2]</a>";
    }

    private function _urlize_cb2($ms) {
        return empty($ms[2])
               ? $ms[0]
               : sprintf('<a href="http://%1$s" rel="nofollow">http://%1$s</a>'
                         , trim($ms[2], '.,;:)'));
    }

    private function _urlize_cb3($ms) {
        return "$ms[1]<a href=\"mailto:$ms[2]@$ms[3]\">$ms[2]@$ms[3]</a>";
    }

    private function _urlize_cb4($ms) {
        return "<a href=\"http://$ms[0]\" rel=\"nofollow\">$ms[0]</a>";
    }

    private function _urlize($v) {
        // Thanks Wordpress (I guess).
        $v = preg_replace_callback('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff'
             . '\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<'
             .'.,;:]|$)|\)))+)#is', array($this, '_urlize_cb1')
             , " $v");
        $v = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.'.
             '\-;:=,?@\[\]+]+)#is', array($this, '_urlize_cb2'), $v);
        $v = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+'
             . '[0-9a-z]{2,})#i', array($this, '_urlize_cb3'), $v);
        $v = trim(preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i",
             "$1$3</a>", $v));
        if (strpos($v, 'www') !== FALSE)
            $v = str_replace(array ('>http://', '>https://'), '>', $v);
        return  preg_replace_callback('#(^| )[a-z0-9-_+]+\.(com|org|net)#',
             array($this, '_urlize_cb4'), $v);
    }

    /**
     * Converts URLs in text into clickable links.
     *
     * Works on links prefixed with ``http://``, ``https://``, or ``www.``. For
     * example, ``http://goo.gl/aia1t`` will get converted but ``goo.gl/aia1t``
     * won't.
     *
     * Also works on domain-only links ending in one of the common ``.com``,
     * ``.net``, or ``.org`` top level domains.
     * For example, ``chano.readthedocs.org`` will still get converted.
     *
     * Links can have trailing punctuation (periods, commas, close-parens) and
     * leading punctuation (opening parens) and ``urlize`` will still do the
     * right thing.
     *
     * Links generated by ``urlize`` have a ``rel="nofollow"`` attribute added
     * to them.
     *
     * For example::
     *
     *     <?=$item->value->urlize()?>
     *
     * If ``value`` is ``"Check out chano.readthedocs.org"``, the output will be
     * ``"Check out <a href="http://chano.readthedocs.org"
     * rel="nofollow">chano.readthedocs.org</a>"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function urlize() {
        $this->_autoescape_single = false;
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_urlize($v);
        return $this;
    }

    private function _urlizetrunc_cb($ms) {
        $len = $this->_urlizetrunc_len;
        if ($len <= 3) return $ms[1] . '...' . $ms[3];
        if (mb_strlen($ms[2], self::$encoding) <= $len) return $ms[0];
        return $ms[1] . mb_substr($ms[2], 0, $len-3, self::$encoding) . '...' . $ms[3];
    }

    function _urlizetrunc($v) {
        $v = self::_urlize($v);
        return preg_replace_callback('#(<a href=.*">)([^<]*)(</a>)#Uis',
                   array($this, '_urlizetrunc_cb'), $v);
    }

    /**
     * Converts URLs into clickable links just like urlize_, but truncates URLs
     * longer than the given character limit.
     *
     * For example::
     *
     *     <?=$item->value->urlizetrunc(15)?>
     *
     * If ``value`` is ``"Check out chano.readthedocs.org"``, the output will
     * be ``'Check out <a href="http://chano.readthedocs.org"
     * rel="nofollow">chano.readth...</a>'``.
     *
     * As with urlize_, this filter should only be applied to plain text.
     *
     * @param int $length
     *   Number of characters that link text should be truncated to, including the ellipsis that's added if truncation is necessary.
     * @chanotype filter
     * @return Chano instance
     */
    function urlizetrunc($len) {
        // TODO: This passes the tests but also truncates existing html
        // addresses which is probably not the desired behavior. Change _urlize
        // to support truncate.
        $this->_autoescape_single = false;
        $this->_urlizetrunc_len = $len;
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_urlizetrunc($v, $len);
        return $this;
    }

    /**
     * Truncates a string after a certain number of words.
     *
     * For example::
     *
     *     <?=$item->value->truncatewords(2)?>
     *
     * If ``value`` is ``"Joel is a slug"``, the output will be
     * ``"Joel is ..."``.
     *
     * @param string $number
     *   Number of words to truncate after.
     * @chanotype filter
     * @return Chano instance
     */
    function truncatewords($number) {
        // Thanks banderson623: http://snippets.dzone.com/posts/show/412.
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v) {
            if (!is_array($v) && !($v instanceof stdClass))  {
                $parts = explode(' ', $v);
                if(count($parts) > $number && $number>0)
                    $v = implode(' ', array_slice($parts, 0, $number)) . ' ...';
            }
        }
        return $this;
    }

    private function _truncatewordshtml($v, $n) {
        // Strip tags, explode words and count the number of chars of the
        // first n words. Then use cakePHP magic function.
        if ($n == 0) return '';
        $parts = explode(' ', strip_tags($v));
        $found_words = 0;
        $found_words_len = 0;
        foreach ($parts as $part) {
            $found_words_len += mb_strlen($part);
            if (preg_match('#[\w][\w-]+[\w]#u', $part)) ++$found_words;
            if ($found_words == $n) {
                // Thankyou cakePHP!
                return chano_truncate($v, $found_words_len+4, array(
                    'ending' => ' ...', 'exact' => true, 'html' => true,
                ), self::$encoding);
            }
            ++$found_words_len;
        }
        return $v;
    }

    /**
     * Similar to `truncatewords`_, except that it is aware of HTML tags.
     * Any tags that are opened in the string and not closed before the
     * truncation point, are closed immediately after the truncation.
     *
     * This is less efficient than ``truncatewords``, so should only be used
     * when it is being passed HTML text.
     *
     * For example::
     *
     *     <?=$item->value->truncatewords_html(2)?>
     *
     * If ``value`` is ``"<p>Joel is a slug</p>"``, the output will be
     * ``"<p>Joel is ...</p>"``.
     *
     * Newlines in the HTML content will be preserved.
     *
     * @param string $number
     *   Number of words to truncate after.
     * @chanotype filter
     * @return Chano instance
     */
    function truncatewordshtml($number) {
        $this->_autoescape_single = false;
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_truncatewordshtml($v, $number);
        return $this;
    }

    /**
     * Truncates a string if it is longer than the specified number of
     * characters. Truncated strings will end with an ellipsis, which defaults
     * to ("...") but can be set by the second argument.
     *
     * For example::
     *
     *     <?=$item->value->truncatechars(9)?>
     *
     * If ``value`` is ``"Joel is a slug"``, the output will be ``"Joel i..."``.
     *
     * @param int $length
     * @param string $ellipsis
     *   Custom ellipsis character(s).
     * @chanotype filter
     * @return Chano instance
     */
    function truncatechars($length, $ellipsis='...') {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                if (mb_strlen($v) > $length)
                    $v = mb_substr($v, 0, $length - mb_strlen($ellipsis),
                                self::$encoding)
                         . $ellipsis;
        return $this;
    }

    /**
     * Escapes a value for use in an URL.
     *
     * For example::
     *
     *     <?=$item->value->urlencode()?>
     *
     * If ``value`` is ``"http://www.example.org/foo?a=b&c=d"``, the output will
     * be ``"http%3A//www.example.org/foo%3Fa%3Db%26c%3Dd"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function urlencode() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = urlencode($v);
        return $this;
    }

    /**
     * Converts an IRI (Internationalized Resource Identifier) to a string that
     * is suitable for including in an URL. This is necessary if you're trying
     * to use strings containing non-ASCII characters in an URL.
     *
     * It's safe to use this filter on a string that has already gone through
     * the ``urlencode`` filter.
     *
     * For example::
     *
     *     <?=$item->value->iriencode()?>
     *
     * If ``value`` is ``"?test=1&me=2"``, the output will be
     * ``"?test=1&amp;me=2"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function iriencode() {
        // TODO: Keep this? Suspicious!
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = str_replace('+', '%20', urlencode(urldecode($v)));
        return $this;
    }

    private function _slice($v, $str) {
        $ps = explode(':', $str);
        $count = count($ps);
        $e = self::$encoding;
        if ($count == 1) {
            $a = $ps[0];
            if ($a == 0) return '';
            else return mb_substr($v, 0, $a, $e);
        }
        if ($count == 2) {
            list($a,$b) = $ps;
            return mb_substr($v, $a, $b-$a, $e);
        }
        if ($count == 3) {
            list ($a, $dummy, $b) = $ps;
            $v = mb_substr($v, $a, mb_strlen($v, $e), $e);
            $len = mb_strlen($v, $e) - 1;
            $result = '';
            for ($i=$a; $i<=$len; $i+=$b) $result .= mb_substr($v, $i, 1, $e);
            return $result;
        }
        return '';
    }

    /**
     * Returns a slice of a string.
     *
     * Uses the same syntax as Python's list slicing. See
     * http://diveintopython.org/native_data_types/lists.html#odbchelper.list.slice
     * for an introduction.
     *
     * Example::
     *
     *     <?=$item->value->slice("0")?>
     *     <?=$item->value->slice("1")?>
     *     <?=$item->value->slice("-1")?>
     *     <?=$item->value->slice("1:2")?>
     *     <?=$item->value->slice("1:3")?>
     *     <?=$item->value->slice("0::2")?>
     *
     * If ``value`` is ``"abcdefg"``, the outputs will be
     * ``""``, ``"a"``, ``"abcdef"``, ``"b"``, ``"bc"`` and ``"aceg"``
     * respectively.
     *
     * @todo Make it work on traversables too.
     * @param string $slice_string
     * @chanotype filter
     * @return Chano instance
     */
    function slice($slice_string) {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_slice($v, $slice_string);
        return $this;
    }

    private function _linenumbers($v) {
        $lines = explode("\n", trim($v));
        $strlen = strlen(count($lines));
        $string = '';
        $i = 1;
        foreach ($lines as $line) {
            $string .=
                $this->_mb_str_pad($i, $strlen, '0', STR_PAD_LEFT)
                . '. ' . $line . "\n";
            ++$i;
        }
        return $string;
    }

    /**
     * Displays text with line numbers.
     *
     * For example::
     *
     *     <?=$item->value->linenumbers()?>
     *
     * If ``value`` is::
     *
     *     one
     *     two
     *     three
     *
     * the output will be::
     *
     *     1. one
     *     2. two
     *     3. three
     *
     * @chanotype filter
     * @return Chano instance
     */
    function linenumbers() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_linenumbers($v);
        return $this;
    }

    /**
     * Removes given arguments of [X]HTML tags from the output.
     *
     * For example::
     *
     *     <?=$item->value->removetags("b", "span", "ol")?>
     *
     * If ``value`` is ``"<b>Joel</b> <button>is</button> a <span>slug</span>"``
     * the output will be ``"Joel <button>is</button> a slug"``.
     *
     * Note that this filter is case-sensitive.
     *
     * If ``value`` is ``"<B>Joel</B> <button>is</button> a <span>slug</span>"``
     * the output will be ``"<B>Joel</B> <button>is</button> a slug"``.
     *
     * @param string $tag1 ... $tagN
     *   An arbitrary number of tags to be removed.
     * @chanotype filter
     * @return Chano instance
     */
    function removetags() {
        $this->_autoescape_single = false;
        $args = func_get_args();
        if (empty($args)) return $this;
        $tags = implode('|', $args);
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = preg_replace("/<\\/?($tags)(\\s+.*?>|>)/Uis", '', $v);
        return $this;
    }

    function _linebreaks($v) {
        $v = preg_replace('#\r\n|\r|\n#', "\n", $v);
        $paragrahps = preg_split('#\n{2,}#', $v);
        $html = '';
        foreach ($paragrahps as $p)
            $html .= '<p>' . str_replace("\n", '<br />', $p) . '</p>';
        return $html;
    }

    /**
     * Replaces line breaks in plain text with appropriate HTML; a single
     * newline becomes an HTML line break (``<br />``) and a new line
     * followed by a blank line becomes a paragraph break (``</p>``).
     *
     * For example::
     *
     *     <?=$item->value->linebreaks()?>
     *
     * If ``value`` is ``Joel\nis a slug``, the output will be ``<p>Joel<br />is
     * a slug</p>``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function linebreaks() {
        $this->_autoescape_single = false;
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_linebreaks($v);
        return $this;
    }

    /**
     * Converts all newlines in a piece of plain text to HTML line breaks
     * (``<br />``).
     *
     * For example::
     *
     *     <?=$item->value->linebreaksbr()?>
     *
     * If ``value`` is ``"Joel\nis a slug"``, the output will be
     * ``Joel<br />is a slug``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function linebreaksbr() {
        $this->_autoescape_single = false;
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = nl2br($v);
        return $this;
    }

    /**
     * Joins a list with a string, like the
     * `implode() <http://php.net/manual/en/function.implode.php>`_ function.
     *
     * For example::
     *
     *     <?=$item->value->join(" // ")?>
     *
     * If ``value`` is the array ``array('a', 'b', 'c')``, the output will be
     * the string ``"a // b // c"``.
     *
     * @param string $glue
     * @chanotype filter
     * @return Chano instance
     */
    function join($glue=', ') {
        if (is_scalar($this->_v)) return $this;
        $this->_v = implode($glue, $this->_v);
        return $this;
    }

    /**
     * Returns the value turned into an array.
     *
     * For example::
     *
     *     <?=$item->value->makelist()?>
     *
     * If ``value`` is the string ``"Joel"``, the output will be the array
     * ``array('J', 'o', 'e', 'l')``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function makelist() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v) {
            if (!is_array($v) && !($v instanceof stdClass))  {
                $_vs = preg_split('/(?<!^)(?!$)/u', (string)$v);
                if (is_int($v)) foreach ($_vs as &$_v) $_v = (int)$_v;
                $v = $_vs;
            }
        }
        return $this;
    }

    /**
     * Converts to lowercase, removes non-word characters (alphanumerics and
     * underscores) and converts spaces to hyphens. Also strips leading and
     * trailing whitespace.
     *
     * For example::
     *
     *     <?=$item->value->slugify()?>
     *
     * If ``value`` is ``"Joel is a slug"``, the output will be
     * ``"joel-is-a-slug"``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function slugify() {
        // Thanks Borek! http://drupal.org/node/63924.
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v) {
            if (!is_array($v) && !($v instanceof stdClass))  {
                $v = str_replace(array(',', '\''), '', $v);
                $v = preg_replace('#[^\\pL0-9_]+#u', '-', $v);
                $v = preg_replace('#[-]{2,}#', '-', $v);
                $v = trim($v, "-");
                $v = iconv(self::$encoding, "us-ascii//TRANSLIT", $v);
                $v = strtolower($v);
                $v = preg_replace('#[^-a-z0-9_]+#', '', $v);
            }
        }
        return $this;
    }

    /**
     * Converts a phone number (possibly containing letters) to its numerical
     * equivalent.
     *
     * The input doesn't have to be a valid phone number. This will happily
     * convert any string.
     *
     * For example::
     *
     *     <?=$item->value->phone2numeric()?>
     *
     * If ``value`` is ``800-COLLECT``, the output will be ``800-2655328``.
     *
     * @chanotype filter
     * @return Chano instance
     */
    function phone2numeric() {
        static $replace_pairs = array('a' => '2', 'b' => '2', 'c' => '2',
            'd' => '3', 'e' => '3', 'f' => '3', 'g' => '4', 'h' => '4',
            'i' => '4', 'j' => '5', 'k' => '5', 'l' => '5', 'm' => '6',
            'n' => '6', 'o' => '6', 'p' => '7', 'q' => '7', 'r' => '7',
            's' => '7', 't' => '8', 'u' => '8', 'v' => '8', 'w' => '9',
            'x' => '9', 'y' => '9', 'z' => '9');
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = strtr(strtolower($v), $replace_pairs);
        return $this;
    }
    
    /**
     * @section question
     *   Questions
     *
     *   Conditionally returns a value based on the value of the current item or
     *   other parameters. All questions are nonchainable.
     */

    /**
     * If value evaluates to ``false``, use given default. Otherwise, use the
     * value.
     *
     * For example::
     *
     *     <?=$item->value->default("nothing")?>
     *
     * If ``value`` is ``""`` (the empty string), the output will be
     * ``nothing``.
     *
     * @chanotype question
     * @return mixed
     */
    function emptyor($default) {
        $v = $this->_reset_filter();
        return empty($v) ? $default : $v;
    }

    /**
     * True if this is the first time through the loop.
     *
     * For example::
     *
     *     <?foreach (new Chano($players) as $player):?>
     *         <?if ($player->score->first()):?>
     *             First!
     *         <?endif?>
     *     <?endforeach?
     * 
     * @chanotype question
     * @return bool
     */
    function isfirst() { return $this->_i === 0; }

    /**
     * True if this is the last time through the loop.
     *
     * For example::
     * 
     *    <?foreach (new Chano($players) as $player):?>
     *         <?if ($player->score->islast()):?>
     *             Last!
     *         <?endif?>
     *     <?endforeach?>
     *
     * @todo Write tests.
     * @chanotype question
     * @return bool
     */
    function islast() { return $this->_i === $this->_iterator->count(); }

    /**
     * Check if a value has changed from the last iteration of a loop.
     * 
     * For example::
     *
     *     <?foreach (new Chano($players) as $player):?>
     *         <?if ($player->score->changed()):?>
     *             Changed!
     *         <?endif?>
     *     <?endforeach?>
     *
     * @todo Write tests.
     * @chanotype question
     * @return bool
     */
    function changed() {
        $this->_v = self::INITIAL;
        $path = $this->_lookup_path_reset();
        return isset($this->_previous_lookups[$path]) &&
            $this->_previous_lookups[$path] != $this->_lookups[$path];
    }

    /**
     * Check if a value is the same as the last iteration of a loop.
     *
     * For example::
     *
     *     <?foreach (new Chano($players) as $player):?>
     *         <?if ($player->score->same()):?>
     *             Same!
     *         <?endif?>
     *     <?endforeach?>
     *
     * @chanotype question
     * @return bool
     */
    function same() {
        $this->_v = self::INITIAL;
        $path = $this->_lookup_path_reset();
        return isset($this->_previous_lookups[$path]) &&
            $this->_previous_lookups[$path] == $this->_lookups[$path];
    }

    /**
     * Returns ``true`` if the value is divisible by the argument.
     *
     * For example::
     *
     *     <?=$item->value->divisibleby(3)?>
     *
     * If ``value`` is ``21``, the output will be ``true``.
     *
     * @chanotype question
     * @return bool
     */
    function divisibleby($divisor) {
        return ($this->_reset_filter() % $divisor) === 0;
    }

    /**
     * @section counter
     *   Counters
     *
     *   Different methods of counting to/from the current item. Works on the
     *   base instance, e.g. you don't have to ask for a key first. All counters
     *   are chainable.
     */

    /**
     * The current iteration of the loop (1-indexed).
     *
     * For example::
     *
     *     <?foreach(new Chano($items) as $item):?>
     *          <?=$item->counter()?>
     *     <?endforeach?>
     *
     * If ``$items`` is::
     *
     *     array(
     *         array('value' => 'foo'),
     *         array('value' => 'foo'),
     *         array('value' => 'foo'),
     *     )
     *
     * The output will be ``123``.
     * 
     * @chanotype counter
     * @return Chano Instance
     */
    function counter() {
        $this->_v = $this->_i + 1;
        return $this;
    }
    
    /**
     * The current iteration of the loop (0-indexed).
     *
     * For example::
     *
     *     <?foreach(new Chano($items) as $item):?>
     *          <?=$item->counter0()?>
     *     <?endforeach?>
     *
     * If ``$items`` is::
     *
     *     array(
     *         array('value' => 'foo'),
     *         array('value' => 'foo'),
     *         array('value' => 'foo'),
     *     )
     *
     * The output will be ``012``.
     * 
     * @chanotype counter
     * @return Chano Instance
     */
    function counter0() {
        $this->_v = $this->_i;
        return $this;
    }

    /**
     * The number of iterations from the end of the loop (1-indexed).
     *
     * For example::
     *
     *     <?foreach(new Chano($items) as $item):?>
     *          <?=$item->revcounter()?>
     *     <?endforeach?>
     *
     * If ``$items`` is::
     *
     *     array(
     *         array('value' => 'foo'),
     *         array('value' => 'foo'),
     *         array('value' => 'foo'),
     *     )
     *
     * The output will be ``321``.
     * 
     * @chanotype counter
     * @return Chano Instance
     */
    function revcounter() {
        $this->_v = $this->_iterator->count() - $this->_i;
        return $this;
    }

    /**
     * The number of iterations from the end of the loop (0-indexed).
     *
     * For example::
     *
     *     <?foreach(new Chano($items) as $item):?>
     *          <?=$item->revcounter0()?>
     *     <?endforeach?>
     *
     * If ``$items`` is::
     *
     *     array(
     *         array('value' => 'foo'),
     *         array('value' => 'foo'),
     *         array('value' => 'foo'),
     *     )
     *
     * The output will be ``210``.
     *
     * @chanotype counter
     * @return Chano Instance
     */
    function revcounter0() {
        $this->_v = $this->_iterator->count() - $this->_i - 1;
        return $this;
    }
    
    /**
     * @section other
     *   Other
     *
     *   Other functions.
     */

    /**
     * Returns the first not empty value of the given arguments. This function
     * is chainable. Works on the base instance.
     *
     * For example::
     *
     *   <?=$item->firstfull(0, null, array(), new stdClass, 42)?>
     *
     * Will output ``42``.
     *
     * @chanotype other
     * @param mixed $arg1 ... $argN
     * $return Chano instance
     */
    function firstfull() {
        $args = func_get_args();
        $this->_v = '';
        foreach ($args as $arg) {
            if (!empty($arg)) {
                $this->_v = $arg;
                break;
             }
        }
        return $this;
    }

    /**
     * Cycle among the given arguments, each time this function is called. Works
     * multiple times with different arguments inside the same loop. This
     * function is chainable.
     *
     * For example::
     *
     *     <?foreach (new Chano($items) as $item):?>
     *         <tr class="<?=$item->cycle('row1', 'row2')?>">
     *             ...
     *         </tr>
     *     <?endforeach?>
     *
     * @param mixed $arg1 ... $argN
     * @chanotype other
     * @return Chano Instance
     */
    function cycle() {
        static $cycles = array();
        $args = func_get_args();
        $key = implode('', $args);
        if (empty($cycles[$key])) {
            $cycles[$key] = array($args, 0, count($args)-1);
            $this->_v = $cycles[$key][0][0];
        } else {
            $cycles[$key][1]++;
            if ($cycles[$key][1] > $cycles[$key][2]) $cycles[$key][1] = 0;
            $this->_v = $cycles[$key][0][$cycles[$key][1]];
        }
        return $this;
    }

    /**
     * Returns the length of the current value. If the current value is a scalar
     * (string, int, etc.) the string length will be returned, otherwise the
     * count. This function is non chainable.
     *
     * For example::
     * 
     *     <?=$item->value->length()?>
     * 
     * If ``value`` is ``"joel"`` or ``array("j", "o", "e", "l")`` the output
     * will be ``4``.
     * 
     * If length is called on the base instance, the count of the main dataset 
     * is given.
     * 
     * For example if ``$items`` is::
     * 
     *     new Chano(array(
     *         array('title' => 'foo'),
     *         array('title' => 'bar'),
     *     ))
     * 
     * then::
     * 
     *     <?$items->length()?>
     * 
     * will output ``2``.
     * 
     * @chanotype other
     * @return int
     */
    function length() {
        $v = $this->_get_v_or_iterator();
        $this->_reset_filter();
        if (is_scalar($v)) return mb_strlen((string)$v, self::$encoding);
        else return count($v);
    }
    
    /**
     * Returns a plural suffix if the value is not 1. By default,
     * this suffix is ``'s'``.
     *
     * Example::
     *
     *     You have <?$item->num_messages?> message<?$item->num_messages->pluralize()?>.
     *
     * If ``num_messages`` is ``1``, the output will be ``You have 1 message.``
     * If ``num_messages`` is ``2``  the output will be ``You have 2 messages.``
     *
     * For words that require a suffix other than ``'s'``, you can provide an
     * alternate suffix as the first argument to the filter.
     *
     * Example::
     *
     *     You have <?$item->num_walruses?> walrus<?$item->num_messages->pluralize("es")?>.
     *
     * For words that don't pluralize by simple suffix, you can specify both a
     * plural and singular suffix as arguments.
     *
     * Example::
     *
     *     You have <?$item->num_cherries?> cherr<?$item->num_cherries->pluralize("y", "ies")?>.
     * 
     * If pluralize is called on the base instance, what is being pluralized
     * is the main dataset. See `length`_.
     *
     * @chanotype other
     * @param string $plural
     * @param string $singular
     * @return string
     */
    function pluralize($plural='s', $singular=null) {
        if (empty($singular)) list($plural, $singular) = array('', $plural);
        else list($plural, $singular) = array($plural, $singular);
        $v = $this->_get_v_or_iterator();
        $this->_reset_filter();
        if (is_scalar($v)) {
            if ((int)$v == 0) return $singular;
            else return (int)$v > 1 ? $singular : $plural;
        }
        else return count($v) > 1 ? $singular : $plural;
    }

    /**
     * For performance reasons, Chano changes the values of the current item by
     * reference. So if you apply a function to a value, and accesses that same
     * value again, the value is still changed. `deepcopy`_ clones the values
     * in the current item, and rebuilds it after every ``__toString()`` call.
     *
     * For example if ``$items`` is::
     *
     *     array(
     *         array('title' => 'foo')
     *     )
     *
     * The following::
     *
     *     <?foreach(new Chano($items) as $item)?>
     *         <?=$item->title->upper()?>
     *         - <?=$item->title?>
     *     <?endforeach?>
     *
     * Will output ``FOO - FOO``. But using deepcopy like::
     * 
     *     <?foreach(new Chano($items) as $item)?>
     *         <?=$item->deepcopy()->title->upper()?>
     *         - <?=$item->title?>
     *     <?endforeach?>
     *
     * Will output ``FOO - foo``.
     * 
     * @chanotype other
     * @return Chano instance
     */
    function deepcopy() {
        unset($this->_current_clone);
        $this->_current_clone = $this->_clone($this->_current);
        return $this;
    }
    
    /**
     * ``var_dumps()`` the content of the current value to screen.
     *
     * @chanotype other
     * @return Chano instance
     */
    function vd() { var_dump($this->_v); return $this; }

    /**
     * @section escaping
     *   Escaping
     *
     *   By default all output from Chano is escaped but this behavior can be
     *   modified by the functions in this section. All escaping functions are
     *   chainable.
     */

    /**
     * Switches on auto-escaping behavior. This only has any effect after the
     * :ref:`autoescapeoff` method has been called as the default behavior of
     * Chano is to escape all output.
     *
     * When auto-escaping is in effect, all variable content has HTML escaping
     * applied to it before placing the result into the output (but after any
     * filters have been applied).
     *
     * Sample usage::
     *
     *     <?foreach(new Chano($items) as $item)?>
     *         <?=$item->autoescapeoff()->body?>
     *         <?=$item->comments?>
     *         <?=$item->autoescapeon()?>
     *         <?=$item->title?>
     *     <?endforeach?>
     *
     * @chanotype escaping
     * @return Chano instance
     */
    function autoescapeon() { $this->_autoescape = true; return $this; }

    /**
     * Switches off the default auto-escaping behavior. This means that all
     * output until the end or until :ref:`autoescapeon` is called will not be
     * escaped unless :ref:`escape` is specifically called.
     *
     * Sample usage::
     *
     *     <?foreach(new Chano($items) as $item)?>
     *         <?=$item->autoescapeoff()->body?>
     *         <?=$item->comments?> <!-- body and comments are not escaped -->
     *         <?=$item->autoescapeon()?>
     *         <?=$item->title?> <!-- title is escaped -->
     *     <?endforeach?>
     *
     * @chanotype escaping
     * @return Chano instance
     */
    function autoescapeoff() { $this->_autoescape = false; return $this; }

    /**
     * Forces escaping on the next output, e.g. when __toString() is called,
     * overruling the :ref:`autoescapeoff` flag a single time. When
     * autoescaping is on this flag has no effect.
     *
     * The opposite of `safe`_.
     *
     * For example::
     *
     *     <?foreach(new Chano($items) as $item)?>
     *         <?=$item->autoescapeoff()?>
     *         <?=$item->escape()->body?> <!-- body is escaped -->
     *         <?=$item->comments?> <!-- comments is not escaped -->
     *     <?endforeach?>
     *
     * @chanotype escaping
     * @return Chano instance
     */
    function escape() { $this->_autoescape_single = true; return $this; }

    /**
     * Marks a string as not requiring further HTML escaping prior to output.
     * When autoescaping is off, this filter has no effect.
     *
     * The opposite of `escape`_.
     *
     * If you are chaining filters, a filter applied after ``safe`` can
     * make the contents unsafe again. For example, the following code
     * prints as escaped::
     *
     *     <?=$item->value->safe()->escape()?>
     *
     * @chanotype escaping
     * @return Chano instance
     */
    function safe() { $this->_autoescape_single = false; return $this; }

    /**
     * Applies HTML escaping to a string (see the `escape`_ filter for
     * details). This filter is applied *immediately* and returns a new, escaped
     * string. This is useful in the rare cases where you need multiple escaping
     * or want to apply other filters to the escaped results. Normally, you want
     * to use the `escape`_ filter.
     *
     * @chanotype return
     * @return Chano instance
     */
    function forceescape() {
        if (is_array($this->_v) || $this->_v instanceof stdClass) $vs = &$this->_v; 
        else $vs = array(&$this->_v);
        $autoescape_next = $this->_autoescape_single;
        foreach($vs as &$v)
            if (!is_array($v) && !($v instanceof stdClass))
                $v = $this->_escape($v);
        $this->_autoescape_single = $autoescape_next;
        return $this;
    }
}

// Register iterators.
require realpath(dirname(__FILE__) . '/lib/iterators.php');
