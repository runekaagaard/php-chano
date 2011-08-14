.. highlight:: php

Chano functions
===============

Stub. Describe the different types here.

Flags
_____

Sets one or more boolean values on the Chano class. Chainable.

.. _autoescapeon:

autoescapeon()
++++++++++++++

Switches on auto-escaping behavior. This only has any effect after the
:ref:`autoescapeoff` method has been called as the default behavior of
Chano is to escape all output.

When auto-escaping is in effect, all variable content has HTML escaping
applied to it before placing the result into the output (but after any
filters have been applied).

The only exceptions to this rule is the :ref:`safe` method.

Sample usage::

    <?foreach(new Chano($items) as $item)?>
        <?=$item->autoescapeoff()->body?>
        <?=$item->comments?>
        <?=$item->autoescapeon()?>
        <?=$item->title?>
    <?endforeach?>

*Returns*
  ``Chano instance``

.. _autoescapeoff:

autoescapeoff()
+++++++++++++++

Switches off the default auto-escaping behavior. This means that all
output until the end or until :ref:`autoescapeon` is called will not be
escaped unless :ref:`escape` is specifically called.

Sample usage::

    <?foreach(new Chano($items) as $item)?>
        <?=$item->autoescapeoff()->body?>
        <?=$item->comments?> <!-- body and comments are not escaped -->
        <?=$item->autoescapeon()?>
        <?=$item->title?> <!-- title is escaped -->
    <?endforeach?>

*Returns*
  ``Chano instance``

.. _escape:

escape()
++++++++

Forces escaping on the next output, i.e. when __toString() is called,
overruling the :ref:`autoescapeoff` flag a single time.

* Sample usage::

    <?foreach(new Chano($items) as $item)?>
        <?=$item->autoescapeoff()?>
        <?=$item->escape()->body?> <!-- body is escaped -->
        <?=$item->comments?> <!-- comments is not -->
    <?endforeach?>

*Returns*
  ``Chano instance``


Filters
_____

Modifies the value of the current item. Chainable.

.. _pluralize:

pluralize($plural='s', $singular=null)
++++++++++++++++++++++++++++++++++++++

Returns a plural suffix if the value is not 1. By default,
this suffix is ``'s'``.

Example::

    You have <?$item->num_messages?> message<?$item->num_messages->pluralize()?>.

If ``num_messages`` is ``1``, the output will be ``You have 1 message.``
If ``num_messages`` is ``2``  the output will be ``You have 2 messages.``

For words that require a suffix other than ``'s'``, you can provide an
alternate suffix as the first argument to the filter.

Example::

    You have <?$item->num_walruses?> walrus<?$item->num_messages->pluralize("es")?>.

For words that don't pluralize by simple suffix, you can specify both a
plural and singular suffix as arguments.

Example::

    You have <?$item->num_cherries?> cherr<?$item->num_cherries->pluralize("y", "ies")?>.

Arguments

- ``string $plural``
- ``string $singular``

*Returns*
  ``Chano instance``

.. _unorderedlist:

unorderedlist()
+++++++++++++++

Recursively takes a self-nested list and returns an HTML unordered list -
WITHOUT opening and closing <ul> tags.

The list is assumed to be in the proper format. For example, if ``var``
contains::

    array(
        'States', array(
            'Kansas', array(
                  'Lawrence', 'Topeka'
            ), 'Illinois'
        )
    );

then ``<?=$item->var->unordered_list()?>`` would render::

    <li>States
    <ul>
            <li>Kansas
            <ul>
                    <li>Lawrence</li>
                    <li>Topeka</li>
            </ul>
            </li>
            <li>Illinois</li>
    </ul>
    </li>

*Returns*
  ``Chano instance``

.. _striptags:

striptags()
+++++++++++

Strips all [X]HTML tags.

For example::

    <?=$item->value->striptags()?>

If ``$value`` is
``"<b>Joel</b> <button>is</button> a <span>slug</span>"``, the output
will be ``"Joel is a slug"``.

*Returns*
  ``Chano instance``

.. _vd:

vd()
++++

``var_dumps()`` the content of the current value to screen.



