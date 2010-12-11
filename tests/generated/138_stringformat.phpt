--TEST--
A generated testfile for the "stringformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 1)));
foreach ($items as $i) echo $i->input->stringformat('03d');
--EXPECT--
001