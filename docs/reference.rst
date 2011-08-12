.. highlight:: php

Chano functions
===============

Stub. Describe the different types here.

Flags
_____

Sets one or more boolean values on the Chano class. Chainable.

autoescapeon
++++++++++++

Switches auto-escaping behavior. This is only usefull when the
``autoescapeoff()`` flag has been called as the default behavior of Chano
is to escape all output.

When auto-escaping is in effect, all variable content has HTML escaping
applied to it before placing the result into the output (but after any
filters have been applied).

Sample usage::

    <?php
    foreach(new Chano($items) as $item) {
        $item->autoescapeon();
    }

Arguments
~~~~~~~~~

- ``return`` - object Chano