*Returns*
  ``Chano instance``

.. _now:

now($format)
++++++++++++

Display the current date and/or time, using a format according to the
given string. Such string can contain format specifiers characters as
described in the :ref:`date` filter section.

Example::

    Current time is: <?=$item->now("F j, Y, g:i a")?>

This would display as ``"Current time is: March 10, 2001, 5:16 pm"``.

*Returns*
  ``Chano instance``

.. _widthratio:

widthratio($max_in, $max_out)
+++++++++++++++++++++++++++++

For creating bar charts and such, this tag calculates the ratio of a
given value to a maximum value, and then applies that ratio to a
constant.

For example::

    <img src="bar.gif" height="10" width="<?=$item->value->widthratio(175, 100)?>" />

Above, if ``value`` is 175 and, the image in the above example will be
88 pixels wide
(because 175/200 = .875; .875 * 100 = 87.5 which is rounded up to 88).

Arguments

- ``numeric $max_in - The maximum before value.``
- ``numeric $max_out - The maximum after value.``

*Returns*
  ``Chano instance``

.. _add:

add($amount)
++++++++++++

Adds the given amount to the current value.

If ``value`` is 2, then ``<?=$item->value->add(2)?>`` will render 4.

Arguments

- ``numeric $amount``

*Returns*
  ``Chano instance``

.. _addslashes:

addslashes()
++++++++++++

Adds slashes before quotes. Useful for escaping strings in CSV, for
example.

For example::

    <?=$item->value->addslashes()?>

If ``value`` is ``"I'm using Chano"``, the output will be
``"I\'m using Chano"``
.

*Returns*
  ``Chano instance``

.. _capfirst:

capfirst()
++++++++++

Capitalizes the first character of the value.

For example::

    <?=$item->value->capfirst()?>

If ``value`` is ``"chano"``, the output will be ``"Chano"``.

*Returns*
  ``Chano instance``

.. _upper:

upper()
+++++++

Converts a string into all uppercase.

For example::

    <?=$item->value->upper()?>

If ``value`` is ``"Joel is a slug"``, the output will be
``"JOEL IS A SLUG"``.

*Returns*
  ``Chano instance``

.. _center:

center($width)
++++++++++++++

Centers the value in a field of a given width.

For example::

    <?=$item->value->center(15)?>

If ``value`` is ``"Chano!"``, the output will be ``"     Chano!    "``.

Arguments

- ``int $width``

*Returns*
  ``Chano instance``

.. _ljust:

ljust($width)
+++++++++++++

Left-aligns the value in a field of a given width.

For example::

    "<?=$item->value->ljust(10)?>"

If value is Chano!, the output will be "Chano!    ".

Arguments

- ``int $width``

*Returns*
  ``Chano instance``

.. _rjust:

rjust($width)
+++++++++++++

Right-aligns the value in a field of a given width.

For example::

    "<?=$item->value->rjust(10)?>"

If value is Chano!, the output will be "    Chano!".

Arguments

- ``int $width``

*Returns*
  ``Chano instance``

.. _cut:

cut($string)
++++++++++++

Removes all values of passed argument from the current value.

For example::

    <?=$item->value->cut(" ")?>

If ``value`` is ``"String with spaces"``, the output will be
``"Stringwithspaces"``.

Arguments

- ``string $string - The string to remove.``

*Returns*
  ``Chano instance``

.. _date:

date($format)
+++++++++++++

Formats a date according to the given format.

The format must be in a syntax supported by the
`strftime() <http://php.net/manual/en/function.strftime.php>`_ function.

The used timezone is the one found by the
`date_default_timezone_get() <http://www.php.net/manual/en/function.date-default-timezone-get.php>`_
function.

Uses the current locale as set by the `setlocale <http://php.net/manual/en/function.setlocale.php>`_
function.

The input value can be a digit, which will be interpreted as a linux
timestamp, a ``DateTime()`` class or a string
`recognized by <http://www.php.net/manual/en/datetime.formats.php>`_ the
`strtotime() <http://php.net/manual/en/function.strtotime.php>`_
class.

For example::

    <?=$item->value->date("%d %B %Y")?>

