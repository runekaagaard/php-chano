--TEST--
Testing chaining capabilities.
--FILE--
<?php
include dirname(__FILE__) . '/../DtlIter.php';
$items = array(
    array('a' => 'æfoo\''),
    array('a' => '>bar'),
);
foreach (new DtlIter($items) as $i)
    echo ":" . $i->a->capfirst()->cut('o')->addslashes()->center(10) . ':';
foreach (new DtlIter($items) as $i)
    echo $i->a->length()->add(5)->widthratio(10, 100);
--EXPECT--
:  &Atilde;&brvbar;f\'   ::   &gt;bar   :11090
