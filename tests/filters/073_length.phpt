--TEST--
A generated testfile for the "length" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => '1234')));
foreach ($items as $i) echo $i->input->length();
--EXPECT--
4