If ``value`` is the string "2000-01-01", a DateTime object like
``new DateTime("2000-01-01")`` or the linux timestamp integer 946684800,
the output will be the string ``'01 January 2000'``.

Arguments

- ``string $format``

*Returns*
  ``Chano instance``

.. _filesizeformat:

filesizeformat()
++++++++++++++++

Format the value like a 'human-readable' file size (i.e. ``'13 KB'``,
``'4.1 MB'``, ``'102 bytes'``, etc).

For example::

    <?=$item->value(filesizeformat)?>

If ``value`` is 123456789, the output would be ``117.7 MB``.

*Returns*
  ``Chano instance``

.. _yesno:

yesno($yes=null, $no=null, $maybe=null)
+++++++++++++++++++++++++++++++++++++++

Given a string mapping values for true, false and (optionally) null,
returns one of those strings according to the value:

For example::

    <?=$item->value(filesizeformat("yeah", "no", "maybe"))?>

==========  ===========================  ==================================
Value       Arguments                    Outputs
==========  ===========================  ==================================
``true``    ``("yeah", "no", "maybe")``  ``yeah``
``false``   ``("yeah", "no", "maybe")``  ``no``
``null``    ``("yeah", "no", "maybe")``  ``maybe``
``null``    ``("yeah", "no")``           ``"no"`` (converts null to false
                                         if no mapping for null is given)
==========  ===========================  ==================================

Arguments

- ``string $yes``
- ``string $no``
- ``string $maybe``

*Returns*
  ``Chano instance``

.. _wordwrap:

wordwrap($width)
++++++++++++++++

Wraps words at specified line length.

For example::

    <?=$item->value->wordwrap(5)?>

If ``value`` is ``Joel is a slug``, the output would be::

    Joel
    is a
    slug

Arguments

- ``int $width - Number of characters at which to wrap the text.``

*Returns*
  ``Chano instance``

.. _wordcount:

wordcount()
+++++++++++

Returns the number of words.

For example::

    <?=$item->value->wordcount()?>

If ``value`` is ``"Joel is a slug"``, the output will be ``4``.

*Returns*
  ``Chano instance``

.. _stringformat:

stringformat($format)
+++++++++++++++++++++

Formats the variable according to the argument, a string formatting
specifier. This specifier uses the syntax of the
`sprintf <http://php.net/manual/en/function.sprintf.php>`_ function.

For example::

    <?=$item->value->stringformat:("%03d")?>

If ``value`` is ``1``, the output will be ``"001"``.

Arguments

- ``string $format``

*Returns*
  ``Chano instance``

.. _escapejs:

escapejs()
++++++++++

Escapes characters for use in JavaScript strings. This does *not* make
the string safe for use in HTML, but does protect you from syntax errors
when using templates to generate JavaScript/JSON.

For example::

    <?=$item->value->escapejs()?>

If ``value`` is ``"testing\r\njavascript \'string" <b>escaping</b>"``,
the output will be
``"testing\\u000D\\u000Ajavascript \\u0027string\\u0022 \\u003Cb\\u003Eescaping\\u003C/b\\u003E"``.

*Returns*
  ``Chano instance``

.. _first:

first()
+++++++

Outputs the first item in an array, stdClass or Traversable.

For example::

    <?=$item->value->first()?>

If ``value`` is the array ``array('a', 'b', 'c')``, the output will be
``'a'``.

*Returns*
  ``Chano instance``

.. _fixampersands:

fixampersands()
+++++++++++++++

.. note::

This is rarely useful as ampersands are automatically escaped.
    See :ref:`escape` for more information.

Replaces ampersands with ``&amp;`` entities.

For example::

    <?=$item->value->fixampersands()?>

If ``value`` is ``Tom & Jerry``, the output will be ``Tom &amp; Jerry``.

*Returns*
  ``Chano instance``

.. _floatformat:

floatformat($decimal_places=null)
+++++++++++++++++++++++++++++++++

When used without an argument, rounds a floating-point number to one
decimal place -- but only if there's a decimal part to be displayed.

For example:

