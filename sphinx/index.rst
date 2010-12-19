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

Flags
******

Sets one or more boolean values on the DtlIter class. Chainable.

autoescapeon()
--------------

autoescapeoff()
---------------
escape() 
--------

Questions
*********
Conditionally returns a boolean based on value of current item. All questions 
are nonchainable.

emptyor($default) 
-----------------

isfirst()
---------

islast()
--------

haschanged() 
------------

same() 
------

divisibleby($divisor) 
---------------------

Returns
*******

Returns value of current item in various ways. Unchainable.

safe()
------

forceescape() 
-------------

Counters
********

Different methods of counting to/from the current item. Chainable. Works on the 
base instance, ie. you don't have to ask for a key first.

counter()
---------

counter0()
---------- 

revcounter() 
------------

revcounter0() 
-------------

Selectors
*********
One of given arguments are conditionally returned. Chainable. Works on base 
instance too.
 
firstof() 
---------

cycle() 
-------

Other nonchainable commands
***************************

length() 
--------

Filters
*******

Modifies the value of the current item. Chainable.
 
pluralize($a='s', $b=null)
--------------------------

unorderedlist() 
---------------

striptags() 
-----------

vd()
----

now($format) 
------------

widthratio($range_in, $range_out) 
---------------------------------

add($amount)  
------------

addslashes() 
------------

capfirst() 
----------

upper() 
-------

center($width) 
--------------

ljust($width) 
-------------

rjust($width) 
-------------

cut($str) 
---------

date($format) 
-------------

time($format) 
-------------

filesizeformat() 
----------------

yesno($yes=null, $no=null, $maybe=null) 
---------------------------------------

wordwrap($width) 
----------------

wordcount() 
-----------

len() 
-----

stringformat($format) 
---------------------

escapejs() 
----------

first() 
-------

fixampersands() 
---------------

floatformat($ds=null) 
---------------------

getdigit($n) 
------------

lower() 
-------

title() 
-------

urlize() 
--------

urlizetrunc($len) 
-----------------

truncatewords($n) 
-----------------

truncatewordshtml($n) 
---------------------

urlencode() 
-----------

iriencode() 
-----------

slice($str) 
-----------

linenumbers() 
-------------

removetags() 
------------

linebreaks() 
------------

linebreaksbr() 
--------------

join($glue=', ') 
----------------

makelist()
----------

slugify() 
---------

phone2numeric() 
---------------

Navigation
==========

* :ref:`genindex`


