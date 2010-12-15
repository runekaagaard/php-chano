--TEST--
Testing that counting works.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = array(
    array('d' => 1),
    array('d' => 1),
    array('d' => 1),
    array('d' => 1),
);
foreach (new DtlIter($items) as $i) echo $i->counter();
echo ":";
foreach (new DtlIter($items) as $i) echo $i->counter0();
echo ":";
foreach (new DtlIter($items) as $i) echo $i->revcounter();
echo ":";
foreach (new DtlIter($items) as $i) echo $i->revcounter0();
--EXPECT--
1234:0123:4321:3210