============  ===================================  ========
``value``     Template                             Output
============  ===================================  ========
``34.23234``  ``<?=$item->value->floatformat()?>``  ``34.2``
``34.00000``  ``<?=$item->value->floatformat()?>``  ``34``
``34.26000``  ``<?=$item->value->floatformat()?>``  ``34.3``
============  ===================================  ========

If used with a numeric integer argument, ``floatformat`` rounds a number
to that many decimal places. For example:

============  ====================================  ==========
``value``     Template                              Output
============  ====================================  ==========
``34.23234``  ``<?=$item->value->floatformat(3)?>``  ``34.232``
``34.00000``  ``<?=$item->value->floatformat(3)?>``  ``34.000``
``34.26000``  ``<?=$item->value->floatformat(3)?>``  ``34.260``
============  ====================================  ==========

If the argument passed to ``floatformat`` is negative, it will round a
number to that many decimal places -- but only if there's a decimal part
to be displayed. For example:

============  =====================================  ==========
``value``     Template                               Output
============  =====================================  ==========
``34.23234``  ``<?=$item->value->floatformat(-3)?>``  ``34.232``
``34.00000``  ``<?=$item->value->floatformat(-3)?>``  ``34``
``34.26000``  ``<?=$item->value->floatformat(-3)?>``  ``34.260``
============  =====================================  ==========

Using ``floatformat`` with no argument is equivalent to using
``floatformat`` with an argument of ``-1``.

Arguments

- ``string $format``

*Returns*
  ``Chano instance``

.. _getdigit:

getdigit($number)
+++++++++++++++++

Given a whole number, returns the requested digit, where 1 is the
right-most digit, 2 is the second-right-most digit, etc. Returns the
original value for invalid input (if input or argument is not an integer,
or if argument is less than 1). Otherwise, output is always an integer.

For example::

    <?=$item->value->get_digit(2)?>

If ``value`` is ``123456789``, the output will be ``8``.

Arguments

- ``int $number``

*Returns*
  ``Chano instance``

.. _lower:

lower()
+++++++

Converts a string into all lowercase.

For example::

    <?=$item->value->lower()?>

If ``value`` is ``Still MAD At Yoko``, the output will be
``still mad at yoko``.

*Returns*
  ``Chano instance``

.. _title:

title()
+++++++

Converts a string into titlecase.

For example::

    <?=$item->value->title()?>

If ``value`` is ``"my first post"``, the output will be
``"My First Post"``.

*Returns*
  ``Chano instance``

.. _urlize:

urlize()
++++++++

Converts URLs in text into clickable links.

Works on links prefixed with ``http://``, ``https://``, or ``www.``. For
example, ``http://goo.gl/aia1t`` will get converted but ``goo.gl/aia1t``
won't.

Also works on domain-only links ending in one of the common ``.com``,
``.net``, or ``.org`` top level domains.
For example, ``chano.readthedocs.org`` will still get converted.

Links can have trailing punctuation (periods, commas, close-parens) and
leading punctuation (opening parens) and ``urlize`` will still do the
right thing.

Links generated by ``urlize`` have a ``rel="nofollow"`` attribute added
to them.

For example::

    <?=$item->value->urlize()?>

If ``value`` is ``"Check out chano.readthedocs.org"``, the output will be
``"Check out <a href="http://chano.readthedocs.org"
rel="nofollow">chano.readthedocs.org</a>"``.

*Returns*
  ``Chano instance``

.. _urlizetrunc:

urlizetrunc($len)
+++++++++++++++++

Converts URLs into clickable links just like urlize_, but truncates URLs
longer than the given character limit.

For example::

    <?=$item->value->urlizetrunc(15)?>

If ``value`` is ``"Check out chano.readthedocs.org"``, the output would
be ``'Check out <a href="http://chano.readthedocs.org"
rel="nofollow">chano.readth...</a>'``.

As with urlize_, this filter should only be applied to plain text.

Arguments

- ``int $length - Number of characters that link text should be truncated to, including the ellipsis that's added if truncation is necessary.``

*Returns*
  ``Chano instance``

.. _truncatewords:

truncatewords($number)
++++++++++++++++++++++

Truncates a string after a certain number of words.

