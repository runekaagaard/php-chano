--TEST--
A generated testfile for the "lower" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'Ë')));
foreach ($items as $i) echo $i->input->lower();
--EXPECT--
ë