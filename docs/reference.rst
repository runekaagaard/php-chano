string(26) "#function autoescapeon#Uis"
string(27) "#function autoescapeoff#Uis"
string(20) "#function escape#Uis"
string(23) "#function pluralize#Uis"
string(27) "#function unorderedlist#Uis"
string(23) "#function striptags#Uis"
string(16) "#function vd#Uis"
string(17) "#function now#Uis"
string(24) "#function widthratio#Uis"
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

The list is assumed to be in the proper format. For example, if ``$var``
contains::

    array(
        'States', array(
            'Kansas', array(
                  'Lawrence', 'Topeka'
            ), 'Illinois'
        )
    );

then ``<?=$var->unordered_list()?>`` would render::

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

    <?=$value->striptags()?>

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

    <img src="bar.gif" height="10" width="<?=$value->widthratio(175, 100)?>" />

Above, if ``$value`` is 175 and, the image in the above example will be
88 pixels wide
(because 175/200 = .875; .875 * 100 = 87.5 which is rounded up to 88).

Arguments

- ``numeric $range_in - The maximum before value.``
- ``int/float $max_out - The maximum after value.``

*Returns*
  ``Chano instance``


