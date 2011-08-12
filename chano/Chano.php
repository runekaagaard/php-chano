<?php
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);

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
 * An iterator that takes an array of arrays as an input and supplies
 * capabilities matching the Django Template Language. Implements the full
 * featureset (almost) found here:
 *     http://docs.djangoproject.com/en/dev/ref/templates/builtins/.
 *
 * @author Rune Kaagaard
 * @todo
 *
 * 1) While the filter tests are pretty good, more tests of more general nature
 * are needed.
 * 2) Create proper documentation in restructured text.
 * 3) Make it work for other kind of iterators than arrays.
 *
 * @codestyle
 *
 * In this project i've been experimenting with a non-pear code style. Some of
 * those experiments entails:
 *   * One line functions.
 *   * Skipping brackets.
 *   * Having both a if/foreach and a statement on a single line.
 *   * Not adding docblocks to (for me) obvious stuff.
 *   * Skipping default "public" keywords.
 */
class Chano implements Iterator, ArrayAccess {
    /**
     * The encoding used in charset sensitive filters.
     * @var string
     */
    static $encoding = 'utf-8';
    static $iterators;

    /**
     * The value of the current item after filters has been applied.
     * @var scalar/array
     */
    public $v = self::INITIAL;

    // Private values.
    const INITIAL = '__CHANO_INITIAL__';
    private $iterator;
    private $count = 0;
    private $i = 0;
    private $items;
    private $current = self::INITIAL;
    private $lookup_path;
    private $lookups = array();
    private $previous_lookups = array();
    private $autoescape = true;
    private $autoescape_off_until_tostring = false;

    /**
     * Takes an array of arrays as first parameter and an optional array of
     * options as second.
     * 
     * @param array $items
     *   Accepts an array, object, iterator, etc. giving arrays or objects. The
     *   given value is responsible for being countable for any of the filters
     *   using that feature to be used.
     */
    function  __construct($items) {
        $this->set_iterator($items);
    }
    function  __toString() {
        return $this->out($this->reset_v());
    }
    private function out($v, $escape=null) {
        if ($escape === null) $escape = $this->autoescape;
        $s = !$this->autoescape_off_until_tostring && $escape
            ? htmlspecialchars((string)$v, ENT_NOQUOTES, self::$encoding)
            : (string)$v;
        $this->autoescape_off_until_tostring = FALSE;
        return (string)$s;
    }

    /**
     * Resets and returns current value.
     *
     * @return mixed
     */
    private function reset_v() {
        $value = $this->v;
        $this->v = self::INITIAL;
        $this->lookup_path_reset();
        return $value;
    }

    /**
     * Resets settings for filters that does not wait for the __toString()
     * method being called to return calue.
     *
     * @return mixed
     */
    private function reset_filter() {
        $this->autoescape_off_until_tostring = FALSE;
        return $this->reset_v();
    }
    
    /*
     * Implementation of Iterator interface.
     */

    static function register_iterator($class) {
        self::$iterators[] = $class;
    }

    private function set_iterator($items) {
        foreach (self::$iterators as $iterator) {
            if ($iterator::is_match($items)) {
                $this->iterator = $iterator::get_instance($items);
                return true;
            }
        }
        throw new Chano_NoMatchingIteratorFoundError;
    }
    function rewind() { $this->iterator->rewind(); }
    function current() {
        $this->current = $this->iterator->current();
        return $this;
    }
    function key() { return $this->iterator->key(); }
    function next() {
        $this->lookup_next();
        $this->current = $this->iterator->next();
        ++$this->i;
    }
    function valid() {
        return $this->iterator->valid();
    }

    /*
     * Stores lookups ($i->key1->key2->etc->_) so it can be compared to lookups
     * for previous item.
     */
    
    private function lookup_add($o) {
        $this->lookup_path .= $o;
        $this->lookups[$this->lookup_path] = $this->v;
    }
    private function lookup_path_reset() {
        $path = $this->lookup_path;
        $this->lookup_path = '';
        return $path;
    }
    private function lookup_next() {
        $this->previous_lookups = $this->lookups;
        $this->lookups = array();
        $this->lookup_path_reset();
    }

    /*
     * Implementation of ArrayAcces interface.
     */
    
