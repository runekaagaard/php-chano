<?php
// Errors on.
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 1);

// Includes.
require realpath(dirname(__FILE__) . '/lib/text.php');

// Exceptions.
class ReadOnlyError extends Exception {}
class NotImplementedError extends Exception {}
class TypeNotArrayError extends Exception {}

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
 * 4) Make it work for array of objects too.
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
class DtlIter implements Iterator, ArrayAccess {
    /**
     * The encoding used in charset sensitive filters.
     * @var string
     */
    static $encoding = 'utf-8';
    /**
     * The value of the current item after filters has been applied.
     * @var scalar/array
     */
    public $v = self::INITIAL;
    
    // Private values.
    const INITIAL = -9892895829385;
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
     *   Array of arrays or an iterator giving arrays. Must be countable.
     * @param array $options
     *   Supported options are:
     *     'encoding': Defaults to 'utf-8'.
     */
    function  __construct($items, array $options=array()) {
        $default = array('encoding' => self::$encoding);
        $options = array_merge($default, $options);
        self::$encoding = $options['encoding'];
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
        $s = !$this->autoescape_off_until_tostring && $escape
            ? htmlspecialchars((string)$this->v, ENT_NOQUOTES, self::$encoding)
            : (string)$this->v;
        $this->autoescape_off_until_tostring = FALSE;
        return (string)$s;
    }
    
