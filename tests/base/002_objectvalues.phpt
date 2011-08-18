--TEST--
Testing that filters works on arrays.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = array(
    (object)array('a' => (object)array('b' => 'ab', 'c' => 'GEARcd'), 'b' => 'bb'),
    (object)array('a' => (object)array('b' => 'ef', 'c' => 'GEARgh'), 'b' => 'bb'),
);
foreach (new Chano($items) as $i)
    echo $i->a->capfirst()->b->_
         . $i->a->cut('GEAR')->capfirst()->c->_;
--EXPECT--
AbCdEfGh
