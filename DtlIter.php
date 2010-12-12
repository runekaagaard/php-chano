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

    function urlize() {
        $v = make_clickable($this->v);
        $protocols = array ('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn');
        if (strpos($v, 'www') !== FALSE) {
            foreach ($protocols as $p) $v = str_replace(">$p://", '>', $v);
        }
        $v = preg_replace_callback('#(^| )[a-z0-9-_+]+\.(com|org|net)#', function($ms) {
            return "<a href=\"http://$ms[0]\" rel=\"nofollow\">$ms[0]</a>";
        }, $v);
        return $v;
    }
    function urlencode() {
        return $this->filter_apply(function($v) {
            return urlencode($v);
        });
    }
}

/**
 * Checks and cleans a URL.
 *
 * A number of characters are removed from the URL. If the URL is for displaying
 * (the default behaviour) amperstands are also replaced. The 'clean_url' filter
 * is applied to the returned cleaned URL.
 *
 * @since 2.8.0
 * @uses wp_kses_bad_protocol() To only permit protocols in the URL set
 *		via $protocols or the common ones set in the function.
 *
 * @param string $url The URL to be cleaned.
 * @param array $protocols Optional. An array of acceptable protocols.
 *		Defaults to 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' if not set.
 * @param string $_context Private. Use esc_url_raw() for database usage.
 * @return string The cleaned $url after the 'clean_url' filter is applied.
 */
function esc_url( $url, $protocols = null, $_context = 'display' ) {
	$original_url = $url;

	if ( '' == $url )
		return $url;
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
	$strip = array('%0d', '%0a', '%0D', '%0A');
	$url = _deep_replace($strip, $url);
	$url = str_replace(';//', '://', $url);
	/* If the URL doesn't appear to contain a scheme, we
	 * presume it needs http:// appended (unless a relative
	 * link starting with / or a php file).
	 */
	if ( strpos($url, ':') === false &&
		substr( $url, 0, 1 ) != '/' && substr( $url, 0, 1 ) != '#' && !preg_match('/^[a-z0-9-]+?\.php/i', $url) )
		$url = 'http://' . $url;

	// Replace ampersands and single quotes only when displaying.
	if ( 'display' == $_context ) {
		$url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
		$url = str_replace( "'", '&#039;', $url );
	}

	if ( !is_array($protocols) )
		$protocols = array ('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet', 'mms', 'rtsp', 'svn');
	#if ( wp_kses_bad_protocol( $url, $protocols ) != $url )
		#return '';
    return $url;
	#return apply_filters('clean_url', $url, $original_url, $_context);
}

/**
 * Callback to convert URI match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URI address.
 */
function _make_url_clickable_cb($matches) {
	$url = $matches[2];

	$url = esc_url($url);
	if ( empty($url) )
		return $matches[0];

	return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>";
}

/**
 * Callback to convert URL match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URL address.
 */
function _make_web_ftp_clickable_cb($matches) {
	$ret = '';
	$dest = $matches[2];
	$dest = 'http://' . $dest;
	$dest = esc_url($dest);
	if ( empty($dest) )
		return $matches[0];

	// removed trailing [.,;:)] from URL
	if ( in_array( substr($dest, -1), array('.', ',', ';', ':', ')') ) === true ) {
		$ret = substr($dest, -1);
		$dest = substr($dest, 0, strlen($dest)-1);
	}
	return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>$ret";
}

/**
 * Callback to convert email address match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with email address.
 */
function _make_email_clickable_cb($matches) {
	$email = $matches[2] . '@' . $matches[3];
	return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}

/**
 * Convert plaintext URI to HTML links.
 *
 * Converts URI, www and ftp, and email addresses. Finishes by fixing links
 * within links.
 *
 * @since 0.71
 *
 * @param string $ret Content to convert URIs.
 * @return string Content with converted URIs.
 */
function make_clickable($ret) {
	$ret = ' ' . $ret;
	// in testing, using arrays here was found to be faster
	$ret = preg_replace_callback('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/=?@\[\](+-]|[.,;:](?![\s<]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is', '_make_url_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', '_make_web_ftp_clickable_cb', $ret);
	$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
	// this one is not in an array because we need it to run last, for cleanup of accidental links within links
	$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
	$ret = trim($ret);
	return $ret;
}

/**
 * Adds rel nofollow string to all HTML A elements in content.
 *
 * @since 1.5.0
 *
 * @param string $text Content that may contain HTML A elements.
 * @return string Converted content.
 */
function wp_rel_nofollow( $text ) {
	// This is a pre save filter, so text is already escaped.
	$text = stripslashes($text);
	$text = preg_replace_callback('|<a (.+?)>|i', 'wp_rel_nofollow_callback', $text);
	$text = esc_sql($text);
	return $text;
}

/**
 * Callback to used to add rel=nofollow string to HTML A element.
 *
 * Will remove already existing rel="nofollow" and rel='nofollow' from the
 * string to prevent from invalidating (X)HTML.
 *
 * @since 2.3.0
 *
 * @param array $matches Single Match
 * @return string HTML A Element with rel nofollow.
 */
function wp_rel_nofollow_callback( $matches ) {
	$text = $matches[1];
	$text = str_replace(array(' rel="nofollow"', " rel='nofollow'"), '', $text);
	return "<a $text rel=\"nofollow\">";
}

/**
 * Perform a deep string replace operation to ensure the values in $search are no longer present
 *
 * Repeats the replacement operation until it no longer replaces anything so as to remove "nested" values
 * e.g. $subject = '%0%0%0DDD', $search ='%0D', $result ='' rather than the '%0%0DD' that
 * str_replace would return
 *
 * @since 2.8.1
 * @access private
 *
 * @param string|array $search
 * @param string $subject
 * @return string The processed string
 */
function _deep_replace( $search, $subject ) {
	$found = true;
	$subject = (string) $subject;
	while ( $found ) {
		$found = false;
		foreach ( (array) $search as $val ) {
			while ( strpos( $subject, $val ) !== false ) {
				$found = true;
				$subject = str_replace( $val, '', $subject );
			}
		}
	}

	return $subject;
}
