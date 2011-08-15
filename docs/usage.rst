.. highlight:: php

Chano usage
===========

Iteration
---------

The Chano class can be looped over by instantiating it with either a array,
stdClass or Traversable of arrays or objects.

For example if ``$items`` is::

    array(
        array('title' => 'first title'), 
        array('title' => 'second title'),
    )

Or::

    (object)array(
        (object)array('title' => 'first title'),
        (object)array('title' => 'second title'),
    )

Or::

    new ArrayObject(
        array(
            array('title' => 'first title'),
            array('title' => 'second title'),
        )
    )

Then looping over ``$items`` is as simple as::
    
    <?foreach(new Chano($items) as $item):?>
        <?=$item->title?>
    <?endforeach?> 

Accessing values
----------------

Both array and object notation are supported so the two lines below are
identical::

    <?=$item->title?>
    <?=$item['title']?>

If you are working with deeper datasets you can access the more inner parts
simply by doing::

    <?=$item->key1->key2->key3?>
    <?=$item['key1']['key2']['key3']?>

Chaining
--------

Most of the Chano functions can be chained, so the following is perfectly
legal::

    <?=$item->monkeys->bosses->join(" -- ")->title()->ljust(20)?>

Datatypes
---------

The return from all chainable Chano functions is of course the Chano instance
itself, and not the actual value of the current item. Only when the Chano
instance is being cast to a string - by doing ``<?=$item->title?>`` or
``echo $item->title;`` will a stringification of the current item be rendered.

If you want to access the actual value you can use the ``Chano::v`` property,
and if you want the stringified value you can use the ``Chano::__toString()``
method or it shortcut the ``Chano::_`` magic property.

For example if ``number_of_monkeys`` is an integer::

    <?php
    <?=type($item->number_of_monkeys)?>
    <?=type($item->number_of_monkeys->_)?>
    <?=type($item->number_of_monkeys->__toString())?>
    <?=type($item->number_of_monkeys->v)?>

The output would be::

    Chano
    String
    String
    Integer

This also means that you can't access keys named "_" and "v" in your items as
the magic values will take precedence.

Standalone values
-----------------

Should you wish to use a Chano function on a single value (string, int, array,
 etc.) this is possible by using the ``Chano::with()`` shortcut.

For example::

   <?=$item->value->center(14)?>

Used inside a ``foreach`` loop is identical to::

   <?=Chano::with($value)->center(14))?>

This works for functions that works on the base Chano instance too. Simply
don't pass any arguments to the ``set()`` function.

For example::

   <?=$item->now()?>

Used inside a ``foreach`` loop is identical to::

   <?=Chano::with()->now())?>

Calling methods on values
-------------------------

Stub.

Encoding
--------

Chano defaults utf-8 but that can be changed by overwriting the
``Chano::$encoding`` property.

For example::

    <?php
    Chano::$encoding = 'latin1';

i18n
----

Chano is locale and timezone aware.

Supporting other input types
----------------------------

By default Chano only accepts arrays, stdClasses or Traversables as input. If
you want support for others check out the
`iterators.php <https://github.com/runekaagaard/php-chano/blob/master/chano/lib/iterators.php>`_
file for an example of how it is done.