For example::

    <?=$item->value->truncatewords(2)?>

If ``value`` is ``"Joel is a slug"``, the output will be
``"Joel is ..."``.

Arguments

- ``string $number - Number of words to truncate after.``

*Returns*
  ``Chano instance``

.. _truncatewordshtml:

truncatewordshtml($number)
++++++++++++++++++++++++++

Similar to `truncatewords`_, except that it is aware of HTML tags.

Any tags that are opened in the string and not closed before the
truncation point, are closed immediately after the truncation.

This is less efficient than ``truncatewords``, so should only be used
when it is being passed HTML text.

For example::

    <?=$item->value->truncatewords_html(2)?>

If ``value`` is ``"<p>Joel is a slug</p>"``, the output will be
``"<p>Joel is ...</p>"``.

Newlines in the HTML content will be preserved.

Arguments

- ``string $number - Number of words to truncate after.``

*Returns*
  ``Chano instance``

.. _truncatechars:

truncatechars($length, $ellipsis='...')
+++++++++++++++++++++++++++++++++++++++

Truncates a string if it is longer than the specified number of
characters. Truncated strings will end with an ellipsis, which defaults
to ("...") but can be set with the second argument.

For example::

    <?=$item->value->truncatechars(9)?>

If ``value`` is ``"Joel is a slug"``, the output will be ``"Joel i..."``.

Arguments

- ``int $length``
- ``string $ellipsis - Custom ellipsis character(s).``

*Returns*
  ``Chano instance``

.. _urlencode:

urlencode()
+++++++++++

Escapes a value for use in a URL.

For example::

    <?=$item->value->urlencode()?>

If ``value`` is ``"http://www.example.org/foo?a=b&c=d"``, the output will
be ``"http%3A//www.example.org/foo%3Fa%3Db%26c%3Dd"``.

*Returns*
  ``Chano instance``

.. _iriencode:

iriencode()
+++++++++++

Converts an IRI (Internationalized Resource Identifier) to a string that
is suitable for including in a URL. This is necessary if you're trying
to use strings containing non-ASCII characters in a URL.

It's safe to use this filter on a string that has already gone through
the ``urlencode`` filter.

For example::

    <?=$item->value->iriencode()?>

If ``value`` is ``"?test=1&me=2"``, the output will be
``"?test=1&amp;me=2"``.

*Returns*
  ``Chano instance``

.. _slice:

slice($slice_string)
++++++++++++++++++++

Returns a slice of a string.

Uses the same syntax as Python's list slicing. See
http://diveintopython.org/native_data_types/lists.html#odbchelper.list.slice
for an introduction.

Example::

    <?=$item->value->slice("0")?>
    <?=$item->value->slice("1")?>
    <?=$item->value->slice("-1")?>
    <?=$item->value->slice("1:2")?>
    <?=$item->value->slice("1:3")?>
    <?=$item->value->slice("0::2")?>

If ``value`` is ``"abcdefg"``, the outputs will be
``""``, ``"a"``, ``"abcdef"``, ``"b"``, ``"bc"`` and ``"aceg"``
respectively.

Arguments

- ``string $slice_string``

*Returns*
  ``Chano instance``

.. _linenumbers:

linenumbers()
+++++++++++++

Displays text with line numbers.

For example::

    <?=$item->value->linenumbers()?>

If ``value`` is::

    one
    two
    three

the output will be::

    1. one
    2. two
    3. three

*Returns*
  ``Chano instance``

.. _join:

join($glue=', ')
++++++++++++++++

Joins a list with a string, like the
`implode() <http://php.net/manual/en/function.implode.php>`_ function.

For example::

    <?=$item->value->join(" // ")?>

If ``value`` is the array ``array('a', 'b', 'c')``, the output will be
the string ``"a // b // c"``.

Arguments

- ``string $glue``

*Returns*
  ``Chano instance``

.. _makelist:

makelist()
++++++++++

Returns the value turned into an array.

For example::

    <?=$item->value->make_list()?>

If ``value`` is the string ``"Joel"``, the output would be the list
``array('J', 'o', 'e', 'l')``.

*Returns*
  ``Chano instance``


