--TEST--
A generated testfile for the "upper" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 'Mixed case input')));
foreach ($items as $i) echo $i->input->upper();
--EXPECT--
MIXED CASE INPUT