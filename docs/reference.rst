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

date
^^^^

Formats a date according to the given format.

Uses the ``date()`` function (http://php.net/date).

Available format strings:

    ================  ========================================  =====================
    Format character  Description                               Example output
    ================  ========================================  =====================
    a                 ``'am'`` or ``'pm'``.                     ``'am'``
    A                 ``'AM'`` or ``'PM'``.                     ``'AM'``
    b                 Month, textual, 3 letters, lowercase.     ``'jan'``
    B                 Not implemented.
    c                 ISO 8601 format. (Note: unlike others     ``2008-01-02T10:30:00.000123+02:00``,
                      formatters, such as "Z", "O" or "r",      or ``2008-01-02T10:30:00.000123`` if the datetime is naive
                      the "c" formatter will not add timezone
                      offset if value is a `naive datetime`_.)
    d                 Day of the month, 2 digits with           ``'01'`` to ``'31'``
                      leading zeros.
    D                 Day of the week, textual, 3 letters.      ``'Fri'``
    E                 Month, locale specific alternative
                      representation usually used for long
                      date representation.                      ``'listopada'`` (for Polish locale, as opposed to ``'Listopad'``)
    f                 Time, in 12-hour hours and minutes,       ``'1'``, ``'1:30'``
                      with minutes left off if they're zero.
                      Proprietary extension.
    F                 Month, textual, long.                     ``'January'``
    g                 Hour, 12-hour format without leading      ``'1'`` to ``'12'``
                      zeros.
    G                 Hour, 24-hour format without leading      ``'0'`` to ``'23'``
                      zeros.
    h                 Hour, 12-hour format.                     ``'01'`` to ``'12'``
    H                 Hour, 24-hour format.                     ``'00'`` to ``'23'``
    i                 Minutes.                                  ``'00'`` to ``'59'``
    I                 Not implemented.
    j                 Day of the month without leading          ``'1'`` to ``'31'``
                      zeros.
    l                 Day of the week, textual, long.           ``'Friday'``
    L                 Boolean for whether it's a leap year.     ``True`` or ``False``
    m                 Month, 2 digits with leading zeros.       ``'01'`` to ``'12'``
    M                 Month, textual, 3 letters.                ``'Jan'``
    n                 Month without leading zeros.              ``'1'`` to ``'12'``
    N                 Month abbreviation in Associated Press    ``'Jan.'``, ``'Feb.'``, ``'March'``, ``'May'``
                      style. Proprietary extension.
    O                 Difference to Greenwich time in hours.    ``'+0200'``
    P                 Time, in 12-hour hours, minutes and       ``'1 a.m.'``, ``'1:30 p.m.'``, ``'midnight'``, ``'noon'``, ``'12:30 p.m.'``
                      'a.m.'/'p.m.', with minutes left off
                      if they're zero and the special-case
                      strings 'midnight' and 'noon' if
                      appropriate. Proprietary extension.
    r                 RFC 2822 formatted date.                  ``'Thu, 21 Dec 2000 16:01:07 +0200'``
    s                 Seconds, 2 digits with leading zeros.     ``'00'`` to ``'59'``
    S                 English ordinal suffix for day of the     ``'st'``, ``'nd'``, ``'rd'`` or ``'th'``
                      month, 2 characters.
    t                 Number of days in the given month.        ``28`` to ``31``
    T                 Time zone of this machine.                ``'EST'``, ``'MDT'``
    u                 Microseconds.                             ``0`` to ``999999``
    U                 Seconds since the Unix Epoch
                      (January 1 1970 00:00:00 UTC).
    w                 Day of the week, digits without           ``'0'`` (Sunday) to ``'6'`` (Saturday)
                      leading zeros.
    W                 ISO-8601 week number of year, with        ``1``, ``53``
                      weeks starting on Monday.
    y                 Year, 2 digits.                           ``'99'``
    Y                 Year, 4 digits.                           ``'1999'``
    z                 Day of the year.                          ``0`` to ``365``
    Z                 Time zone offset in seconds. The          ``-43200`` to ``43200``
                      offset for timezones west of UTC is
                      always negative, and for those east of
                      UTC is always positive.
    ================  ========================================  =====================

Arguments

- ``string $format``

*Returns*
  ``Chano instance``