    /*
     * Implementation of Iterator interface.
     */
    
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
        if ($this->v == self::INITIAL) $this->v = $this->current[$o];
        else $this->v = $this->v[$o];
        $this->lookup_add($o);
        return $this;
    }
    function offsetExists($offset) { throw new NotImplementedError; }
    function offsetSet($offset, $value) { throw new ReadOnlyError; }
    function offsetUnset($offset) { throw new ReadOnlyError; }

    /**
     * Implementation of __get magic method.
     *
     * @param string $name
     * @return mixed
     */
    function  __get($name) { return $this->offsetGet($name); }

    /**
     * Resets settings for filters that does not wait for the __toString()
     * method being called to return calue.
     *
     * @todo Fix code duplication with __toString() and out() methods.
     * @return mixed
     */
    function filter_reset() {
        $value = $this->v;
        $this->v = self::INITIAL;
        $path = $this->lookup_path_reset();
        $this->autoescape_off_until_tostring = FALSE;
        return $value;
    }
    /**
     * Appplies a filter function to the value of current row taking into
     * account if said value is scalar or arrary.
     *
     * @param function $function
     * @return $this
     */
    function filter_apply($function) {
        if (!is_array($this->v)) $vs = array(&$this->v); else $vs = &$this->v;
        foreach($vs as &$v) 
            if (!is_array($v) || $this->v === null)
                $v = $function($v);
        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Below this line are the methods that are part of the template api.     //
    ////////////////////////////////////////////////////////////////////////////

    /*
     * Flags.
     *
     * Sets one or more boolean values on the DtlIter class. Chainable.
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
    function divisibleby($divisor) {
        return ($this->filter_reset() % $divisor) === 0;
    }

    /*
     * Returns.
     *
     * Returns value of current item in various ways. Unchainable.
     */

    function safe() { return $this->out(false); }
    function forceescape() {
        return htmlentities($this->filter_reset(), null, self::$encoding);
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
        $this->v = $this->count - $this->i + 1;
        return $this;
    }
    function revcounter0() {
        $this->v = $this->count - $this->i;
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
        return $v;
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
    function length() {
        if (is_scalar($this->v)) $this->v = strlen((string)$this->v);
        else $this->v = count($this->v);
        return $this;
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
            return mb_strtoupper($v, DtlIter::$encoding);
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
    function time($format) {
        return $this->filter_apply(function($v) use ($format) {
            return date($format, mktime($v));
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
            $hide_zeros = true;
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
            return mb_strtolower($v, DtlIter::$encoding);
        });
    }
    function title() {
        return $this->filter_apply(function($v) {
            return mb_convert_case($v, MB_CASE_TITLE, DtlIter::$encoding);
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
                : sprintf('<a href="http://%1$s" rel="nofollow">http://%1$s</a>'
                , trim($ms[2], '.,;:)')); }, $v);
        $v = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+'
             . '[0-9a-z]{2,})#i', function ($ms) { return
                    "$ms[1]<a href=\"mailto:$ms[2]@$ms[3]\">$ms[2]@$ms[3]</a>";
                }, $v);
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
        $this->autoescape_off_until_tostring = true;
        return $this->filter_apply(function($v) {
            return DtlIter::_urlize($v);
        });
    }
    function urlizetrunc($len) {
        // TODO: This passes the tests but also truncates existing html
        // addresses which is probably not the desired behavior. Change _urlize
        // to support truncate.
        $this->autoescape_off_until_tostring = true;
        return $this->filter_apply(function($v) use ($len) {
            $v = DtlIter::_urlize($v);
            return preg_replace_callback('#(<a href=.*">)([^<]*)(</a>)#Uis', 
                function($ms) use ($len) {
                    if ($len <= 3) return $ms[1] . '...' . $ms[3];
                    if (strlen($ms[2]) <= $len) return $ms[0];
                    return $ms[1] . substr($ms[2], 0, $len-3) . '...' . $ms[3];
                }, $v);});
    }
    function truncatewords($n) {
        // Thanks banderson623: http://snippets.dzone.com/posts/show/412.
        return $this->filter_apply(function($v) use($n) {
            $parts = explode(' ', $v);
            if(count($parts) > $n && $n>0)
                return implode(' ', array_slice($parts, 0, $n)) . ' ...';
            else return $v;
        });
    }
    function truncatewordshtml($n) {
        $this->autoescape_off_until_tostring = true;
        return $this->filter_apply(function($v) use($n) {
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
                    return dtl_truncate($v, $found_words_len+4, array(
                        'ending' => ' ...', 'exact' => true, 'html' => true,
                    ));
                }
                ++$found_words_len;
            }
            return $v;
        });
    }
    function urlencode() {
        return $this->filter_apply(function($v) {
            return urlencode($v);
        });
    }
    function iriencode() {
        // TODO: Keep this? Suspicious!
        return $this->filter_apply(function($v) {
            return str_replace('+', '%20', urlencode(urldecode($v)));
        });
    }
    function slice($str) {
        return $this->filter_apply(function($v) use($str) {
            $ps = explode(':', $str);
            $count = count($ps);
            if ($count == 1) {
                $a = $ps[0];
                if ($a == 0) return '';
                else return mb_substr($v, 0, $a, DtlIter::$encoding);
            }
            if ($count == 2) {
                list($a,$b) = $ps;
                return mb_substr($v, $a, $b-$a, DtlIter::$encoding);
            }
            if ($count == 3) {
                list ($a, $dummy, $b) = $ps;
                $v = mb_substr($v, $a, strlen($v), DtlIter::$encoding);
                $len = strlen($v) - 1;
                $result = '';
                for ($i=$a; $i<=$len; $i+=$b) $result .= $v[$i];
                return $result;
            }
            return '';
        });
    }
    function linenumbers() {
        // TODO: Make pretty. Do you feel pretty, well do you punk?.
        return $this->filter_apply(function($v) {
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
        });
    }
    function removetags() {
        $this->autoescape_off_until_tostring = true;
        $args = func_get_args();
        if (empty($args)) return $this;
        $tags = implode('|', $args);
        return $this->filter_apply(function($v) use($tags) {
            return preg_replace("/<\\/?($tags)(\\s+.*?>|>)/Uis", '', $v);
        });
    }
    function linebreaks() {
        $this->autoescape_off_until_tostring = true;
        return $this->filter_apply(function($v) {
            $v = preg_replace('#\r\n|\r|\n#', "\n", $v);
            $paragrahps = preg_split('#\n{2,}#', $v);
            $html = '';
            foreach ($paragrahps as $p) 
                $html .= '<p>' . str_replace("\n", '<br />', $p) . '</p>';
            return $html;
        });
    }
    function linebreaksbr() {
        $this->autoescape_off_until_tostring = true;
        return $this->filter_apply(function($v) {
            return nl2br($v);
        });
    }
    function join($glue) {
        if (is_scalar($this->v)) return $this;
        $this->v = implode($glue, $this->v);
        return $this;
    }
    function makelist() {
        return $this->filter_apply(function($v) {
            $vs = str_split((string)$v);
            if (is_int($v)) foreach ($vs as &$v) $v = (int)$v;
            return $vs;
        });
    }
    function slugify() {
        // Thanks Borek! http://drupal.org/node/63924.
        return $this->filter_apply(function($v) {
            $v = str_replace(array(',', '\''), '', $v);
            $v = preg_replace('#[^\\pL0-9_]+#u', '-', $v);
            $v = preg_replace('#[-]{2,}#', '-', $v);
            $v = trim($v, "-");
            $v = iconv(DtlIter::$encoding, "us-ascii//TRANSLIT", $v);
            $v = strtolower($v);
            $v = preg_replace('#[^-a-z0-9_]+#', '', $v);
            return $v;
        });
    }
    function phone2numeric() {
        return $this->filter_apply(function($v) {
            static $replace_pairs = array('a' => '2', 'b' => '2', 'c' => '2',
                'd' => '3', 'e' => '3', 'f' => '3', 'g' => '4', 'h' => '4',
                'i' => '4', 'j' => '5', 'k' => '5', 'l' => '5', 'm' => '6',
                'n' => '6', 'o' => '6', 'p' => '7', 'q' => '7', 'r' => '7',
                's' => '7', 't' => '8', 'u' => '8', 'v' => '8', 'w' => '9',
                'x' => '9', 'y' => '9', 'z' => '9');
            return strtr(strtolower($v), $replace_pairs);
        });
    }
}