--TEST--
A generated testfile for the "rjust" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'test')));
foreach ($items as $i) echo $i->input->rjust(3);
--EXPECT--
test