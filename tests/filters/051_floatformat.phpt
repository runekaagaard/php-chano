--TEST--
A generated testfile for the "floatformat" filter.
--FILE--
<?php
include dirname(__FILE__) . '/../../DtlIter.php';
$items = new DtlIter(array(array('input' => 8.2798)));
foreach ($items as $i) echo $i->input->floatformat(3);
--EXPECT--
8.280