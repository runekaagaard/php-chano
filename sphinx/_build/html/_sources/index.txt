.. highlight:: php

Welcome to PHP Django Template Language Iterator's documentation!
=================================================================

The "PHP Django Template Language Iterator" (DTLI) is a standard PHP iterator
class that accepts an array of arrays or objects. It lets you iterate over it 
like you would a normal array. But instead of just being able to access the raw 
value it also provides access to a large range of utility functions that 
manipulates or asks questions about the value in various ways. The functions 
provided are almost directly ported from the Django Template Language.

Basic usage
===========

Instantiation is as simple as::
    
    <?php
    $data = array(
                array('title' => 'first title'), 
                array('title' => 'second title'),
            );
    $items = new (DtlIter($data));
    
Now ``$items`` can be iterated as one normally would::

    <?foreach($items as $i):?>
        <?=$i->title?>
    <?endforeach?> 

Both array and object notation are supported so these two lines are exactly the 
same::

    <?=$i->title?>
    <?=$i['title']?>

Nesting goes arbitrarily deep::

    <?=$i->key1->key2->key3?>
    <?=$i['key1']['key2']['key2']?>

Important to note is that when ``$i->title`` is accessed the returned value is
not a string (e.g. 'first title') but an instance of the DtlIter class itself.
Only when used with ``echo $i->title`` or ``<?=$i->title?>`` the __toString()
method is called and the output will be a string as expected. In other cases
you can access the __toString() value by appending an ``->_`` and if you want 
to access the actual value by appending a ``->v``::

    <?php
    echo type($i->title); // DtlIter.
    echo type($i->title->_); // String.
    echo type($i->titles->v); // Array.

This also means that you can't access keys named "_" and "v" in your arrays as
the magic values will take precedence.

Filter reference
================

Stub.

Navigation
==========

* :ref:`genindex`


