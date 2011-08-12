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

The only exceptions to this rule is the ``safe()`` method.

Sample usage::

    <?foreach(new Chano($items) as $item)?>
        <?=$item->autoescapeoff()->body?>
        <?=$item->comments?>
        <?=$item->autoescapeon()?>
        <?=$item->title?>
    <?endforeach?>

Returns
~~~~~~~

- ``Chano instance``

.. _autoescapeoff:

autoescapeoff
+++++++++++++

Switches off the default auto-escaping behavior. This means that all
output until the end or until :ref:`autoescapeon` is called will not be
escaped unless ``escape()`` is specifically called.

Sample usage::

    <?foreach(new Chano($items) as $item)?>
        <?=$item->autoescapeoff()->body?>
        <?=$item->comments?>
        <?=$item->autoescapeon()?>
        <?=$item->title?>
    <?endforeach?>

Returns
~~~~~~~

- ``Chano instance``


