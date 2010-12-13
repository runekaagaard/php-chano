<?php
// Errors on.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Exceptions.
class ReadOnlyError extends Exception {}
class NotImplementedError extends Exception {}
class TypeNotArrayError extends Exception {}

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
 *    getdigit
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

    // Filters util functions.
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

    // Filter flags commands.
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
            return mb_strtoupper($v, 'utf-8');
        });
    }
    function center($width) {
        return $this->filter_apply(function($v) use($width) {
            return str_pad($v, $width, " ", STR_PAD_BOTH);
        });
    }
    function ljust($width) {
        return $this->filter_apply(function($v) use($width) {
            return str_pad($v, $width, " ", STR_PAD_LEFT);
        });
    }
    function rjust($width) {
        return $this->filter_apply(function($v) use($width) {
            return str_pad($v, $width, " ", STR_PAD_RIGHT);
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
            $prefixes = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
            if (empty($size) || !is_numeric($size)) return "0 $prefixes[0]";
            for ($i=0; round($size, 1) >= 1024 && $i<5; $size /= 1024, ++$i);
            if ($i==0) return "$size $prefixes[0]";
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
        // Thanks Heine!: http://drupal.org/node/479368#pift-results-479368-3198886-3198886.
        $replace_pairs = array('\\' => '\u005C', '"' => '\u0022', "\x00" => '\u0000', "\x01" => '\u0001', "\x02" => '\u0002', "\x03" => '\u0003', "\x04" => '\u0004', "\x05" => '\u0005', "\x06" => '\u0006', "\x07" => '\u0007', "\x08" => '\u0008', "\x09" => '\u0009', "\x0a" => '\u000A', "\x0b" => '\u000B', "\x0c" => '\u000C', "\x0d" => '\u000D', "\x0e" => '\u000E', "\x0f" => '\u000F', "\x10" => '\u0010', "\x11" => '\u0011', "\x12" => '\u0012', "\x13" => '\u0013', "\x14" => '\u0014', "\x15" => '\u0015', "\x16" => '\u0016', "\x17" => '\u0017', "\x18" => '\u0018', "\x19" => '\u0019', "\x1a" => '\u001A', "\x1b" => '\u001B', "\x1c" => '\u001C', "\x1d" => '\u001D', "\x1e" => '\u001E', "\x1f" => '\u001F', "'" => '\u0027', '<' => '\u003C', '>' => '\u003E', '&' => '\u0026', '/' => '\u002F', "\xe2\x80\xa8" => '\u2028', "\xe2\x80\xa9" => '\u2029',);       
        return $this->filter_apply(function($v) use($replace_pairs) {
            return strtr($v, $replace_pairs);
        });
    }
    function first() {
        if (!is_array($this->v)) throw new TypeNotArrayError;
        reset($this->v);
        $this->v = current($this->v);
        return $this;
    }
    function fixampersands() {
        return $this->filter_apply(function($v) {
            return strtr($v, '&', '&amp;');
        });
    }
    function floatformat($ds=null) {
        return $this->filter_apply(function($v) use($ds) {
            if (!is_numeric($v)) return '';
            if (!is_numeric($ds)) $ds = '-1';
            $ds = (string)$ds;
            $hide_zeros = True;
            if ($ds) {
                if ($ds[0] == '-') $ds = ltrim($ds, '-');
                else $hide_zeros = False;
            }
            if ($hide_zeros && (int)$v == $v) return $v;
            $ds = $ds ? $ds : 1;
            return sprintf("%.{$ds}f", round($v, $ds));
        });
    }
    function getdigit($n) {
        return $this->filter_apply(function($v) use($n) {
            if (!intval($v) || !intval($n)) return $v;
            $v_s = (string)$v;
            if (!isset($v_s[$n-1])) return $v;
            else return $v_s[$n-1];
        });
    }
    function lower() {
        return $this->filter_apply(function($v) {
            return mb_strtolower($v, 'utf-8');
        });
    }
    function title() {
        return $this->filter_apply(function($v) {
            return mb_convert_case($v, MB_CASE_TITLE, "utf-8");
        });
    }
    static function _urlize($v) {
        // Thanks Wordpress (I guess).
        $v = preg_replace_callback('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff' 
             . '\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<'
             .'.,;:]|$)|\)))+)#is', function($ms) {
                return empty($ms[2]) ? $ms[0]
                    : "$ms[1]<a href=\"$ms[2]\" rel=\"nofollow\">$ms[2]</a>";}
             , " $v");
        $v = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.'.
             '\-;:=,?@\[\]+]+)#is', function ($ms) { return empty($ms[2])
            ? $ms[0]
            : sprintf('<a href="http://%1$s" rel="nofollow">http://%1$s</a>',
            trim($ms[2], '.,;:)')); }, $v);
        $v = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+'
             . '[0-9a-z]{2,})#i', function ($ms) { return
             "$ms[1]<a href=\"mailto:$ms[2]@$ms[3]\">$ms[2]@$ms[3]</a>"; }, $v);
        $v = trim(preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i",
             "$1$3</a>", $v));
        if (strpos($v, 'www') !== FALSE)
            $v = str_replace(array ('>http://', '>https://'), '>', $v);
        return  preg_replace_callback('#(^| )[a-z0-9-_+]+\.(com|org|net)#',
             function($ms) {
                return "<a href=\"http://$ms[0]\" rel=\"nofollow\">$ms[0]</a>";
             }, $v);
    }
    function urlize() {
        $autosecape = $this->autoescape;
        $this->autoescape_off();
        return $this->filter_apply(function($v) {
            return DtlIter::_urlize($v);
        });
        $this->autoescape = $autosecape;
        return $this;
    }

    function urlencode() {
        return $this->filter_apply(function($v) {
            return urlencode($v);
        });
    }
}