--TEST--
Testing that items can be a stdClass.
--FILE--
<?php
include dirname(__FILE__) . '/../../chano/Chano.php';
$items = (object)array(
    array('a' => array('b' => 'ab', 'c' => 'GEARcd'), 'b' => 'bb'),
    array('a' => array('b' => 'ef', 'c' => 'GEARgh'), 'b' => 'bb'),
);
foreach (new Chano($items) as $i)
    echo $i->a->capfirst()->b->_
         . $i->a->cut('GEAR')->capfirst()->c->_;
--EXPECT--
AbCdEfGh