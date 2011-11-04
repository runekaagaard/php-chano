--TEST--
Testing that filters works on arrays.
--FILE--
<?php
require dirname(__FILE__) . '/../bootstrap.php';
$items = array(
    (object)array('a' => array('b' => 'ab', 'c' => 'GEARcd', 'd' => array('e' => 'y'))),
    (object)array('a' => (object)array('b' => 'ef', 'c' => 'GEARgh', 'd' => (object)array('e' => 'x'))),
);
foreach (new Chano($items) as $i) {
    echo $i->a->upper()->b . ' -- ';
    echo $i->a->cut('GEAR')->capfirst()->c . ' -- ';
    echo $i->a->upper()->d->e . "\n";
}
--EXPECT--
AB -- CD -- y
EF -- GH -- x
