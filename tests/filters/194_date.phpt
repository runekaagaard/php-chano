--TEST--
Testing chaining capabilities.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = array(
    array('d' => '9823498374'),
    array('d' => '2234832748'),
);
foreach (new DtlIter($items) as $i)
    echo $i->date('c');
    echo $i->date('c')->cut('1919');
--EXPECT--
1919-09-27T20:41:43+01:001919-09-27T20:41:43+01:00-09-27T20:41:43+01:00