    function offsetGet($o) {
        if ($o == '_') return $this->__toString();

        if ($this->v == self::INITIAL) $v = $this->current;
        else $v = $this->v;

        if (is_object($v)) $this->v = $v->$o;
        elseif (is_array($v)) $this->v = $v[$o];
        else throw new Chano_TypeNotComplexError;

        $this->lookup_add($o);
        return $this;
    }
    function offsetExists($offset) { throw new Chano_NotImplementedError; }
    function offsetSet($offset, $value) { throw new Chano_ReadOnlyError; }
    function offsetUnset($offset) { throw new Chano_ReadOnlyError; }

    /**
     * Implementation of __get magic method.
     *
     * @param string $name
     * @return mixed
     */
    function  __get($name) { return $this->offsetGet($name); }
    
    function __call($name, $args) {
        $this->v = call_user_func_array(array($this->current, $name), $args);
        return $this;
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // Below this line are the methods that are part of the template api.     //
    ////////////////////////////////////////////////////////////////////////////

    /*
     * Flags.
     *
     * Sets one or more boolean values on the Chano class. Chainable.
     *
     * Sets autoescape on output on/off.
     */

    function autoescapeon() { $this->autoescape = true; return $this; }
    function autoescapeoff() { $this->autoescape = false; return $this; }
    function escape() {
        $this->autoescape_off_until_tostring = false;
        $this->autoescape = true;
        return $this;
    }

    /*
     * Questions.
     *
     * Conditionally returns a boolean based on value of current item. All
     * questions are nonchainable.
     */
    
    function emptyor($default) {
        $value = $this->reset_filter();
        return empty($value) ? $default : $value;
    }
    function isfirst() { return $this->i === 0; }
    function islast() { return $this->i === $this->iterator->count(); }
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
    function divisibleby($divisor) {
        return ($this->reset_filter() % $divisor) === 0;
    }

    /*
     * Returns.
     *
     * Returns value of current item in various ways. Unchainable.
     */

    function safe() { return $this->out($this->v, false); }
    function forceescape() {
        return htmlentities($this->reset_filter(), null, self::$encoding);
    }

    /*
     * Counters.
     *
     * Different methods of counting to/from the current item. Chainable. Works
     * on the base instance, ie. you don't have to ask for a key first.
     */

    function counter() {
        $this->v = $this->i + 1;
        return $this;
    }
    function counter0() {
        $this->v = $this->i;
        return $this;
    }
    function revcounter() {
        $this->v = $this->iterator->count() - $this->i;
        return $this;
    }
    function revcounter0() {
        $this->v = $this->iterator->count() - $this->i - 1;
        return $this;
    }
    
    /*
     * Selectors.
     *
     * One of given arguments are conditionally returned. Chainable. Works on
     * base instance too.
     */

    function firstof() {
        $args = func_get_args();
        $this->v = '';
        foreach ($args as $arg) {
            if (!empty($arg)) {
                $this->v = $arg;
                break;
            }
        }
        return $this;
    }
    function cycle() {
        static $cycles = array();
        $args = func_get_args();
        $key = implode('', $args);
        if (empty($cycles[$key])) {
            $cycles[$key] = array($args, 0, count($args)-1);
            $this->v = $cycles[$key][0][0];
        } else {
            $cycles[$key][1]++;
            if ($cycles[$key][1] > $cycles[$key][2]) $cycles[$key][1] = 0;
            $this->v = $cycles[$key][0][$cycles[$key][1]];
        }
        return $this;
    }

    /*
     * Other nonchainable commands.
     */
    function length() {
        $v = $this->reset_filter();
        if (is_scalar($v)) return strlen((string)$v);
        else return count($v);
    }

    /*
     * Filters.
     *
     * Modifies the value of the current item. Chainable.
     */

    function pluralize($a='s', $b=null) {
        if (empty($b)) list($singular, $plural) = array('', $a);
        else list($singular, $plural) = array($a, $b);
        if (is_scalar($this->v)) {
            if ((int)$this->v == 0) $this->v = $plural;
            else $this->v = (int)$this->v > 1 ? $plural : $singular;
        }
        else $this->v = count($this->v) > 1 ? $plural : $singular;
        return $this;
    }
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
    function unorderedlist() {
        $this->autoescape_off_until_tostring = true;
        $this->v = $this->_unorderedlist($this->_clean_list($this->v));
        return $this;
    }
    
    function striptags() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = strip_tags($v);
        return $this;
    }
    function vd() { var_dump($this->v); return $this; }
    function now($format) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = date($format);
        return $this;
    }
    function widthratio($range_in, $range_out) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = round($v / $range_in * $range_out);
        return $this;
    }
    function add($amount) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v += $amount;
        return $this;
    }
    function addslashes() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = addslashes($v);
        return $this;
    }
    function capfirst() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = ucfirst($v);
        return $this;
    }
    function upper() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = mb_strtoupper($v, self::$encoding);
        return $this;
    }
    function center($width) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = str_pad($v, $width, " ", STR_PAD_BOTH);
        return $this;
    }
    function ljust($width) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = str_pad($v, $width, " ", STR_PAD_LEFT);
        return $this;
    }
    function rjust($width) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = str_pad($v, $width, " ", STR_PAD_RIGHT);
        return $this;
    }
    function cut($str) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = str_replace($str, '', $v);
        return $this;
    }
    function date($format) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = date($format, (int)$v);
        return $this;
    }
    function time($format) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = date($format, mktime($v));
        return $this;

    }
    function _filesizeformat($size) {
        static $prefixes = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
        if (empty($size) || !is_numeric($size)) return "0 $prefixes[0]";
        for ($i=0; round($size, 1) >= 1024 && $i<5; $size /= 1024, ++$i);
        if ($i==0) return "$size $prefixes[0]";
        return sprintf('%01.1f %s', $size, $prefixes[$i]);
    }
    function filesizeformat() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_filesizeformat($v);
        return $this;
    }
    function yesno($yes=null, $no=null, $maybe=null) {
        $choices = array(
            true => $yes ? $yes : 'yes',
            false => $no ? $no : 'no',
            null => $maybe ? $maybe : 'maybe',
        );
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) { 
            if (!is_array($v) || $this->v === null) {
                if ($v === null && $no && !$maybe) $v = False;
                if ($v !== null) $v = (bool)$v;
                $v = $choices[$v];
            }
        }
        return $this;
    }
    function wordwrap($width) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = wordwrap($v, $width, "\n", true);
        return $this;
    }
    function wordcount() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = str_word_count($v, 0, '0123456789');
        return $this;
    }
    function len() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = strlen($v);
        return $this;
    }
    function stringformat($format) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = sprintf("%$format", $v);
        return $this;
    }
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
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = strtr($v, $replace_pairs);
        return $this;
    }
    function first() {
        if (is_array($this->v)) {
            if (empty($this->v)) throw new Chano_ValueIsEmptyError;
            reset($this->v);
            $this->v = current($this->v);
        } elseif ($this->v instanceof stdClass 
        || $this->v instanceof Traversable) {
            $has_value = false;
            foreach ($this->v as $v) {
                $has_value = true;
                $this->v = $v;
                break;
            }
            if (!$has_value) throw new Chano_ValueIsEmptyError;
        } else {
            throw new Chano_TypeNotTraversableError;
        }
        
        return $this;
    }
    function fixampersands() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
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
    function floatformat($ds=null) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_floatformat($v, $ds);
        return $this;
    }
    function _getdigit($v, $n) {
            if (!intval($v) || !intval($n)) return $v;
            $v_s = (string)$v;
            if (!isset($v_s[$n-1])) return $v;
            else return $v_s[$n-1];
        }
    function getdigit($n) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_getdigit($v, $n);
        return $this;
    }
    function lower() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = mb_strtolower($v, self::$encoding);
        return $this;
    }
    function title() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) {
            if (!is_array($v) || $this->v === null) {
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
    function urlize() {
        $this->autoescape_off_until_tostring = true;
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_urlize($v);
        return $this;
    }
    private function _urlizetrunc_cb($ms) {
        $len = $this->_urlizetrunc_len;
        if ($len <= 3) return $ms[1] . '...' . $ms[3];
        if (strlen($ms[2]) <= $len) return $ms[0];
        return $ms[1] . substr($ms[2], 0, $len-3) . '...' . $ms[3];
    }
    function _urlizetrunc($v) {
        $v = self::_urlize($v);
        return preg_replace_callback('#(<a href=.*">)([^<]*)(</a>)#Uis', 
                   array($this, '_urlizetrunc_cb'), $v);
    }
    function urlizetrunc($len) {
        // TODO: This passes the tests but also truncates existing html
        // addresses which is probably not the desired behavior. Change _urlize
        // to support truncate.
        $this->autoescape_off_until_tostring = true;
        $this->_urlizetrunc_len = $len;
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_urlizetrunc($v, $len);
        return $this;
    }
    function truncatewords($n) {
        // Thanks banderson623: http://snippets.dzone.com/posts/show/412.
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) {
            if (!is_array($v) || $this->v === null) {
                $parts = explode(' ', $v);
                if(count($parts) > $n && $n>0)
                    $v = implode(' ', array_slice($parts, 0, $n)) . ' ...';
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
                ));
            }
            ++$found_words_len;
        }
        return $v;
    }
    function truncatewordshtml($n) {
        $this->autoescape_off_until_tostring = true;
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_truncatewordshtml($v, $n);
        return $this;
    }
    function truncatechars($length, $ellipsis='...') {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v)
            if (!is_array($v) || $this->v === null)
                if (strlen($v) > $length)
                    $v = substr($v, 0, $length - strlen($ellipsis)) . $ellipsis;
        return $this;
    }
    function urlencode() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = urlencode($v);
        return $this;
    }
    function iriencode() {
        // TODO: Keep this? Suspicious!
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = str_replace('+', '%20', urlencode(urldecode($v)));
        return $this;
    }
    private function _slice($v, $str) {
        $ps = explode(':', $str);
        $count = count($ps);
        if ($count == 1) {
            $a = $ps[0];
            if ($a == 0) return '';
            else return mb_substr($v, 0, $a, self::$encoding);
        }
        if ($count == 2) {
            list($a,$b) = $ps;
            return mb_substr($v, $a, $b-$a, self::$encoding);
        }
        if ($count == 3) {
            list ($a, $dummy, $b) = $ps;
            $v = mb_substr($v, $a, strlen($v), self::$encoding);
            $len = strlen($v) - 1;
            $result = '';
            for ($i=$a; $i<=$len; $i+=$b) $result .= $v[$i];
            return $result;
        }
        return '';
    }
    function slice($str) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_slice($v, $str);
        return $this;
    }
    private function _linenumbers($v) {
        $lines = explode("\n", trim($v));
        $strlen = strlen(count($lines));
        $string = '';
        $i = 1;
        foreach ($lines as $line) {
            $string .= 
                str_pad($i, $strlen, '0', STR_PAD_LEFT)
                . '. ' . $line . "\n";
            ++$i;
        }
        return $string;
    }
    function linenumbers() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_linenumbers($v);
        return $this;
    }
    function removetags() {
        $this->autoescape_off_until_tostring = true;
        $args = func_get_args();
        if (empty($args)) return $this;
        $tags = implode('|', $args);
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
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
    function linebreaks() {
        $this->autoescape_off_until_tostring = true;
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $this->_linebreaks($v);
        return $this;
    }
    function linebreaksbr() {
        $this->autoescape_off_until_tostring = true;
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = nl2br($v);
        return $this;
    }
    function join($glue=', ') {
        if (is_scalar($this->v)) return $this;
        $this->v = implode($glue, $this->v);
        return $this;
    }
    function makelist() {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) {
            if (!is_array($v) || $this->v === null) {
                $_vs = str_split((string)$v);
                if (is_int($v)) foreach ($_vs as &$_v) $_v = (int)$_v;
                $v = $_vs;
            }
        }
        return $this;
    }
    function slugify() {
        // Thanks Borek! http://drupal.org/node/63924.
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) {
            if (!is_array($v) || $this->v === null) {
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
    function phone2numeric() {
        static $replace_pairs = array('a' => '2', 'b' => '2', 'c' => '2',
            'd' => '3', 'e' => '3', 'f' => '3', 'g' => '4', 'h' => '4',
            'i' => '4', 'j' => '5', 'k' => '5', 'l' => '5', 'm' => '6',
            'n' => '6', 'o' => '6', 'p' => '7', 'q' => '7', 'r' => '7',
            's' => '7', 't' => '8', 'u' => '8', 'v' => '8', 'w' => '9',
            'x' => '9', 'y' => '9', 'z' => '9');
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = strtr(strtolower($v), $replace_pairs);
        return $this;
    }
}

require realpath(dirname(__FILE__) . '/lib/iterators.php');