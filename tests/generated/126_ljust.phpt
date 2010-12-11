--TEST--
A generated testfile for the "ljust" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'test')));
foreach ($items as $i) echo $i->input->ljust(10);
--EXPECT--
test      