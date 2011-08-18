.. highlight:: php

Chano usage
===========

Iteration
---------

The Chano class can be looped over by instantiating it with either an array,
stdClass or Traversable of arrays or objects.

For example if ``$items`` is::

    array(
        array('title' => 'first title'), 
        array('title' => 'second title'),
    )

or::

    (object)array(
        (object)array('title' => 'first title'),
        (object)array('title' => 'second title'),
    )

or::

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

If you are working on nested datasets you can access its inner parts simply by 
doing::

    <?=$item->key1->key2->key3?>
    <?=$item['key1']['key2']['key3']?>
    
It works this way due to the fact that Chano updates its current value,
everytime a key is accessed on it. This goes on until its ``__toString()`` 
method is called.

Modifying values
----------------

You can modify the current value by calling any of the functions described on
the `functions <http://chano.readthedocs.org/en/latest/functions.html>`_ page.

For example if ``value`` is ``"joel"``::

    <?php
    // Note this is not a typical Chano usecase, but merely an illustrative
    // example. Normally, chaining is the way to go!
    $item->value; // Current value is "joel".
    $item->upper(); // Current value is "JOEL".
    $item->lower(); // Current value is "joel" again.
    echo $item; // "joel" is rendered and the current value is reset.
    echo $item->other_value; // Meaning that you can start over with another
                             // value from the current array or object.

Most of the Chano functions can be chained, so the following is perfectly
legal::

    <?=$item->monkeys->bosses->join(" -- ")->title()->ljust(20)?>

An important thing to note is that if a function is being called when the 
current value is an array or an stdClass, Chano will iterate over said array or 
stdClass and non-recursively modify all scalar values in it. So the following 
would be identical to the example above::

    <?=$item->monkeys->bosses->title()->ljust(20)->join(" -- ")?>

Datatypes
---------

The return from all chainable Chano functions is of course the Chano instance
itself, and not the actual value of the current item. Only when the Chano
instance is being cast to a string - by doing ``<?=$item->title?>`` or
``echo $item->title;`` will a stringification of the current item be rendered.

If you want to access the actual value you can use the ``Chano::v`` property,
and if you want the stringified value you can use the ``Chano::__toString()``
method or its shortcut, the ``Chano::_`` magic property.

For example if ``number_of_monkeys`` is an integer::

    <?php
    <?=get_class($item->number_of_monkeys)?>
    <?=gettype($item->number_of_monkeys->v)?>
    <?=gettype($item->number_of_monkeys->__toString())?>
    <?=gettype($item->number_of_monkeys->_)?>
    
The output would be::

    Chano
    Integer
    String
    String

This also means that you can't access keys named ``"_"`` and ``"v"`` in your 
items as the magic values will take precedence.

Standalone values
-----------------

Should you wish to use a Chano function on a single value (string, int, array, 
etc.) this is possible by using the ``Chano::with()`` shortcut.

For example::

   <?=$item->value->center(14)?>

Used inside a ``foreach`` loop is identical to::

   <?=Chano::with($value)->center(14))?>

This works on functions that works on the base Chano instance too. Simply
don't pass any arguments to the ``set()`` function.

For example::

   <?=$item->now("%B %e, %Y, %R %P")?>

Used inside a ``foreach`` loop is identical to::

   <?=Chano::with()->now("%B %e, %Y, %R %P"))?>

Calling methods on values
-------------------------

Function calls for functions not found on the Chano class is passed on to the
current item which is updated with return of said function.

For example if ``$items`` is a collection of Propel Orm Model instances, which 
each has getter functions::

    <?foreach(new Chano($items) as $item):?>
        <?=$item->getTitle->title()?>
        <?=$item->getBody->safe()?>
    <?endforeach?>

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
