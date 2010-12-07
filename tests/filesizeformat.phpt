--TEST--
Testing chaining capabilities.
--FILE--
<?php
include dirname(__FILE__) . '/../DtlIter.php';
$items = array(
    array('t' => '9823498374'),
    array('t' => '2234832748'),
    array('t' => '123456789'),
);
foreach (new DtlIter($items) as $i) echo $i->t->filesizeformat();
--EXPECT--
9.1 GiB2.1 GiB117.7 MiB
