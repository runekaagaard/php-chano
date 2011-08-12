.. highlight:: php

Chano functions
===============

Stub. Describe the different types here.

Flags
_____

Sets one or more boolean values on the Chano class. Chainable.

.. _autoescapeon:

autoescapeon
++++++++++++

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

autoescapeoff
+++++++++++++

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

escape
++++++

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

pluralize
+++++++++

Returns a plural suffix if the value is not 1. By default,
this suffix is ``'s'``.

Example::

    You have <?$item->num_messages?> message<?$item->num_messages->pluralize()?>.

If ``num_messages`` is ``1``, the output will be ``You have 1 message.``
If ``num_messages`` is ``2``  the output will be ``You have 2 messages.``

For words that require a suffix other than ``'s'``, you can provide an
alternate suffix as a parameter to the filter.

Example::

    You have <?$item->num_walruses?> walrus<?$item->num_messages->pluralize("es")?>.

For words that don't pluralize by simple suffix, you can specify both a
singular and plural suffix, separated by a comma.

Example::

    You have <?$item->num_cherries?> cherr<?$item->num_cherries->pluralize("y", "ies")?>.
*Returns*
  ``Chano instance``


